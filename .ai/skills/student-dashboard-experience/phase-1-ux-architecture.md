# PHASE 1 — UX Architecture
## Mindlytics Student Learning Operating System

**Status:** Experience architecture only — no UI, no visual design, no components, no code.  
**Governing skill:** `.ai/skills/student-dashboard-experience/`  
**Grounded in:** Current Mindlytics student product (courses advanced/offline/online, exams, assignments, certificates, calendar, notifications, learning paths/patterns, wallet, portfolio, groups; no student goal system or embedded AI coach today).

---

## Student Mindset (Entry Logic)

When a student opens the dashboard, the product must resolve these questions **in this order**:

| # | Question | Architectural answer |
|---|---|---|
| 1 | Why did they open the platform? | Almost always: **reduce uncertainty** — resume learning, meet a deadline, check status, or feel progress. Rarely: “browse widgets.” |
| 2 | What is the first thing they should notice? | **Their Next Learning Act** — one named activity with context (course/path + why now). Not a wall of stats. |
| 3 | What should they do immediately? | **One primary action:** Continue / Start mission / Submit due item / Join live session — whichever has highest learning urgency. |
| 4 | What should motivate them? | **Competence + continuity:** visible momentum (streak/journey position), calm pride in recent mastery, a reachable daily closure — never guilt or badge spam. |
| 5 | What information is secondary? | Catalog browsing, wallet, portfolio, full course lists, historical certificates, referral, settings. Available, not competing. |
| 6 | What can remain hidden? | Raw enrollment tables, invoice history, system meta, unused product modules, advanced analytics detail, AI reasoning dumps, empty feature shells. |
| 7 | Which actions deserve visual priority? | Resume learning · Complete due assessment · Join live · Accept AI-guided revision · Close today’s mission. |
| 8 | What should AI provide proactively? | Next-best action, weak-topic alert + practice path, exam readiness, catch-up plan, burnout protection — **insight → why → one action**. Never a chatbot as the product. |

**North star feeling:** *“I entered my personal learning space.”* — not an admin panel.

**Five governing questions every zone must answer (exactly one):**
1. What should I do now?
2. What have I achieved?
3. What is my next goal?
4. Where am I progressing?
5. How can AI help me?

---

## 1. User Journey — First 60 Seconds

### 0–3s · Orientation (emotion: relief / recognition)
- Student lands in a **personal learning space**, not a control panel.
- Instant recognition: *this is about my learning today*.
- Cognitive load is deliberately low: one dominant focus, minimal competing claims.

### 3–8s · Clarity (emotion: focus)
- They notice **The Stage** (Next Learning Act):
  - What it is (lesson / pattern / exam / assignment / live session)
  - Which journey it belongs to
  - Why it is next (deadline, unfinished progress, AI recommendation, or daily mission)
- Secondary peripheral signal only: streak continuity or “mission still open” — enough to motivate, not enough to distract.

### 8–15s · Decision (emotion: agency)
- One obvious primary path: **ابدأ / تابع** (Continue).
- If a hard deadline exists within the urgency window (e.g. exam/assignment soon), the Stage may elevate that instead of casual resume — still **one** primary act, not two equals.
- Soft alternate: “Not now — show catch-up options” (does not split attention equally).

### 15–30s · Commitment (emotion: momentum)
- Student commits to Continue → deep-link into the **real next activity** (lesson player, exam flow, assignment submit, live join URL, learning pattern attempt).
- If they stay on the dashboard, they may skim **Journey continuity** (past → present → next) to confirm they are not lost.
- Emotion target: *I know where I am and what happens if I act.*

### 30–45s · Reinforcement (emotion: pride / curiosity — optional layer)
- Only if they have not left yet:
  - A short **achievement whisper** (recent mastery / milestone) OR
  - One **AI coach sentence** with a single action (weak topic → revise; exam readiness → practice set)
- No chart walls. No equal KPI strip fighting the Stage.

### 45–60s · Branch or exit (emotion: calm confidence)
- Ideal outcome: student has **left the dashboard into learning**.
- If browsing: they may open Planning (what’s due this week), Journey detail, or Resources — all clearly secondary.
- Empty / blocked states (e.g. scholarship pending activation) replace the Stage with **one recovery action**, never a dead stats page.

### Decision tree (first minute)

