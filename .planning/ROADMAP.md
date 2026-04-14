# Roadmap

**Project:** PHP SBOM / v1.0 Purl
**Updated:** 2026-04-14

## Phase Overview

### Phase 1: Conformance Test Scaffold
- **Slug:** `01-conformance-test-scaffold`
- **Goal:** Make the `packages/purl` contract executable before implementation by adding package-local test wiring, shared fixtures, and focused contract tests.
- **Requirements:** BUILD-02
- **Status:** Complete
- **Plans:** 2 plans

Plans:
- [x] `01-01-PLAN.md` — add PHPUnit harness and shared conformance fixtures
- [x] `01-02-PLAN.md` — add focused build, parse/round-trip, and invalid-input contract tests

### Phase 2: Canonical Purl Model
- **Slug:** `02-canonical-purl-model`
- **Goal:** Ship the immutable canonical `Purl` value object that owns rendering, qualifier ordering, and subpath cleanup.
- **Depends on:** Phase 1
- **Requirements:** MODEL-01, MODEL-02, MODEL-03
- **Status:** Complete
- **Plans:** 2 plans

Plans:
- [x] `02-01-PLAN.md` — lock model-only executable contract coverage
- [x] `02-02-PLAN.md` — implement the immutable canonical `Purl` model

### Phase 3: Validated Construction Path
- **Slug:** `03-validated-construction-path`
- **Goal:** Add `PurlFactory::make(...)` as the supported programmatic construction path for separated components with structured build-time failures.
- **Depends on:** Phase 2
- **Requirements:** BUILD-01
- **Status:** Complete
- **Plans:** 2 plans

Plans:
- [x] `03-01-PLAN.md` — strengthen build-path and invalid-build contract tests
- [x] `03-02-PLAN.md` — implement `PurlFactory` and the build exception taxonomy

### Phase 4: Spec Parser & Package Docs
- **Slug:** `04-spec-parser-package-docs`
- **Goal:** Consumers can parse raw package URL strings into canonical `Purl` instances and learn the supported parse/build/stringify API from package-local documentation.
- **Depends on:** Phase 3
- **Requirements:** PARSE-01, PARSE-02, PARSE-03, BUILD-03
- **Status:** Complete
- **Plans:** 3 plans
- **Success criteria:**
  - `Parser::parse(...)` returns canonical `Purl` instances for representative valid package URLs, including non-canonical-but-valid inputs that normalize through `toString()`.
  - Invalid parse inputs fail explicitly as `InvalidPurl` with generic messages and stable `context()` metadata.
  - Round-trip tests pass for qualifier ordering, encoding, and subpath cleanup.
  - `packages/purl/README.md` documents quick-start parse/build/stringify usage, canonicalization behavior, and invalid-input handling with cross-ecosystem examples.

Plans:
- [x] `04-01-PLAN.md` — lock parser error/context and round-trip contract expectations
- [x] `04-02-PLAN.md` — implement `Parser::parse(...)` against the existing canonical model and factory
- [x] `04-03-PLAN.md` — write package-local README coverage for parse/build/stringify usage
