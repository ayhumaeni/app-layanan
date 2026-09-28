---
name: Sapa Sosial Blitar
colors:
  surface: '#f9f9ff'
  surface-dim: '#cfdaf2'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eeff'
  surface-container-high: '#dee8ff'
  surface-container-highest: '#d8e3fb'
  on-surface: '#111c2d'
  on-surface-variant: '#40484d'
  inverse-surface: '#263143'
  inverse-on-surface: '#ecf1ff'
  outline: '#70787e'
  outline-variant: '#c0c7ce'
  surface-tint: '#1f6583'
  primary: '#00445c'
  on-primary: '#ffffff'
  primary-container: '#0f5c7a'
  on-primary-container: '#95d3f5'
  inverse-primary: '#90cef1'
  secondary: '#835500'
  on-secondary: '#ffffff'
  secondary-container: '#feae2c'
  on-secondary-container: '#6b4500'
  tertiary: '#004925'
  on-tertiary: '#ffffff'
  tertiary-container: '#006334'
  on-tertiary-container: '#6de196'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c2e8ff'
  primary-fixed-dim: '#90cef1'
  on-primary-fixed: '#001e2c'
  on-primary-fixed-variant: '#004d68'
  secondary-fixed: '#ffddb4'
  secondary-fixed-dim: '#ffb955'
  on-secondary-fixed: '#291800'
  on-secondary-fixed-variant: '#633f00'
  tertiary-fixed: '#85faac'
  tertiary-fixed-dim: '#69dd92'
  on-tertiary-fixed: '#00210e'
  on-tertiary-fixed-variant: '#00522a'
  background: '#f9f9ff'
  on-background: '#111c2d'
  surface-variant: '#d8e3fb'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '800'
    lineHeight: 44px
  headline-xl-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '800'
    lineHeight: 36px
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 30px
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
  body-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 20px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '700'
    lineHeight: 24px
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-tablet: 1.5rem
  gutter-desktop: 2rem
  margin: 1rem
  margin-tablet: 2rem
  margin-desktop: 3rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

This design system establishes an empathetic, dignified, and exceptionally accessible public service interface for the residents of Kabupaten Blitar. Rooted in public sector service delivery, the UI removes the intimidating friction typical of legacy bureaucratic portals, replacing administrative austerity with warmth, clarity, and reassurance.

### Brand Personality
- **Mengayomi & Solutif (Nurturing & Problem-Solving):** Approaches every resident with respect, offering direct pathways to social assistance (bansos, healthcare verification, elderly care) without jargon.
- **Tegas & Transparan (Accountable & Clear):** High-visibility process stages and unambiguous status indicators prevent citizen anxiety regarding their application outcomes.
- **Inklusif (Radically Inclusive):** Specifically engineered for low digital literacy, older demographics, and budget-grade mobile devices under low-bandwidth rural conditions.

### Design Movement: Warm Humanist Public Service
The visual language merges modern municipal clarity with humanist warmth:
- Generous micro-copy with conversational, plain-language Bahasa Indonesia prompts.
- Substantial 48px to 56px touch targets throughout all interactive paths.
- Physical visual affordances (subtle directional shadows, clear card enclosures) that explicitly communicate interactivity without relying on abstract iconography.

## Colors

The color system meets strict WCAG 2.1 Level AA and AAA standards across all informative and interactive elements. Color is never used as the single identifier for status; it works in tandem with explicit text labels and semantic iconography.

### Core Swatches
- **Primary (`#0F5C7A` - Deep Teal-Blue):** Anchor color for government authority, structural headers, active navigation, and primary structural elements. Evokes institutional calm and civic trust.
- **Secondary (`#F5A623` - Warm Amber):** Reserved exclusively for high-intent actions, crucial application deadlines, and primary forward-movement buttons. Contenders for click actions must utilize dark text (`#1E293B`) over this amber background to sustain a 4.5:1+ contrast ratio.
- **Tertiary / Success (`#1E9E5A` - Leaf Green):** Communicates successful submissions, verified citizen identities (DTKS verified), and disbursements ready for pickup.
- **Warning (`#F39C12` - Orange):** Flags incomplete profile steps, pending document revisions, or required verifications.
- **Error (`#E74C3C` - Red):** Strictly reserved for non-qualification, expired timelines, or field validation failures.
- **Canvas (`#F7FAFB` - Soft Off-White):** Reduces eye strain on low-cost IPS displays, isolating cards cleanly.
- **Surface (`#FFFFFF` - Pure White):** Used for elevated actionable items, modal dialogues, and form step containers.
- **Typography Tones:** `#1E293B` (Dark Slate) provides a 12.6:1 contrast ratio against white for base text; `#64748B` (Muted Slate) is used strictly for non-critical metadata and step counters.

## Typography

The type system runs exclusively on **Plus Jakarta Sans**, combining open apertures, wide proportions, and distinctive letterforms that prevent confusion between similar glyphs (such as 'I', 'l', and '1')—a frequent issue when elderly citizens read NIK or KK identification numbers.

### Hierarchy & Legibility Rules
- **Line Heights:** Generous line heights (minimum 1.5x) are standard across all body levels to aid tracking for citizens with presbyopia or mild cognitive impairment.
- **Restricted Scale:** Micro-copy below 13px is banned for citizen-facing guidance; `label-sm` is reserved exclusively for non-essential status tags that feature accompanying icons.
- **Numerals:** Tabular figures are enforced inside identification card lookups, bansos quota figures, and family member tallies to guarantee scanning accuracy.