```
Open dashboard
  ├─ Blocking state (pending activation / no enrollment)?
  │    └─ Single recovery path → stop
  ├─ Time-critical obligation within urgency window?
  │    └─ Stage = that obligation → primary CTA
  ├─ Unfinished learning session exists?
  │    └─ Stage = resume exact next activity → primary CTA
  ├─ Daily mission incomplete?
  │    └─ Stage = mission → primary CTA
  └─ Else
       └─ Stage = AI/journey next-best learning act → primary CTA
```

**Success metric for the first 60 seconds:** majority of sessions either enter learning or resolve a due obligation — not “explored eight widgets.”

---

## 2. Information Hierarchy

Ranked by importance for the **home learning OS** (not the whole portal).

| Rank | Information | Answers | Why |
|---|---|---|---|
| 1 | **Next Learning Act** (named activity + CTA) | What should I do now? | Removes uncertainty; drives completion and retention. |
| 2 | **Urgency context** (due soon / live now / mission open) | What should I do now? | Prevents missed deadlines without creating a second homepage. |
| 3 | **Journey position** (past → present → next on active path/course) | Where am I progressing? | Turns course lists into a story of growth. |
| 4 | **Daily closure signal** (mission / daily goal state) | What is my next goal? | Builds ethical habit loop: cue → action → closure. |
| 5 | **Momentum signal** (streak continuity — calm) | Motivation | Supports return ritual; must stay peripheral to Stage. |
| 6 | **AI proactive insight** (one insight + one action) | How can AI help me? | Differentiator; only valuable if actionable and calm. |
| 7 | **Mastery / recent achievement** (sparse) | What have I achieved? | Competence > vanity metrics. |
| 8 | **Week ahead** (exams, assignments, live sessions) | Planning | Prevents surprise; secondary to acting now. |
| 9 | **Active learning inventory** (courses/paths summary) | Orientation | Needed for multi-enrollment students; not the hero. |
| 10 | **Notifications digest** (unread, high-signal only) | Situational awareness | Useful; easy to overload — keep compressed. |
| 11 | **Certificates / achievements archive** | Pride (asynchronous) | Meaningful but not entry-critical. |
| 12 | **Groups / community** | Relatedness (light) | Supportive, not the default home purpose. |
| 13 | **Catalog / discover more** | Exploration | Growth of catalog ≠ progress of learner. |
| 14 | **Wallet / invoices / orders** | Admin necessity | Account hygiene; never compete with learning. |
| 15 | **Portfolio / referrals / settings** | Identity / account | Belong elsewhere; linked, not staged. |

**Hard rule:** If an item does not help answer one of the five governing questions, it does not earn a home zone.

---

## 3. Dashboard Zones (Logical Experience Only)

Organize experience — not layout chrome.

### A. Learning Stage Zone (Critical)
- **Purpose:** Decide and act.
- **Contains:** Next Learning Act, primary CTA, urgency reason, deep link target.
- **Question:** What should I do now?
- **Emotion:** Focus → Momentum

### B. Motivation Continuity Zone (Important, peripheral to Stage)
- **Purpose:** Habit and energy without noise.
- **Contains:** Daily mission/goal state, learning streak (calm), optional weekly challenge invitation.
- **Questions:** What is my next goal? (+ light motivation)
- **Emotion:** Motivation, Pride (quiet)

### C. Journey Zone (Important)
- **Purpose:** Narrative continuity of growth.
- **Contains:** Active path/course position, present stage, next step, recovery branch if behind.
- **Question:** Where am I progressing?
- **Emotion:** Progress, Confidence
- **Note:** Course catalog grids are **not** a journey.

### D. AI Coach Zone (Important, embedded — not a chat product)
- **Purpose:** Proactive intelligence.
- **Contains:** One insight (weak topic / readiness / catch-up / burnout care) + why + single action (revise, quiz, plan, rest).
- **Question:** How can AI help me?
- **Emotion:** Curiosity, Safety, Clarity

### E. Insights & Mastery Zone (Important, deeper)
- **Purpose:** Competence evidence.
- **Contains:** Sparse mastery signals, performance pattern (interpreted), recent milestone.
- **Question:** What have I achieved?
- **Emotion:** Pride, Competence

### F. Planning Zone (Important, secondary)
- **Purpose:** Time awareness.
- **Contains:** Upcoming exams, assignments, live sessions, study plan suggestions for the week.
- **Question:** What is my next goal? (time-bound)
- **Emotion:** Calm control

### G. Achievements Zone (Optional on home; full archive elsewhere)
- **Purpose:** Long-term identity.
- **Contains:** Certificates entry, rare milestones, career-growth arc teaser.
- **Question:** What have I achieved?
- **Emotion:** Pride (asynchronous)

