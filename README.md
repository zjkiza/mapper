# DTO Mapper

Mapping Symfony entity to dto, dto to Symfony entity, Symfony entity collection to dto and dto collection to Symfony entity collection. 
**Bundle is based on using setters and getters from entity.** 

## Requirements

| Package              | Supported versions                       | Tested in CI                     |
|----------------------|------------------------------------------|----------------------------------|
| PHP                  | 8.2 - 8.5                                | 8.2, 8.3, 8.4 (8.5 experimental) |
| Symfony              | 5.4, 6.4, 7.x, 8.x                       | 6.4, 7.4, 8.1                    |
| Doctrine ORM         | 2.17, 3.x                                | 2.17 (lowest), 3.x               |
| Doctrine Persistence | 2.4 - 4.x                                | 2.5 (lowest), 4.x                |

Symfony 5.4 is supported by the bundle code, but it is not covered by the automated tests because the test stack
(PHPUnit 11.5 or newer, DAMA Doctrine test bundle) requires Symfony 6.4 or newer.

## Table of contents

- [Installation and configuration](docs/installation.md)
- [Instructions for setting up DTO and Entity to work with the mapper:](docs/guide.md)
    - [Settings on the entity](docs/guide.md#settings-on-the-entity)
    - [Configuration of DTO objects](docs/guide.md#configuration-of-dto-objects)
    - [Entity to DTO mapping](docs/guide.md#entity-to-dto)
    - [DTO to Entity mapping](docs/guide.md#dto-to-entity)
- [Working with the mapper](docs/working.md)

## Planned
- support that all data can be sent via the entity constructor.