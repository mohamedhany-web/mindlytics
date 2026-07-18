---
name: motion-system
description: >-
  Mindlytics motion tokens, transitions, and micro-interaction rules for
  premium learning UI. Use when adding hover/focus/loading/success animations,
  progress transitions, skeletons, or page motion — presence and hierarchy,
  never noise.
---

# Motion System

Everything should feel alive — without feeling busy.

## Tokens

| Token | Duration | Intent |
|---|---|---|
| `motion-fast` | 120–160ms | Hover, focus ring |
| `motion-base` | 200–280ms | Panel / state transitions |
| `motion-slow` | 320–480ms | Progress, completion, success |

Easing: calm, slightly decelerated. Avoid bouncy toy springs unless celebrating a rare milestone.

## Required interaction states

Design every interactive element for:

- Hover
- Focus
- Loading
- Completion
- Success
- Empty
- Skeleton

## Where motion earns its place

- Progress fills and mastery shifts
- Daily mission completion
- Streak increment (subtle)
- AI insight reveal
- Journey step advance
- Soft panel elevation on focus

## Rules

- Ship at least 2–3 intentional motions on visually led screens
- Motion creates presence and hierarchy — not noise
- Prefer opacity/transform/width over layout thrash
- Respect `prefers-reduced-motion`
- No confetti spam, neon glows, or perpetual loops
- Skeleton loading > spinner walls for content regions
- Success feedback should feel earned and brief

## Reject

- Decorative animation with no state meaning
- Autoplaying distraction
- Heavy parallax on dense Arabic reading surfaces
- Motion that delays the next learning action

## Checklist

```
Motion Progress:
- [ ] Tokens applied (fast/base/slow)
- [ ] All critical states covered
- [ ] Reduced-motion path exists
- [ ] No noise / no delay to learning
```