### H. Resources & Portal Zone (Optional / navigational)
- **Purpose:** Access without distraction.
- **Contains:** My courses index, offline/online portals, materials download entry, groups, portfolio, wallet — as destinations, not equal home modules.
- **Question:** none on home (navigation only)
- **Emotion:** Trust / control

### I. System / Account Zone (Hidden from learning attention)
- Settings, invoices detail, referral machinery, raw admin-like tables.

**Zone relationship (experience flow):**  
Stage → (commit to learning)  
Else skim Journey → AI → Planning → Mastery  
Resources only when searching or managing.

---

## 4. Student Goals (Complete Inventory)

Goals the Learning OS must be able to recognize and route. Grouped by intent.

### Learning continuity
- Continue last lesson / lecture
- Continue learning path stage
- Complete today’s mission / daily goal
- Resume unfinished learning pattern (quiz, flashcards, code challenge, live coding, etc.)
- Watch next video / finish in-video questions for points
- Catch up after falling behind

### Assessment & proof
- Start / finish quiz or exam
- Review exam results
- Submit assignment / task
- Prepare for upcoming exam
- Earn / view certificate
- View achievements / milestones

### Live & scheduled learning
- Join live online session (`meeting_url`)
- Check today’s / week’s schedule
- Book offline / online group or seat (where applicable)
- Attend scheduled offline lecture (awareness + prep)

### Mastery & revision
- Review weak topics
- Practice with AI-generated quiz / flashcards
- Review notes / lesson summary
- Personalized revision session
- Improve mastery level on a skill node

### Planning & self-regulation
- Set / close daily goal
- Follow weekly challenge
- View study plan
- Protect energy (respond to burnout guidance)
- Check learning prediction / pace reality

### Discovery & enrollment
- Browse catalog (years / subjects / courses)
- Enroll / activate pending scholarship registration
- Open offline course portal
- Open online course portal

### Social / cohort (light)
- Open group space
- Submit group assignment
- Check community challenge (where offered)

### Evidence & career identity
- Update portfolio project
- Share / review certificates
- Track career-growth narrative

### Account & logistics (valid goals, low home priority)
- Check notifications
- Pay invoice / view wallet / orders
- Download material / resources
- Edit profile / settings
- Use referral

**Routing principle:** The Stage picks **one** goal as primary using urgency + unfinished work + mission + AI next-best — all other goals remain reachable without equal priority.

---

## 5. Priority Matrix

### Critical (must be resolvable in the first viewport of attention)
| Element | Why |
|---|---|
| Next Learning Act + primary CTA | Core job: act now |
| Blocking states (pending activation, no access) | Without resolution, learning cannot start |
| Time-critical due item / live-now session | Missing these destroys trust |
| Exact deep link into real activity | “Continue” that dumps to a course index is a broken promise |

### Important (visible without hunting; never equal to Critical)
| Element | Why |
|---|---|
| Journey past → present → next | Prevents “lost in courses” |
| Daily mission / goal state | Habit loop |
| Streak continuity (calm) | Return motivation |
| AI insight + one action | Proactive help |
| Week-ahead deadlines (compressed) | Planning without panic |
| Sparse mastery / recent win | Competence signal |
| High-signal notifications count/entry | Situational awareness |

### Optional (available; not home-competitive)
| Element | Why |
|---|---|
| Full my-courses grid | Inventory, not narrative |
| Certificates archive | Pride on demand |
| Achievements gallery | Optional identity |
| Groups / community | Relatedness light |
| Weekly challenge detail | Intensity optional |
| Learning heatmap detail | Quiet analytics |
| Portfolio entry | Career identity side-door |
| Catalog discover | Growth exploration |
| Materials download hubs | Resource task |

### Hidden (exist in product; not part of learning attention model)
| Element | Why |
|---|---|
| Wallet / invoices / order tables | Admin necessity |
| Referral machinery | Growth tool, not learning OS |
| Settings / profile editors | Account |
| Raw KPI soup (counts without interpretation) | Cognitive noise; current anti-pattern to retire from hero |
| AI chain-of-thought / raw model text | Undermines trust and clarity |
| Empty feature shells | Fake complexity |
| Instructor/admin metaphors | Breaks “personal learning space” |

---

## 6. Cognitive Load Analysis

