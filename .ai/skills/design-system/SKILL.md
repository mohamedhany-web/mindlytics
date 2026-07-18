---
name: design-system
description: >-
  Mindlytics design tokens, atomic components, spacing, radius, elevation, and
  composition rules. Use when defining or applying UI tokens, building reusable
  student components, establishing spacing/type scales, or preventing one-off
  styles in Mindlytics learning surfaces.
---

# Design System

Source of truth for reusable visual foundations across Mindlytics learning product surfaces.

Related: `branding`, `motion-system`, `arabic-typography`, `dashboard-experience`.

## Mission

Build a coherent atomic system so every screen feels like one premium product — not a collage of templates.

## Brand anchors

| Token | Value | Role |
|---|---|---|
| `--ml-teal` | `#49A4A2` | Primary |
| `--ml-yellow` | `#FFD23F` | Accent / achievement |
| `--ml-white` | `#FFFFFF` | Elevated surface |
| `--ml-surface` | `#F7F9FC` | Canvas |
| `--ml-surface-2` | `#EEF2F7` | Wells |
| `--ml-ink` | `#1A2238` | Primary text |
| `--ml-ink-muted` | `#475569` | Secondary text |

No random gradients. No neon. No rainbow. Color with intention only.

## Scales

### Spacing (8px grid)

`4, 8, 16, 24, 32, 40, 48, 64, 80, 96`

### Radius

`8 / 12–16 / 20–24 / 28–32`

### Elevation

- `e0` flat canvas
- `e1` soft resting panel
- `e2` hover / focus lift
- `e3` temporary overlay only

### Type roles

`display` · `title` · `subtitle` · `body` · `caption`

Prefer: IBM Plex Sans Arabic, Tajawal, Alexandria, or Cairo.

## Atomic structure

1. Tokens
2. Primitives (text, icon, button, input, progress)
3. Molecules (mission strip, streak pulse, AI insight)
4. Organisms (hero stage, journey rail, mastery map)
5. Templates (learning OS shell)

## Rules

- Extract reusable components; ban one-off mega CSS blobs
- Prefer CSS variables / shared partials already used in the repo
- Asymmetry and editorial composition over equal card grids
- Large whitespace is a material, not empty leftover
- Soft shadows + subtle borders; never thick chrome
- Every new primitive must document states: default, hover, focus, disabled, loading

## Agent checklist

```
Design System Progress:
- [ ] Map existing tokens/components in repo
- [ ] Reuse before inventing
- [ ] Bind colors/spacing/type to tokens
- [ ] Name components by role, not decoration
- [ ] Verify RTL + 8px grid
- [ ] Reject template-looking equal widget grids
```

## Reject

AdminLTE · Bootstrap card kits · Tailwind dashboard clones · KPI box walls · heavy borders · thick shadows · color overload
