# Changelog

All notable changes to this project will be documented in this file.

## [0.9.0]

### Fixed
- Command `bin/run.py` failed with `failed to get console: provided file is not a console`.
- Option `--verbose` of the `bin` scripts did not display the output of the docker compose.
- Abandoned package `composer/package-versions-deprecated` is no longer installed in the development environment.
- Composer could not detect the root package version in the docker container.

### Added
- Add support for Symfony 8 (`symfony/dependency-injection`, `symfony/config`, `symfony/console`, `symfony/http-kernel` ^8.0).
- Add support for `doctrine/persistence` ^4.
- Add GitHub Actions workflow with a test matrix (PHP 8.2 - 8.5, Symfony 6.4, 7.4 and 8.1, lowest dependencies) and a static analysis job.
- Add `phpstan/phpstan-strict-rules` to the static analysis.
- Add services `FullCollectionBuilder` and `RepositoryRelationMapper` extracted from `SingleDtoToEntityMapper`.
- Add supported versions table to the README.

### Changed
- Move service definitions from XML (`services.xml`, `cache_services.xml`) to PHP (`services.php`, `cache_services.php`),
  because the XML configuration format is deprecated since Symfony 7.4. Service ids and aliases are not changed.
- `Zjk\DtoMapper\DependencyInjection\Extension` extends `Symfony\Component\DependencyInjection\Extension\Extension`
  because `Symfony\Component\HttpKernel\DependencyInjection\Extension` is not available in the newest Symfony versions.
- `SingleDtoToEntityMapper` has a new constructor, `RelationResolver` is replaced with `RepositoryRelationMapper`
  (the class is an internal service, it is wired by the bundle).
- `RelationResolver` throws `Zjk\DtoMapper\Exception\RuntimeException` when the add or remove method of the relation does not exist or is not public.
- Parameter `$collections` of `MapperInterface::fromCollectionDtoToEntity()` and `fromCollectionDtoToEntityLazy()` is documented as `iterable<int|string, object>`.
- `ThrowIfTrait::throwIf()` accepts only a boolean condition.
- Development environment: PHP 8.4 docker image, PHPUnit 13 (the tests run on PHPUnit 11.5 - 13), Psalm 6, PHPMD 3, `docker compose` instead of `docker-compose`,
  MySQL health check instead of waiting in a loop.
- PHPUnit fails on deprecations triggered by the project code.
- Rector configuration is updated for Rector 2.

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