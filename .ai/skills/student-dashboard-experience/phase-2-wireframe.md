# PHASE 2 — Structural Wireframe
## Mindlytics Student Learning OS (Blueprint Only)

**Status:** Information architecture + layout structure only.  
**Source of truth:** `phase-1-ux-architecture.md`  
**Governing skill:** `.ai/skills/student-dashboard-experience/`

### Constraints (enforced)

- No final UI
- No branding
- No color except grayscale structure
- No illustrations
- No icons except `[i]` placeholders
- No shadows, gradients, or visual style
- No huge heroes, equal grids, KPI boxes, Bootstrap/dashboard templates

### Blueprint language

| Symbol | Meaning |
|---|---|
| `████` | Highest attention density (Critical) |
| `▓▓▓▓` | Strong secondary attention (Important) |
| `░░░░` | Low attention / optional |
| `····` | Whitespace / breathing room |
| `[label]` | Content label only |
| `(CTA)` | Action affordance (structure, not style) |
| `---` | Group separator / rhythm break |
| `→` / `←` | Reading direction notes (RTL-primary product) |

**Reading model:** RTL-first. Wireframes below are drawn LTR for file readability; **placement logic is RTL** (primary Stage starts at the inline-start / right in Arabic UI).

---

## A. Page Skeleton (Desktop)

Compact editorial shell — not a dashboard template.

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ TOP BAR (slim · 1 line · low density)                                        │
│ [Space name: Learning home]     [notif digest] [account]                     │
│ ············································································ │
├────────────┬─────────────────────────────────────────────────────────────────┤
│            │                                                                 │
│ NAV RAIL   │  MAIN COLUMN (editorial · asymmetrical · high organization)     │
│ (narrow)   │                                                                 │
│            │  ┌─ BAND 0 · CONTEXT STRIP (optional · 1 line) ───────────────┐ │
│ [Home]     │  │ [greeting fragment] · [date context] · [streak: n]         │ │
│ [Learn]    │  └────────────────────────────────────────────────────────────┘ │
│ [Plan]     │  ······························································ │
│ [Achieve]  │                                                                 │
│ [More ▾]   │  ┌─ ZONE A · LEARNING STAGE (CRITICAL · densest) ████████────┐ │
│            │  │                                                          │ │
│ ········   │  │  [ZONE A · What should I do now?]                         │ │
│            │  │  [Next Learning Act — title]                              │ │
│            │  │  [Context: course/path · why now]                         │ │
│            │  │  [Urgency note — if any]                                  │ │
│            │  │                                                          │ │
│            │  │  (PRIMARY CTA: Continue / Start / Submit / Join)          │ │
│            │  │  (secondary text link: Not now · other options)           │ │
│            │  │                                                          │ │
│            │  └──────────────────────────────────────────────────────────┘ │
│            │  ······························································ │
│            │                                                                 │
│            │  ┌─ ZONE B · MOTIVATION CONTINUITY ▓▓──┐  ┌─ ZONE D · AI ▓▓──┐ │
│            │  │ [Daily mission state]               │  │ [AI insight]     │ │
│            │  │ [Goal: open / closed]               │  │ [Why — 1 line]   │ │
│            │  │ [Streak continuity — peripheral]    │  │ (AI ACTION CTA)  │ │
│            │  │ Q: What is my next goal?            │  │ Q: How AI helps? │ │
│            │  └─────────────────────────────────────┘  └──────────────────┘ │
│            │  ······························································ │
│            │                                                                 │
│            │  ┌─ ZONE C · JOURNEY (IMPORTANT · mid density) ▓▓▓▓▓▓▓▓▓─────┐ │
│            │  │ [Where am I progressing?]                                 │ │
│            │  │                                                           │ │
│            │  │  [Past] ── [Present ●] ── [Next]                          │ │
│            │  │   label      label         label                          │ │
│            │  │                                                           │ │
│            │  │  [Recovery note — only if behind]                         │ │
│            │  └───────────────────────────────────────────────────────────┘ │
│            │  ······························································ │
│            │                                                                 │
│            │  ┌─ ZONE F · PLANNING ▓▓─────────────┐  ┌─ ZONE E · MASTERY ▓┐ │
│            │  │ [Week ahead — compressed]         │  │ [Recent win]       │ │
│            │  │ · [item due]                      │  │ [Mastery whisper]  │ │
│            │  │ · [item due]                      │  │ Q: Achieved?       │ │
│            │  │ · [live session]                  │  └────────────────────┘ │
│            │  │ Q: time-bound next goal           │                         │
│            │  └───────────────────────────────────┘                         │
│            │  ······························································ │
│            │                                                                 │
│            │  ┌─ ZONE H · RESOURCES / PORTAL (OPTIONAL · low) ░░░░░───────┐ │
│            │  │ [My courses] [Materials] [Groups] [Certificates] …        │ │
│            │  │ (text links / compact list — not equal cards)             │ │
│            │  └───────────────────────────────────────────────────────────┘ │
│            │                                                                 │
└────────────┴─────────────────────────────────────────────────────────────────┘
```

**Whitespace rule:** Vertical rhythm uses repeating `····` bands between zones (compact gaps, not huge empty hero voids). Stage is larger than siblings but **not** a full-viewport hero.

---

## B. First Viewport Focus Map (Above the Fold)

What must be scannable without scrolling on typical laptop height:

```
┌─────────────────────────────────────────────┐
│ context strip (thin)                        │  ← 5% height
├─────────────────────────────────────────────┤
│                                             │
│           ZONE A · STAGE                    │  ← ~40–45% height
│           (single primary CTA)              │
│                                             │
├──────────────────────┬──────────────────────┤
│ ZONE B · Mission     │ ZONE D · AI Coach    │  ← ~25–30% height
│ + streak (quiet)     │ insight + 1 action   │
└──────────────────────┴──────────────────────┘
        Journey may peek (rail edge)              ← optional 10%
