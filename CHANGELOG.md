# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog and this project adheres to Semantic Versioning.

## [0.1.0-alpha.4] - 2026-07-07

### Added

- **Documentation**: New Events guide explaining the choice for Eloquent lifecycle events and semantic domain events.
- **Documentation**: New Custom Models guide explaining how to extend the package using Contracts and Concerns.
- **Testing**: Reached and enforced 100% test coverage across the entire package.
- **Incidents Module**: Initial scaffolding for security, privacy and operational events (incidents + timeline notes).
- **Suppliers Module**: Initial scaffolding for suppliers/vendors including review metadata and relation foundation.
- **Tasks Module**: Initial scaffolding for actionable governance work (tasks + relations foundation).
-

### Changed

- **Architecture**: Simplified the domain layer by removing redundant CRUD Action classes (`CreateAsset`, `UpdatePerson`, etc.). Managers now interact directly with Eloquent models for basic operations.
- **Architecture**: Removed custom Event classes for standard CRUD operations (Created, Updated, Deleted) in favor of Laravel's native Eloquent lifecycle events (`eloquent.created`, etc.).
- **Architecture**: Renamed Contracts (Interfaces) to follow a pluralized naming convention (`HasPerson` -> `HasPeople`, `HasAsset` -> `HasAssets`) to avoid naming conflicts.
- **Architecture**: Renamed Concerns (Traits) to follow the `InteractsWith{Domain}s` convention (e.g., `InteractsWithPeople`).
- **Managers**: Updated all domain managers to support PHP named arguments and more flexible input types (ID, Array, or Object).
- **Documentation**: Refactored all usage examples to use the `Trustbird` facade and named arguments for better consistency and readability.
- **AI Guidelines**: Updated `.ai` instructions to reflect the new architecture, naming conventions, and testing requirements.

### Removed

- Redundant CRUD Action classes across all domains.
- Redundant CRUD Event classes (Created, Updated, Deleted) across all domains.
- Empty directories left over after architectural simplification.

### Fixed

- Inconsistent test assertions for events.
- Missing coverage for several manager and service provider methods.
- **Policies Module**: Initial implementation including `Policy` and `PolicyVersion` models, migrations, and factories.
- **Policies Module**: Versioned draft/publish lifecycle via `DraftPolicyVersion`, `UpdatePolicyVersion`, and `PublishPolicyVersion`.
- **Policies Module**: Owner and reviewer relationships to `Person`, plus review workflow via `ReviewPolicy`.
- **Policies Module**: Integrated events (`PolicyCreated`, `PolicyUpdated`, `PolicyReviewed`, `PolicyVersionDrafted`, `PolicyVersionUpdated`, `PolicyVersionPublished`).
- **Documentation**: Added policies domain usage guide.
- **Documentation**: Added incidents domain usage guide.

## [0.1.0-alpha.3] - 2026-07-02

### Added

- **Workspace Module**: Initial implementation including `Workspace` model, migration, factory, and actions.
- **Teams Module**: Initial implementation including `Team` model, migration, factory, and actions.
- **Risks Module**: Initial implementation including `Risk` model, migration, factory, and actions.

## [0.1.0-alpha.2] - 2026-07-01

### Added

- **Assets Module**: Initial implementation including `Asset` model, migrations, and actions.
- **Assets Module**: Support for various asset kinds (Device, System, Application, DataStore, etc.).
- **Assets Module**: Integrated events (`AssetCreated`, `AssetUpdated`, `AssetDeleted`).
- **People Module**: Enhanced with standard events for all actions (`PersonCreated`, `PersonUpdated`, `PersonTerminated`).
- **People Module**: Added `MarkPersonnelTaskComplete` and `RecordPersonnelReminder` actions with events.
- **Documentation**: Comprehensive English documentation for People and Assets domains.
- **AI Guidelines**: Added structured `.ai` guidelines for architecture, coding style, and testing.
- **Development**: Achieved 100% test coverage for the core Asset model.

### Changed

- Renamed `AssetType` to `AssetKind` and optimized `Asset` model methods for better data classification.
- Updated documentation and guidelines to be exclusively in English.

## [0.1.0-alpha.1] - 2026-06-23

### Added

- Initial public alpha release.
- Basic package skeleton.
- Automatic Laravel package discovery.