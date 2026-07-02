# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog and this project adheres to Semantic Versioning.

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