```

Below fold: Journey full → Planning + Mastery → Resources.

---

## C. Zone Block Specs (Labels Only)

### ZONE A — Learning Stage · CRITICAL · `████`

```
┌──────────────────────────────────────────────┐
│ LABEL: Learning Stage                        │
│ QUESTION: What should I do now?              │
│                                              │
│ [Activity type]                              │
│ [Activity title — strongest text weight]     │
│ [Parent journey / course name]               │
│ [Why now: deadline | resume | mission | AI]  │
│                                              │
│ (PRIMARY CTA)                                │
│ (text: secondary alternate)                  │
└──────────────────────────────────────────────┘
```

**Density:** High information, low chrome. One action cluster only.  
**Size:** Widest block; taller than B/D; shorter than classic heroes.

### ZONE B — Motivation Continuity · IMPORTANT · `▓▓`

```
┌─────────────────────────────┐
│ LABEL: Today                │
│ QUESTION: Next goal?        │
│ [Daily mission title]       │
│ [State: open | done]        │
│ [Streak: n days]            │
└─────────────────────────────┘
```

**Density:** Low–mid. No second CTA competing with Stage unless mission IS the Stage content.

### ZONE D — AI Coach · IMPORTANT · `▓▓`

```
┌─────────────────────────────┐
│ LABEL: AI Coach             │
│ QUESTION: How AI helps?     │
│ [Insight — 1 sentence]      │
│ [Why — evidence fragment]   │
│ (single AI action)          │
└─────────────────────────────┘
```

**Density:** Mid. Never a chat thread. Never taller than Stage.

### ZONE C — Journey · IMPORTANT · `▓▓▓`

```
┌──────────────────────────────────────────────┐
│ LABEL: Journey                               │
│ QUESTION: Where am I progressing?            │
│                                              │
│ [Past step] — [Present step ●] — [Next step] │
│                                              │
│ [Optional recovery: catch-up plan link]      │
└──────────────────────────────────────────────┘
```

**Density:** Mid. Horizontal narrative (RTL: present emphasized near start of reading). Not a course card grid.

### ZONE F — Planning · IMPORTANT · `▓▓`

```
┌─────────────────────────────┐
│ LABEL: This week            │
│ QUESTION: Next goal (time)  │
│ [Due item 1]                │
│ [Due item 2]                │
│ [Live session]              │
│ (link: full calendar)       │
└─────────────────────────────┘
```

**Density:** List compression. Max ~3–5 visible rows.

### ZONE E — Insights & Mastery · IMPORTANT · `▓`

```
┌─────────────────────────────┐
│ LABEL: Mastery              │
│ QUESTION: Achieved?         │
│ [Recent milestone / skill]  │
│ [One interpreted signal]    │
└─────────────────────────────┘
```

**Density:** Sparse. No chart wall. No KPI row of four equals.

### ZONE H — Resources · OPTIONAL · `░░`

```
┌──────────────────────────────────────────────┐
│ LABEL: Library & portal                      │
│ [Courses] · [Materials] · [Groups] · [Certs] │
└──────────────────────────────────────────────┘
```

**Density:** Navigation strip. Not a card marketplace.

### HIDDEN (not drawn on home wireframe)

Wallet · Invoices · Orders tables · Referrals · Settings editors · Raw KPI soup · AI dumps

---

## D. Asymmetry Map (Anti-Equal-Grid)

```
     WIDTH WEIGHT (conceptual)

