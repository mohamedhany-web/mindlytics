<?php

namespace App\Services\Hr;

use App\Models\HrApplicationSkill;
use App\Models\HrJobApplication;
use App\Models\HrJobPosting;
use Illuminate\Support\Facades\Log;

class AtsScoringService
{
    public function __construct(
        private readonly CVParserService $cvParser,
    ) {}

    public function processApplication(HrJobApplication $application): HrJobApplication
    {
        $application->loadMissing(['job', 'cvFile', 'skills']);

        $job = $application->job;
        if (! $job) {
            return $application;
        }

        $parsed = $this->parseApplicationCv($application, $job);

        $this->persistParsedData($application, $parsed);
        $this->scoreAndSave($application, $job);

        return $application->fresh(['skills', 'job']);
    }

    /**
     * @return array<string, mixed>
     */
    public function scoreApplication(HrJobApplication $application, HrJobPosting $job): array
    {
        $requiredSkills = $job->normalizedRequiredSkills();
        $candidateSkills = $application->normalizedParsedSkills();

        $skillsScore = $this->calculateSkillsScore($requiredSkills, $candidateSkills);
        $experienceScore = $this->calculateExperienceScore(
            $job->required_experience,
            $application->parsed_experience_years
        );
        $educationScore = $this->calculateEducationScore(
            $job->required_education,
            $application->parsed_education
        );

        $weights = config('hr.scoring_weights', [
            'skills' => 0.60,
            'experience' => 0.30,
            'education' => 0.10,
        ]);

        $autoScore = round(
            ($skillsScore * ($weights['skills'] ?? 0.60))
            + ($experienceScore * ($weights['experience'] ?? 0.30))
            + ($educationScore * ($weights['education'] ?? 0.10)),
            2
        );

        $matchedSkills = $this->matchedSkills($requiredSkills, $candidateSkills);

        return [
            'skills_score' => round($skillsScore, 2),
            'experience_score' => round($experienceScore, 2),
            'education_score' => round($educationScore, 2),
            'auto_score' => $autoScore,
            'breakdown' => [
                'required_skills' => $requiredSkills,
                'candidate_skills' => $candidateSkills,
                'matched_skills' => $matchedSkills,
                'required_experience' => $job->required_experience,
                'candidate_experience' => $application->parsed_experience_years,
                'required_education' => $job->required_education,
                'candidate_education' => $application->parsed_education,
                'weights' => $weights,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseApplicationCv(HrJobApplication $application, HrJobPosting $job): array
    {
        $extraHints = $job->normalizedRequiredSkills();
        $parsed = [
            'email' => $application->email,
            'phone' => $application->phone,
            'skills' => [],
            'education' => null,
            'experience_years' => null,
        ];

        $cvFile = $application->cvFile;
        if ($cvFile) {
            try {
                $fromCv = $this->cvParser->parseFromFile($cvFile, $extraHints);
                $parsed['email'] = $parsed['email'] ?: ($fromCv['email'] ?? null);
                $parsed['phone'] = $parsed['phone'] ?: ($fromCv['phone'] ?? null);
                $parsed['skills'] = $fromCv['skills'] ?? [];
                $parsed['education'] = $fromCv['education'] ?? null;
                $parsed['experience_years'] = $fromCv['experience_years'] ?? null;
            } catch (\Throwable $e) {
                Log::warning('HR CV parse failed', [
                    'application_id' => $application->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if ($application->cover_letter) {
            $fromLetter = $this->cvParser->parseText($application->cover_letter, $extraHints);
            $parsed['skills'] = array_values(array_unique(array_merge(
                $parsed['skills'],
                $fromLetter['skills'] ?? []
            )));
            $parsed['education'] = $parsed['education'] ?: ($fromLetter['education'] ?? null);
            $parsed['experience_years'] = $parsed['experience_years'] ?? ($fromLetter['experience_years'] ?? null);
        }

        return $parsed;
    }

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function persistParsedData(HrJobApplication $application, array $parsed): void
    {
        $updates = [];

        if (! $application->email && ! empty($parsed['email'])) {
            $updates['email'] = $parsed['email'];
        }
        if (! $application->phone && ! empty($parsed['phone'])) {
            $updates['phone'] = $parsed['phone'];
        }

        $updates['parsed_skills'] = array_values($parsed['skills'] ?? []);
        $updates['parsed_education'] = $parsed['education'] ?? null;
        $updates['parsed_experience_years'] = $parsed['experience_years'] ?? null;

        if ($updates !== []) {
            $application->update($updates);
        }

        $application->skills()->delete();
        foreach ($updates['parsed_skills'] as $skill) {
            HrApplicationSkill::create([
                'job_application_id' => $application->id,
                'skill_name' => (string) $skill,
            ]);
        }
    }

    private function scoreAndSave(HrJobApplication $application, HrJobPosting $job): void
    {
        $result = $this->scoreApplication($application, $job);

        $application->update([
            'skills_score' => $result['skills_score'],
            'experience_score' => $result['experience_score'],
            'education_score' => $result['education_score'],
            'auto_score' => $result['auto_score'],
            'scoring_notes' => $result['breakdown'],
            'scored_at' => now(),
        ]);
    }

    /**
     * @param  list<string>  $required
     * @param  list<string>  $candidate
     */
    public function calculateSkillsScore(array $required, array $candidate): float
    {
        if ($required === []) {
            return $candidate !== [] ? 100.0 : 0.0;
        }

        $matched = count($this->matchedSkills($required, $candidate));

        return round(($matched / count($required)) * 100, 2);
    }

    public function calculateExperienceScore(?int $requiredYears, null|float|string $candidateYears): float
    {
        if ($requiredYears === null || $requiredYears <= 0) {
            return $candidateYears !== null ? 100.0 : 0.0;
        }

        $candidate = (float) ($candidateYears ?? 0);

        if ($candidate >= $requiredYears) {
            return 100.0;
        }

        return round(($candidate / $requiredYears) * 100, 2);
    }

    public function calculateEducationScore(?string $required, ?string $candidate): float
    {
        if ($required === null || $required === '') {
            return $candidate !== null ? 100.0 : 0.0;
        }

        if ($candidate === null || $candidate === '') {
            return 0.0;
        }

        $matrix = config('hr.education_score_matrix', []);
        if (isset($matrix[$required][$candidate])) {
            return (float) $matrix[$required][$candidate];
        }

        $levels = config('hr.education_levels', []);
        $reqRank = (int) ($levels[$required]['rank'] ?? 0);
        $candRank = (int) ($levels[$candidate]['rank'] ?? 0);

        if ($reqRank <= 0 || $candRank <= 0) {
            return 0.0;
        }

        if ($candRank >= $reqRank) {
            return 100.0;
        }

        return max(10.0, round(($candRank / $reqRank) * 100, 2));
    }

    /**
     * @param  list<string>  $required
     * @param  list<string>  $candidate
     * @return list<string>
     */
    public function matchedSkills(array $required, array $candidate): array
    {
        $normalizedCandidate = array_map(fn ($s) => mb_strtolower(trim($s)), $candidate);
        $matched = [];

        foreach ($required as $skill) {
            $needle = mb_strtolower(trim($skill));
            foreach ($normalizedCandidate as $i => $cand) {
                if ($cand === $needle || str_contains($cand, $needle) || str_contains($needle, $cand)) {
                    $matched[] = $candidate[$i] ?? $skill;
                    break;
                }
            }
        }

        return array_values(array_unique($matched));
    }
}