### What currently overloads (product reality → architectural debt)
- Equal **stat cards** (active items, completed courses, % progress, pending orders) compete with learning intent.
- Mixed concerns on one home: learning + commerce (orders) + archives (certificates) without a single primary act.
- “Continue” that is not tied to an exact next activity increases decision friction.
- Large inventories without journey narrative force scanning instead of deciding.
- Absence of proactive AI means students must self-diagnose what to do — high executive load.

### Load-reduction rules
1. **One primary action** in Stage — never two equal CTAs.
2. **One idea per zone** — mapped to exactly one governing question.
3. **Interpret, don’t dump** — numbers need a sentence of meaning or they leave the home model.
4. **Progressive disclosure** — archives, wallet, full analytics behind intentional navigation.
5. **Compression** — week-ahead as a short agenda, not a calendar dump.
6. **AI brevity** — insight → why → action; dismissible; no chat wall.
7. **Motivation without noise** — streak/mission peripheral; no childish badge walls; no fake urgency.
8. **Recovery over shame** — behind schedule → calm catch-up plan, not red punishment theater.
9. **Scholarship / constrained portals** — fewer zones, same Stage logic; do not show empty modules.
10. **Success = leaving into learning** — the best dashboard is briefly seen.

### Explicit removals from the home attention model
- Pending orders as a peer to learning progress
- Multi-KPI strip as the compositional center
- Undifferentiated course card grids as the main progress story
- Chatbot-first AI entry
- Decorative metrics with no pedagogical meaning

---

## 7. UX Improvements vs Tutor LMS / Moodle / Udemy / Coursera

Principles only — **no copying** of those products’ layouts or information architecture.

| Competitor pattern (typical) | Mindlytics Learning OS improvement |
|---|---|
| **Tutor LMS / Moodle:** course list + widgets + admin residue | Replace inventory-first homes with a **Stage + Journey narrative**. Student feels a personal OS, not an ERP skin. |
| **Moodle:** everything visible, high chrome, high cognitive tax | Enforce **priority matrix + one question per zone**. Hidden does not mean deleted — it means not competing. |
| **Udemy:** continue + recommendations optimized for catalog consumption | Optimize for **commitment to the next pedagogical act** and mastery continuity, not endless browsing. Recommendations serve weak topics and readiness, not impulse enroll. |
| **Coursera:** structured programs but often impersonal dashboards | Make path position **emotionally continuous** (past → present → next) with Arabic-first learning voice and calm coaching. |
| **All four:** progress as percentages / checklists | Elevate **competence narrative** (mastery, milestones, journey) over vanity % soup. |
| **All four:** help as FAQ, forum, or bolted chatbot | Embed **proactive AI coach** that proposes the next act with evidence — never AI-as-decoration. |
| **LMS family:** deadlines scattered across modules | Unify urgency into Stage decision logic + compressed Planning zone. |
| **Consumer course marketplaces:** streak/gamification as cartoons or absent | Premium, adult motivation: streak, mission, mastery — ethical, sparse, learning-linked. |

### Dramatic difference in one sentence
Mindlytics wins when opening the product answers *“What do I do now, and why does it matter to my growth?”* in under ten seconds — then gets out of the way.

---

## Architecture Summary (Non-Visual)

```
LEARNING OS HOME
│
├─ CRITICAL: Learning Stage (Next Act + CTA + urgency)
├─ IMPORTANT: Motivation Continuity (mission / streak)
├─ IMPORTANT: Journey (past → present → next + recovery)
├─ IMPORTANT: AI Coach (insight → why → action)
├─ IMPORTANT: Planning (compressed week obligations)
├─ IMPORTANT: Insights & Mastery (sparse competence)
├─ OPTIONAL: Achievements / Resources navigation
└─ HIDDEN: Account, commerce detail, raw admin data
```

**Primary behavioral loop:**  
Cue (Stage) → Action (real activity) → Visible progress (Journey/Mastery) → Reason to return (Mission/Streak) → optional AI refinement tomorrow.

**Ethical boundaries:** no fake urgency, no guilt punishment, no dark patterns, no childish manipulation, burnout care over grind.

---

## Phase 1 Exit Criteria

Phase 1 is complete when stakeholders agree that:

1. The first 60 seconds journey is the product truth.
2. Zones and hierarchy are locked as experience law.
3. Priority matrix governs what may appear on home.
4. Cognitive load rules forbid KPI-centered LMS clones.
5. AI is defined as proactive coaching, not a chatbot shell.
6. No UI, styling, or components have been produced yet.

**Next phase (not started):** Experience → information design / content model — still before visual UI.