Stage     ████████████████████████████
Mission   ██████████████
AI        ██████████████
Journey   ████████████████████████████
Planning  ████████████████
Mastery   ████████
Resources ████████████████████████████ (thin height)
```

Mission and AI share a row but remain **secondary** in height/weight to Stage.  
Planning and Mastery share a row with **unequal** width (Planning wider — time list needs scan room; Mastery stays whisper-sized).

---

## E. Mobile Wireframe (Stacked · RTL content order)

```
┌──────────────────────────┐
│ [Learning home] [notif]  │
├──────────────────────────┤
│ [context · streak]       │
├──────────────────────────┤
│ ZONE A · STAGE           │
│ [title]                  │
│ [why now]                │
│ (PRIMARY CTA)            │
├──────────────────────────┤
│ ZONE B · Mission/Streak  │
├──────────────────────────┤
│ ZONE D · AI insight      │
│ (AI action)              │
├──────────────────────────┤
│ ZONE C · Journey rail    │
│ Past · Present · Next    │
├──────────────────────────┤
│ ZONE F · This week       │
├──────────────────────────┤
│ ZONE E · Mastery whisper │
├──────────────────────────┤
│ ZONE H · Portal links    │
└──────────────────────────┘
```

Stack order matches Phase 1 first-60s cognition: Act → Motive → AI → Orient → Plan → Pride → Browse.

---

## F. Blocking / Empty State Wireframe

Replaces Zone A content; other zones collapse or hide if irrelevant.

```
┌──────────────────────────────────────────────┐
│ LABEL: Access needed                         │
│ [Blocking reason — one sentence]             │
│ (SINGLE RECOVERY CTA)                        │
└──────────────────────────────────────────────┘
```

No stats page. No empty equal cards.

---

## G. Scanning Speed Annotations

| Scan pass | Target | Time budget |
|---|---|---|
| Pass 1 | Stage title + primary CTA | < 3s |
| Pass 2 | Why-now + mission/streak periphery | < 8s |
| Pass 3 | AI insight (if still present) | < 15s |
| Pass 4 | Journey present marker | < 30s |
| Pass 5 | Week list / mastery / portal | only if browsing |

---

## H. Placement Rationale

### Why every section is where it is

| Placement | Rationale (from Phase 1) |
|---|---|
| **Stage at top of main column** | Critical rank #1–2. First notice + immediate action. Must win the first viewport. |
| **Context strip above Stage** | Orientation only; must not outrank Stage. Streak lives here as peripheral motivation. |
| **Mission + AI under Stage (paired)** | After decision clarity: reinforce daily goal and offer proactive help without competing for the primary CTA. |
| **Mission left/start · AI left-adjacent in RTL** | Mission is habit continuity (self); AI is assist (coach). Both Important, neither Critical. |
| **Journey below the pair** | Supports “where am I progressing?” once the student has registered what to do now. Prevents inventory-first LMS pattern. |
| **Planning + Mastery mid-lower** | Time goals and achievement are Important but secondary to acting now. Unequal widths avoid KPI twin boxes. |
| **Resources at bottom** | Optional portal access. Scanning speed: reached only when hunting. |
| **Nav rail separate** | Portal destinations (wallet, settings, catalog) stay out of the learning attention model. |
| **Hidden zones omitted** | Cognitive load law: commerce/account do not share Stage oxygen. |

### How the eye moves (RTL)

1. Enters **Stage** (inline-start emphasis / right side in Arabic UI)  
2. Drops to **primary CTA**  
3. Side-glances **Mission/Streak** then **AI**  
4. Travels along **Journey** past → present → next  
5. Optionally scans **This week** then **Mastery**  
6. Stops or exits into learning — Resources only if needed  

LTR wireframe drawings above are mirrors for documentation; implementation reading flow is RTL-native.

### Why this improves learning

- Converts uncertainty into a single pedagogical act (completion driver).  
- Journey replaces “% and course cards” with narrative continuity (competence).  
- AI attaches help to evidence + action (revision, readiness) instead of browsing.  
- Planning prevents deadline failure without becoming the homepage.  
- Mastery offers pride without badge walls.

### How it minimizes cognitive load

- One primary CTA.  
- One question per zone.  
- No equal grid of widgets.  
- No KPI strip as composition center.  
- Compressed lists (≤5 planning rows).  
- Progressive disclosure for archives/commerce.  
- Blocking states replace noise with one recovery path.  
- Whitespace bands separate groups so scanning does not blur modules together.

### How it increases engagement

- **Cue → action** is immediate (Stage).  
- **Investment** via journey position and mission closure.  
- **Return ritual** via calm streak (peripheral, ethical).  
- **Curiosity** via one AI insight, not a chat abyss.  
- **Pride** sparsely via mastery whisper.  
- Success metric remains: leave the home into real learning quickly — engagement with learning, not with the dashboard chrome.

---

## Phase 2 Exit Criteria

- [x] Structure maps 1:1 to Phase 1 zones and priority matrix  
- [x] Asymmetrical, editorial, compact — not template grid  
- [x] No branding, color, illustration, or visual style  
- [x] Eye-flow and cognitive-load rationale documented  
- [ ] Stakeholder approval before Phase 3 (visual / UI design)

**Next phase (not started):** Visual design system application — only after this blueprint is approved.