## Layout & Spacing

A mobile-first fluid layout forms the operational base, scaled to support single-thumb navigation on screens as narrow as 360px.

### Grid & Breakpoints
- **Mobile (360px – 599px):** Single-column stack. Content occupies full width between 16px (`1rem`) outer margins. Critical actions (e.g., "Kirim Pengajuan") stick persistently to the bottom viewport with safe-area spacing.
- **Tablet (600px – 1023px):** 6-column fluid grid with 24px (`1.5rem`) gutters and margins. Information dashboards split into primary workflow forms and persistent support/guidance drawers.
- **Desktop (1024px+):** 12-column grid constrained to a maximum width of 1140px, centered with progressive whitespace. Form inputs anchor to an 8-column center track to preserve optimal line lengths (50–70 characters) and prevent horizontal disorientation.

### Vertical Rhythm
A rigid 8px spatial grid organizes layouts. Distance between interactive form controls never drops below `1rem` (16px) to eliminate accidental taps.

## Elevation & Depth

To avoid confusing users with complex stacked layering, this design system uses physical surface layering paired with soft, low-contrast ambient shadows.

### Elevation Hierarchy
- **Level 0 (Canvas):** `#F7FAFB` background. Flat, zero elevation.
- **Level 1 (Card & Content Blocks):** White `#FFFFFF` surfaces defined by a 1px border (`#E2E8F0`) and an ambient shadow: `0px 2px 8px -1px rgba(15, 92, 122, 0.06), 0px 1px 3px 0px rgba(0, 0, 0, 0.04)`. The teal tint grounds the card naturally within the civic palette.
- **Level 2 (Interactive Floating / Bottom Action Bar):** `0px 8px 24px -4px rgba(15, 92, 122, 0.12), 0px 2px 6px -1px rgba(0, 0, 0, 0.04)`. Applied to fixed bottom actions and dropdown menus.
- **Level 3 (Modals & Crisis Alerts):** Elevated dialogs accompanied by a 50% opacity slate (`#0F172A`) backdrop scrim: `0px 20px 32px -8px rgba(15, 92, 122, 0.20)`.

## Shapes

The interface uses balanced curves (equivalent to a base of 12px or `0.75rem` for standard components) to project warmth and modern digital hospitality without sacrificing dense administrative information.

### Applied Corner Curves
- **Standard Controls (Inputs, Buttons, Chips):** `12px` roundedness to provide a gentle, thumb-friendly target.
- **Surface Cards & Modular Panels:** `16px` (`rounded-lg`) to clearly cluster related fields into distinct perceptual units.
- **Modals & Slide-up Drawers:** `24px` (`rounded-xl`) top corners on mobile viewports for intuitive swipe-away perception.
- **Badges & Avatars:** Fully rounded pill-shapes (`9999px`) to immediately distinguish classification indicators from actionable rectangular elements.

## Components

### Buttons
- **Primary Action (Secondary Color - Amber):** Background `#F5A623`, text `#1E293B` (semibold), minimum height 52px. Used for main workflow advancement (e.g., "Daftar Bansos Baru"). Hover/active states darken by 6%.
- **Institutional Primary (Teal):** Background `#0F5C7A`, text `#FFFFFF`, minimum height 48px. Used for non-transactional system actions (e.g., "Cek NIK").
- **Secondary / Ghost:** 2px solid `#0F5C7A` outline, background transparent, text `#0F5C7A`.
- **Large Accessibility Touch Zone:** All mobile buttons feature a minimum physical tap target of 48px × 48px, padded internally with `1rem` horizontal padding.

### Input Fields & Selectors
- **Container Structure:** 52px total height, pure white surface, enclosed by a 1.5px border (`#CBD5E1`).
- **Focus State:** 2px border `#0F5C7A` accompanied by an accessible 3px outer ring `#0F5C7A` with 20% opacity.
- **Elderly Assistive Helpers:** Labels are always persistent above the input (never pure floating placeholders) in `15px` bold slate (`#1E293B`). Micro-helpers display below with contextual examples (e.g., "Contoh: 3505XXXXXXXXXXXX").

### Status Badges (Tri-Attribute Standard)
Every status badge must include:
1. **Background Tint + Solid Border:** Light tint at 12% opacity + 1px matching solid border.
2. **Iconography:** Leading 16px icon (e.g., Check-Circle, Clock-Hour, Alert-Triangle).
3. **Explicit Text:** E.g., `Terverifikasi`, `Menunggu Berkas`, `Perlu Perbaikan`. Never show color dots alone.

### Cards & Service Tiles
- Enclosed with 12px padding on mobile, 20px on desktop.
- Service selection cards (e.g., "Bantuan Lansia", "SKTM Terpadu") include a 48px circular icon container with light teal background (`rgba(15, 92, 122, 0.08)`) and high-contrast line icons, paired with title and one-line benefit explanation.

### Checkboxes & Radio Buttons
- Sized at a minimum of 24px × 24px with high-contrast outlines.
- Wrapped in a full-width selectable row item with 12px padding and active borders so low-motor-skill users can tap anywhere in the row to toggle.

### Verification Banner (Citizen Alert)
- Sticky top banners communicating dynamic status changes regarding local disbursements, featuring high-contrast warning amber or success green with left-anchored status glyphs and direct telephone hotline shortcuts.