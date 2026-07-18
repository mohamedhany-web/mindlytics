---
name: interaction-design
description: >-
  Interaction design for Mindlytics learning UI — flows, affordances, states,
  gestures, and decision-reducing patterns. Use when defining how students
  click/tap through missions, lessons, AI actions, progress, or multi-step
  learning flows.
---

# Interaction Design

Design every interaction so learning continues with minimal friction.

## North star

One obvious next action at all times.

If the student must hunt, the interaction failed.

## Interaction inventory

Always specify:

- Trigger (what invites action)
- Affordance (why it looks actionable)
- Feedback (immediate response)
- Result (where they land / what changed)
- Recovery (undo, back, alternate path)

## Patterns for learning OS

| Pattern | Use |
|---|---|
| Primary stage CTA | Daily mission / continue learning |
| Progressive disclosure | Advanced AI options stay secondary |
| Inline AI action | Insight → do this now |
| Journey step advance | Celebrate briefly, then next |
| Mastery drill-in | From map → weak topic → practice |
| Calm interrupt | Burnout/protection messaging |

## State design (mandatory)

Hover · Focus · Loading · Disabled · Error · Empty · Skeleton · Success · Completion

Pair with `motion-system` and `accessibility`.

## Decision reduction

- One primary button per region
- Destructive actions require confirm only when costly
- Defaults should match the learner’s most likely next step
- Don’t split equal-weight CTAs that compete

## Mobile + RTL

- Thumb-reachable primary actions
- No hover-only critical info
- Logical swipe/back expectations in Arabic RTL
- Forms: label → field → helper → error order preserved

## Reject

- Mystery meat icons
- Accidental navigation
- Multi-primary button wars
- Interactions that delay content for spectacle

## Checklist

```
Interaction Design Progress:
- [ ] Next action obvious
- [ ] Full state set defined
- [ ] Feedback < 100ms perceived where possible
- [ ] Recovery path exists
- [ ] RTL + mobile verified
```
