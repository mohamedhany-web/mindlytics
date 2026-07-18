# Mindlytics Student Dashboard — Design System

Use these tokens for every student experience surface. Do not invent ad-hoc colors or spacing.

## Color tokens

| Token | Value | Role |
|---|---|---|
| `--ml-teal` | `#49A4A2` | Primary brand, focus, progress, primary actions |
| `--ml-yellow` | `#FFD23F` | Accent, achievement, momentum highlights |
| `--ml-white` | `#FFFFFF` | Surfaces, elevated panels |
| `--ml-surface` | `#F7F9FC` | Page canvas |
| `--ml-surface-2` | `#EEF2F7` | Secondary wells, subtle separators |
| `--ml-ink` | `#1A2238` | Primary text |
| `--ml-ink-muted` | `#475569` | Secondary text, captions |

Rules:

- Teal for trust, continuity, learning progress
- Yellow sparingly for celebration / streak / milestone energy
- No random gradients, rainbow palettes, or neon
- Color must signal meaning, not decoration

## Typography tokens

Preferred Arabic fonts (pick one family per implementation and stay consistent):

1. IBM Plex Sans Arabic
2. Tajawal
3. Alexandria
4. Cairo

Suggested scale (RTL-first):

| Token | Size | Weight | Use |
|---|---|---|---|
| `display` | 32–40px | 700–800 | Hero / personal greeting |
| `title` | 22–28px | 700 | Section headlines |
| `subtitle` | 16–18px | 600 | Supporting section lines |
| `body` | 14–16px | 400–500 | Reading text |
| `caption` | 12–13px | 500 | Meta, timestamps, hints |

Rules:

- Arabic is first class
- Strong hierarchy; never flatten all text to one size
- Premium rhythm; avoid cramped Arabic line-height
- Numbers align correctly in RTL contexts

## Spacing scale (8px grid)

`4, 8, 16, 24, 32, 40, 48, 64, 80, 96`

Prefer generous whitespace. Crowding is a defect.

## Radius scale

| Token | Value | Use |
|---|---|---|
| `r-sm` | 8px | Controls, chips |
| `r-md` | 12–16px | Panels, inputs |
| `r-lg` | 20–24px | Hero shells, immersive containers |
| `r-xl` | 28–32px | Rare statement surfaces |

## Shadow / elevation

| Level | Feel | Use |
|---|---|---|
| `e0` | Flat | Canvas |
| `e1` | Soft whisper shadow | Resting panels |
| `e2` | Soft layered depth | Hover / active focus |
| `e3` | Gentle glass lift | Temporary overlays only |

No thick heavy shadows. Prefer soft layered depth and subtle borders.

## Motion tokens

| Token | Duration | Easing intent |
|---|---|---|
| `motion-fast` | 120–160ms | Hover, focus ring |
| `motion-base` | 200–280ms | Panel transitions |
| `motion-slow` | 320–480ms | Progress, completion, success |

Motion must create presence and hierarchy — never noise.

Required states for interactive elements:

- Hover
- Focus
- Loading
- Completion
- Success
- Empty
- Skeleton

## Layout principles

- Editorial / asymmetrical composition over equal card grids
- Hero panel for “what should I do now?”
- Timeline / journey blocks for progress
- Adaptive sections, not identical widgets
- Large white space as a design material
- Glass only when it increases clarity, not as decoration

## Atomic structure

Build reusable pieces:

- Tokens
- Primitives (text, icon, button, progress)
- Molecules (mission strip, streak pulse, AI insight)
- Organisms (hero learning stage, journey rail, mastery map)
- Templates (student home OS shell)

No one-off visual snowflakes that cannot be reused.
