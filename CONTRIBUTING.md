# Contributing

Thanks for contributing.

## Development Setup

1. Clone the repository.
2. Install dependencies:

```bash
./.wt/setup
```

`composer install` also installs the local CaptainHook hooks. Commits must use
the Conventional Commits format, and pre-commit runs `composer ecs`,
`composer stan`, and `composer test`.

## Run Checks

Use the root Composer scripts:

```bash
composer test
composer stan
composer ecs
composer ci
```

## Project Layout

- Packages live under `packages/*`.
- Shared tooling and CI config live at the repository root.

## Pull Requests

- Keep changes focused and minimal.
- Add or update tests when behavior changes.
- Run `composer ci` before opening a pull request.
- Explain the motivation and impact in the PR description.
- Use conventional commits (`feat:`, `fix:`, `feat!:`) for release automation.
- Release tags are created by release-please; Monorepo Builder handles the
  post-release next-dev updates.
- Configure the `RELEASE_PLEASE_TOKEN` repository secret with a PAT so
  release automation can trigger downstream workflows.
