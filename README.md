# php-depkit Monorepo

Monorepo for PHP ecosystem package tooling used by the php-depkit project.

## Overview

This repository hosts multiple related packages under `packages/*` and keeps
quality tooling centralized at the root.

Current packages include:

- `php-depkit/advisories`
- `php-depkit/dev-deps-client`
- `php-depkit/ecosystem-client`
- `php-depkit/enrichment`
- `php-depkit/manifests`
- `php-depkit/purl`
- `php-depkit/registries`
- `php-depkit/sbom-cli`
- `php-depkit/sbom-scanner`

## Requirements

- PHP 8.3+
- Composer 2+

## Quick Start

```bash
./.wt/setup
```

## Quality Commands

Run from the repository root:

```bash
composer test      # run PHPUnit for all packages that have phpunit.xml(.dist)
composer stan      # run PHPStan level 9 across package src directories
composer ecs       # run Easy Coding Standard checks
composer ci        # run test + stan + ecs
```

## CI

GitHub Actions runs `composer ci` on:

- pushes to `main`
- pushes to `dev`
- pull requests targeting the repository

## Contributing

See `CONTRIBUTING.md` for local development and contribution workflow.

## Versioning & Releases

This repository follows SemVer for published packages.

- Version source of truth: release-please manifest and Git tags
- Change communication: GitHub releases and commit history
- Release automation: release-please (`.github/workflows/release-please.yml`)

Release-please is the release authority. After it creates a release, a
Monorepo Builder `after-release` stage opens the next dev cycle by updating
package branch aliases and future inter-package constraints.

Set a repository secret named `RELEASE_PLEASE_TOKEN` to a PAT if you want the
release PRs and post-release commits to trigger downstream GitHub Actions.

Releases should be reproducible and low-risk through automated CI checks.

Use conventional commits (`feat:`, `fix:`, `feat!:`) so release-please can
build release notes and version bumps correctly.

## License

MIT. See `LICENSE`.
