# PR Review

AI-agnostic staff-engineer review standard for Trustbird pull requests.

Any AI tool or human reviewer should follow this document. Do not duplicate these rules in editor-specific folders (for example `.cursor/`).

## When to run

Run this review:

1. When asked to review a PR, review all open PRs, or check PR quality.
2. **Immediately after creating any new pull request** (feature, fix, or release). Do not consider the PR finished until this review has been produced and material findings are addressed or explicitly deferred.

## Before reviewing

1. Read project rules (skim; enforce strictly):
   - `.ai/instructions.md`
   - `.ai/philosophy.md`
   - `.ai/boundaries.md`
   - `.ai/architecture.md`
   - `.ai/coding-style.md`
   - `.ai/testing.md`
   - `.ai/package-development.md`
   - `.ai/developer-api.md`
   - `AGENTS.md`
2. Scope to **changed files only** (PR diff / branch vs base).
3. If given a PR number/URL: fetch with `gh pr view` + `gh pr diff`.
4. If asked to review **all open PRs**: list with `gh pr list --state open`, then review each PR separately with the same format.

## Trustbird-specific hard rules

Flag violations of these as **Critical** or **Important** (not suggestions):

| Rule | Severity |
|------|----------|
| Public behaviour bypasses `Trustbird::{domain}()->{action}(...)` / `trustbird()` | Critical |
| Public API parameters not designed for named arguments | Important |
| Business logic in models, service providers, or commands | Important |
| Frontend / UI / views / Livewire / Filament / assets introduced | Critical (package boundary) |
| Destructive migration without docs/strategy | Critical |
| Missing/insufficient tests; coverage must stay 100% for PHP changes | Important |
| User-visible change without docs (`docs/02-usage`) and/or `CHANGELOG.md` | Important |
| Managers instantiate package models directly instead of contracts/`app(Has…)` | Important |
| New domain not wired A–Z in manager/facade/provider/config/tests | Important |
| Unnecessary Composer dependencies | Important |
| Editor-specific agent/config files instead of `.ai/` | Important |

### Package architecture (enforce)

```text
Developer API (Trustbird facade / trustbird())
↓
Typed managers (final readonly)
↓
Actions (complex / semantic events) OR Eloquent (simple CRUD)
↓
Models (persistence only: casts, relations, simple helpers)
```

- Actions: one responsibility; semantic events for meaningful ops.
- CRUD: Eloquent lifecycle events are enough.
- Contracts: `Has{Plural}`; concerns: `InteractsWith{Plural}`.
- All resources: `workspace_id` + `BelongsToWorkspace` (except Workspace).
- Prefer additive migrations; SQLite + MySQL compatible.

### Project-specific preferences

- Prefer Actions over fat Controllers (consuming apps); in this package prefer managers/actions over fat models.
- Never place business logic inside Livewire/Filament components (and never add those to this package).
- Heavy work always goes to queued Jobs.
- Services should be stateless.
- Repositories are discouraged unless they add real abstraction.
- Prefer Eloquent over raw SQL.
- Avoid facades inside domain logic where dependency injection is possible.
- Prefer Value Objects and Enums over magic strings.
- Every new feature should have tests.
- Avoid introducing unnecessary packages.
- Follow existing architecture instead of inventing a new pattern.

### Ignore

- Personal formatting preferences
- Minor style already covered by Pint
- Reordering imports
- One-line formatting

### Do NOT suggest

- Controllers / Form Requests / Filament / Livewire patterns for this package
- Repositories unless they add real abstraction
- Over-splitting into tiny classes
- Subjective one-line saves
- New patterns that fight existing domain scaffolding
- Editor-specific tooling files (`.cursor/`, `.vscode/` agent rules, etc.) — put shared AI guidance in `.ai/` only

## Severity

**Critical** — Security, data loss, breaking public API, performance regressions, race conditions, N+1, incorrect business logic, package boundary violations

**Important** — Laravel/Trustbird best practices, SOLID, duplication, missing validation where applicable, missing transactions, missing tests/docs, wrong layer

**Suggestion** — Cleaner implementation, naming, expressive Laravel/PHP features, readability

## Evaluate

### Architecture

Correct layer? Thin public surface? Logic in managers/actions? Side effects via events? No god services?

### Laravel (package-appropriate)

Prefer: enums, casts, relationships, scopes, collections, queues/jobs when appropriate, events, config in `config/trustbird.php`, DI over hidden statics in domain code. Flag reinventing what Laravel already provides.

### Database

Indexes, N+1, FKs, cascade, transactions, chunking/cursors, bulk ops, ULID + workspace patterns.

### Eloquent

Relations, casts, fillable via concerns, unnecessary queries, fat models.

### Security

Mass assignment, secrets, unsafe raw SQL, authorization extension points if added, validation of public inputs where the package exposes them.

### Performance

Query-in-loops, missing eager load, cache/queue opportunities.

### Developer API

Named args, BC of parameter names, docs examples use facade only, no encouraging direct Eloquent writes in docs.

### Testing

Feature + Developer API tests, CoverageTest enums, GeneralApiTest managers, 100% coverage, edge cases for actions/guards.

### Code quality

Naming, duplication, complexity, magic strings, dead code.

### Modern PHP

`readonly`, enums, promotion, match, nullsafe, return types, PHPDoc generics where useful.

## Review format

Return Markdown.

# Summary

Overall quality score:
/10

Risk:
Low / Medium / High

## Critical

(if any)

## Important

(if any)

## Suggestions

(if any)

## Laravel Opportunities

List Laravel-specific improvements.

## Performance

## Security

## Tests Missing

## Trustbird Checklist

- [ ] Public API only via managers
- [ ] Named arguments preserved
- [ ] Models stay thin
- [ ] No frontend leakage
- [ ] Migrations additive / dual-DB safe
- [ ] Tests + coverage path considered
- [ ] Docs + changelog if user-visible
- [ ] No editor-specific AI config; shared rules live in `.ai/`

## Overall Recommendation

Approve

Approve with comments

Request Changes

For every finding include:

- File
- Line
- Why it matters
- Suggested fix
- Example code if useful

Avoid nitpicks. Focus on high-impact improvements.

## Multi-PR mode

When reviewing all open PRs:

1. `gh pr list --state open --json number,title,url,author,headRefName`
2. For each PR, produce a separate review under `## PR #<n>: <title>`
3. End with a short table: PR | Score | Risk | Recommendation

## After creating a PR

Whenever an agent or developer opens a new PR with `gh pr create` (or equivalent):

1. Immediately run this review on that PR.
2. Post findings in the conversation (and optionally as a PR comment).
3. Fix Critical/Important issues before asking for human review, unless the user explicitly wants the review only.
