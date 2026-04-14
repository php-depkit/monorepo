---
phase: 04-spec-parser-package-docs
plan: 03
subsystem: docs
tags: [php, purl, readme, parser, api]
requires:
  - phase: 04-spec-parser-package-docs
    provides: Locked parser behavior and `InvalidPurl::context()` semantics from Plans 01 and 02
provides:
  - Package-local README coverage for the parse, build, and stringify workflow
  - Canonicalization examples for qualifier ordering and subpath cleanup
  - Consumer-facing invalid-input guidance built around `InvalidPurl::context()`
affects: [packages/purl, 04-spec-parser-package-docs]
tech-stack:
  added: []
  patterns: [public-api-first package docs, value-safe exception guidance, cross-ecosystem examples]
key-files:
  created: [.planning/phases/04-spec-parser-package-docs/04-spec-parser-package-docs-03-SUMMARY.md]
  modified: [packages/purl/README.md]
key-decisions:
  - "Teach parse, build, and stringify as one workflow so consumers can start from the public API instead of implementation details."
  - "Document `InvalidPurl::context()` as the stable inspection point and keep exception-message guidance generic."
patterns-established:
  - "Package READMEs lead with a runnable quick start, then walk through the supported public API story."
  - "Invalid-input docs stay value-safe: examples inspect structured context instead of scraping exception text."
requirements-completed: [BUILD-03]
duration: 7 min
completed: 2026-04-14
---

# Phase 04 Plan 03: Package-local README for parse, build, stringify, and safe invalid-input handling

**Package-local docs now teach the full `packages/purl` workflow with canonicalization examples and stable `InvalidPurl::context()` guidance.**

## Performance

- **Duration:** 7 min
- **Started:** 2026-04-14T22:30:13Z
- **Completed:** 2026-04-14T22:36:44Z
- **Tasks:** 2
- **Files modified:** 1

## Accomplishments
- Reworked `packages/purl/README.md` around the supported parse, build, and stringify workflow.
- Added cross-ecosystem examples for Composer, npm, Maven, and qualifier-bearing package URLs.
- Documented canonicalization and invalid-input handling without promising unsupported exception internals.

## Task Commits

Each task was committed atomically:

1. **Task 1: Draft quick-start and canonical workflow documentation** - `77c8b7e` (docs)
2. **Task 2: Document invalid-input handling and stable context inspection** - `06e24fb` (docs)

**Plan metadata:** Pending

## Files Created/Modified
- `packages/purl/README.md` - Documents quick start, parse/build/stringify usage, canonicalization behavior, and safe invalid-input handling.

## Decisions Made
- Taught parse, build, and stringify as one consumer workflow instead of splitting the README into parser-only and builder-only narratives.
- Kept invalid-input examples centered on `InvalidPurl::context()` so consumers rely on the supported `component` and `reason` shape.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- `packages/purl/README.md` already existed as an untracked file in the working tree. I adopted it, aligned it to the locked Phase 4 decisions, and committed it in task-sized slices.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Phase 04 now covers the shipped parser contract and the package-local docs for the current public API.
- The `packages/purl` milestone is ready for phase-level completion review.

## Self-Check: PASSED

- Found summary file: `.planning/phases/04-spec-parser-package-docs/04-spec-parser-package-docs-03-SUMMARY.md`
- Found commit: `77c8b7e`
- Found commit: `06e24fb`

---
*Phase: 04-spec-parser-package-docs*
*Completed: 2026-04-14*
