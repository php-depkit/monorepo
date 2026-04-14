# Requirements

**Milestone:** v1.0 Purl
**Updated:** 2026-04-14

## Active Requirements

| ID | Phase | Status | Requirement |
|---|---|---|---|
| BUILD-02 | 01 | complete | `packages/purl` has a package-local PHPUnit scaffold and shared conformance fixtures that later phases can execute from the monorepo root. |
| MODEL-01 | 02 | complete | `packages/purl/src/Purl.php` provides an immutable value object with explicit getters for `type`, `namespace`, `name`, `version`, `qualifiers`, and `subpath`. |
| MODEL-02 | 02 | complete | Canonical qualifier ordering and subpath cleanup are preserved in-memory and in `Purl::toString()`. |
| MODEL-03 | 02 | complete | Canonical rendering percent-encodes components correctly and omits empty optional components cleanly. |
| BUILD-01 | 03 | complete | `PurlFactory::make(...)` constructs canonical `Purl` instances from separated components and rejects invalid build input through typed `InvalidPurl` failures with structured context. |
| PARSE-01 | 04 | complete | `Parser::parse(string)` accepts structurally valid raw PURLs, decodes components, and returns canonical `Purl` instances even when the input is not already canonical. |
| PARSE-02 | 04 | complete | Malformed, incomplete, or badly encoded raw PURLs throw `InvalidPurl` with generic value-safe messages and stable `context()` metadata naming the failing component and reason. |
| PARSE-03 | 04 | complete | Parse and round-trip behavior stays stable across representative Composer, npm, Maven, qualifier-bearing, encoding, and subpath cases. |
| BUILD-03 | 04 | complete | `packages/purl/README.md` documents the public parse/build/stringify workflow with quick start, canonicalization, invalid-input handling via `InvalidPurl::context()`, and cross-ecosystem examples including a qualifier-bearing case. |

## Notes

- Phase 04 is intentionally limited to parser + README work inside `packages/purl`.
- Type metadata, registry URL generation, reverse-registry parsing, and broad convenience APIs remain out of scope for this milestone.
