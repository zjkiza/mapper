# Changelog

All notable changes to this project will be documented in this file.

## [0.8.0]

### Fixed
- Typo error.
- Remove duplicate code for doctrine proxy initializer.
- N+1 query risk. Add batch lookup mechanism.
- Optimize add, remove and edit for relation.
- Fix Memory Leak Risk.
- Replace hardcoded strategies with tagges services.
- Class Mapper separation of logic into composite services.

### Added
- Add fromCollectionEntityToDtoLazy for lazy loading of relations.
- Add fromCollectionDtoToEntityLazy for lazy loading of relations.

## [0.7.7]

### Fixed
- Dependency, removed composer.lock

## [0.7.6]

### Added
- Raised versions of phpstan and rector and up dependency.

## [0.7.5]

### Added
- Continuous Integration script optimization.

## [0.7.4]

### Added
- Continuous Integration.
- Update dependency.

## [0.7.3]

### Added
- psalm
- phpmd

## [0.7.2]

### Fixed
- Namespace in unit tests
- Fix changelog

### Added
- phpstan-symfony
- Updated version of php in docker from 8.2 to 8.3

## [0.7.1]

### Added
- Optimized code.
- Unused file removed.
- Added class name to reference (for data-fixtures)

## [0.7.0]

### Fixed  
- Bug : the command `bin/console debug:config zjk_dto_mapper` gave an error. 
- Static analyses.
- Documentation

### Added
- Composer dependency.
- Reduction of complexity.

## [0.6.2]

### Added
- Redis for acceleration and updated documentation.

## [0.6.1]

### Fixed
- Description.

## [0.6.0]

### Fixed
- Bug: embedded objects when dto to entity conversion.
- Bug: make file.
- Documentation.

### Added
- Unit test

## [0.5.1]

### Fixed
- Documentation.
- Catches an exception when the data type is not valid on the ID.

### Added
- Unit test

## [0.5.0]
Initial version