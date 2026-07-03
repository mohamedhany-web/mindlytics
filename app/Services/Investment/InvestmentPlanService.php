<?php

namespace App\Services\Investment;

use App\Models\InvestmentPlan;
use Illuminate\Support\Arr;

class InvestmentPlanService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?int $userId = null): InvestmentPlan
    {
        $data['slug'] = InvestmentPlan::generateSlug((string) $data['title']);
        $data['created_by'] = $userId;
        $data['process_steps'] = $this->normalizeSteps($data['process_steps'] ?? null);

        return InvestmentPlan::create(Arr::only($data, $this->fillableKeys()));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(InvestmentPlan $plan, array $data): InvestmentPlan
    {
        if (! empty($data['title']) && $data['title'] !== $plan->title) {
            $data['slug'] = InvestmentPlan::generateSlug((string) $data['title'], $plan->id);
        }

        if (array_key_exists('process_steps', $data)) {
            $data['process_steps'] = $this->normalizeSteps($data['process_steps']);
        }

        $plan->update(Arr::only($data, $this->fillableKeys()));

        return $plan->fresh();
    }

    /**
     * @param  mixed  $steps
     * @return array<int, array{title: string, description: string}>|null
     */
    private function normalizeSteps(mixed $steps): ?array
    {
        if (! is_array($steps)) {
            return null;
        }

        $normalized = [];
        foreach ($steps as $step) {
            if (! is_array($step)) {
                continue;
            }
            $title = trim((string) ($step['title'] ?? ''));
            $description = trim((string) ($step['description'] ?? ''));
            if ($title === '' && $description === '') {
                continue;
            }
            $normalized[] = ['title' => $title, 'description' => $description];
        }

        return $normalized === [] ? null : $normalized;
    }

    /**
     * @return list<string>
     */
    private function fillableKeys(): array
    {
        return [
            'title', 'slug', 'short_description', 'description', 'plan_type',
            'min_investment', 'max_investment', 'target_amount', 'currency',
            'duration_months', 'expected_return_min', 'expected_return_max',
            'return_model', 'risk_level', 'eligibility_criteria', 'benefits',
            'terms_summary', 'legal_notes', 'process_steps', 'is_active',
            'is_featured', 'sort_order', 'starts_at', 'ends_at', 'created_by',
        ];
    }
}
