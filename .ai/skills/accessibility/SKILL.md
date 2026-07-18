---
name: accessibility
description: >-
  Accessibility standards for Mindlytics RTL learning UI — keyboard, focus,
  contrast, semantics, reduced motion, and inclusive Arabic experiences. Use
  when building student components, forms, dashboards, or reviewing a11y of
  learning flows.
---

# Accessibility

Premium experiences are inclusive by default.

## Baseline

- Semantic HTML before ARIA
- Keyboard reachability for all primary actions
- Visible focus rings (teal-tinted, never removed)
- Contrast: text on surfaces meets WCAG AA minimum
- Hit targets ≥ 44×44px for primary controls
- Honor `prefers-reduced-motion`
- Do not convey meaning by color alone

## RTL + Arabic

- Logical properties (`margin-inline`, `padding-inline`, `inset-inline`) over physical LTR hacks
- Reading order matches Arabic DOM order
- Icons that imply direction flip correctly when needed
- Form errors announced and linked to fields
- Numbers and timers remain readable in RTL

## Learning-specific

- Progress indicators need text/value equivalents
- Gamification celebrations must not be the only success signal
- AI insights need clear headings and actions
- Empty states explain what to do next
- Media/lesson players expose captions/controls when available

## Component checklist

```
A11y Progress:
- [ ] Semantics correct (landmarks, headings, lists, buttons)
- [ ] Keyboard path verified
- [ ] Focus visible
- [ ] Contrast checked for teal/yellow/ink
- [ ] Reduced motion respected
- [ ] Screen-reader labels for icon-only controls
- [ ] Error/success states announced
```

## Reject

- `outline: none` without replacement
- Icon buttons with no accessible name
- Low-contrast yellow text on white
- Motion-only feedback
- Trap focus bugs in overlays
