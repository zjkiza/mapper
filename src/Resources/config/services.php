<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Zjk\DtoMapper\Accessor\DefaultAccessor;
use Zjk\DtoMapper\Accessor\Initializer\DoctrineProxyInitializer;
use Zjk\DtoMapper\Accessor\Method\DoctrineProxyMethodAccessor;
use Zjk\DtoMapper\Accessor\Method\PrivateMethodAccess;
use Zjk\DtoMapper\Accessor\Property\DoctrineProxyPropertyAccessor;
use Zjk\DtoMapper\Accessor\Property\PrivatePropertyAccess;
use Zjk\DtoMapper\Builder\Create\EntityMetadataBuilderCreate;
use Zjk\DtoMapper\Builder\Create\PropertyBuilderCreate;
use Zjk\DtoMapper\Builder\Strategy\Entity\EntityStrategy;
use Zjk\DtoMapper\Builder\Strategy\Entity\NewEntityStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\CollectionStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\DtoStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\GetterStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\IdentifierStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\RepositoryStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\SetterStrategy;
use Zjk\DtoMapper\Builder\Strategy\Property\TransformerStrategy;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\MethodAccessorInterface;
use Zjk\DtoMapper\Contract\PropertyAccessInterface;
use Zjk\DtoMapper\Contract\RepositoryInterface;
use Zjk\DtoMapper\Contract\TransformerInterface;
use Zjk\DtoMapper\Mapper;
use Zjk\DtoMapper\Mapping\CollectionMapper;
use Zjk\DtoMapper\Mapping\EntityFactory;
use Zjk\DtoMapper\Mapping\FullCollectionBuilder;
use Zjk\DtoMapper\Mapping\IdentifierResolver;
use Zjk\DtoMapper\Mapping\RelationResolver;
use Zjk\DtoMapper\Mapping\RepositoryRelationMapper;
use Zjk\DtoMapper\Mapping\SingleDtoToEntityMapper;
use Zjk\DtoMapper\Mapping\SingleEntityToDtoMapper;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use Zjk\DtoMapper\Reader\MetadataReader;
use Zjk\DtoMapper\Repository\DoctrineRepositoryRegistry;
use Zjk\DtoMapper\Transformer\Transformer;
use Zjk\DtoMapper\Transformer\UpperTransformer;
use Zjk\DtoMapper\Transformer\UuidTransformer;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Configure Registry
    $services->set(DoctrineRepositoryRegistry::class, DoctrineRepositoryRegistry::class)
        ->args([service(ManagerRegistry::class)]);

    $services->alias(RepositoryInterface::class, DoctrineRepositoryRegistry::class);

    // Property Strategies
    foreach ([
        CollectionStrategy::class,
        DtoStrategy::class,
        GetterStrategy::class,
        IdentifierStrategy::class,
        RepositoryStrategy::class,
        SetterStrategy::class,
        TransformerStrategy::class,
    ] as $strategy) {
        $services->set($strategy)->tag('zjk_dto_mapper.property_strategy');
    }

    // Entity Strategies
    foreach ([
        EntityStrategy::class,
        NewEntityStrategy::class,
    ] as $strategy) {
        $services->set($strategy)->tag('zjk_dto_mapper.entity_strategy');
    }

    // Configure Builder
    $services->set(PropertyBuilderCreate::class, PropertyBuilderCreate::class)
        ->args([tagged_iterator('zjk_dto_mapper.property_strategy')]);

    $services->set(EntityMetadataBuilderCreate::class, EntityMetadataBuilderCreate::class)
        ->args([tagged_iterator('zjk_dto_mapper.entity_strategy')]);

    $services->set(ReflectionMetadata::class, ReflectionMetadata::class)
        ->args([
            service(PropertyBuilderCreate::class),
            service(EntityMetadataBuilderCreate::class),
        ]);

    // Configure Transformer
    $services->set(UuidTransformer::class, UuidTransformer::class)->tag('zjk_dto_mapper.transformer');
    $services->set(UpperTransformer::class, UpperTransformer::class)->tag('zjk_dto_mapper.transformer');

    $services->set(Transformer::class, Transformer::class)
        ->args([
            service(DefaultAccessorInterface::class),
            tagged_iterator('zjk_dto_mapper.transformer'),
        ]);

    $services->alias(TransformerInterface::class, Transformer::class);

    // Configure Doctrine Proxy Initializer
    $services->set(DoctrineProxyInitializer::class, DoctrineProxyInitializer::class);

    // Configure Accessor property
    $services->set(PrivatePropertyAccess::class, PrivatePropertyAccess::class);

    $services->set(DoctrineProxyPropertyAccessor::class, DoctrineProxyPropertyAccessor::class)
        ->args([
            service(PrivatePropertyAccess::class),
            service(DoctrineProxyInitializer::class),
        ]);

    $services->alias(PropertyAccessInterface::class, DoctrineProxyPropertyAccessor::class);

    // Configure Accessor method
    $services->set(PrivateMethodAccess::class, PrivateMethodAccess::class);

    $services->set(DoctrineProxyMethodAccessor::class, DoctrineProxyMethodAccessor::class)
        ->args([
            service(PrivateMethodAccess::class),
            service(DoctrineProxyInitializer::class),
        ]);

    $services->alias(MethodAccessorInterface::class, DoctrineProxyMethodAccessor::class);

    // Configure Accessor default
    $services->set(DefaultAccessor::class, DefaultAccessor::class)
        ->args([
            service(MethodAccessorInterface::class),
            service(PropertyAccessInterface::class),
        ]);

    $services->alias(DefaultAccessorInterface::class, DefaultAccessor::class);

    // Configure Metadata Reader
    $services->set(MetadataReader::class, MetadataReader::class)
        ->args([service(ReflectionMetadata::class)]);

    $services->alias(MetadataReaderInterface::class, MetadataReader::class);

    // Configure Mapping Services
    $services->set(IdentifierResolver::class, IdentifierResolver::class)
        ->args([
            service(MetadataReaderInterface::class),
            service(DefaultAccessorInterface::class),
            service(TransformerInterface::class),
        ]);

    $services->set(EntityFactory::class, EntityFactory::class)
        ->args([
            service(RepositoryInterface::class),
            service(MetadataReaderInterface::class),
        ]);

    $services->set(SingleEntityToDtoMapper::class, SingleEntityToDtoMapper::class)
        ->args([
            service(MetadataReaderInterface::class),
            service(DefaultAccessorInterface::class),
            service(TransformerInterface::class),
        ]);

    $services->set(SingleDtoToEntityMapper::class, SingleDtoToEntityMapper::class)
        ->args([
            service(EntityManagerInterface::class),
            service(MetadataReaderInterface::class),
            service(DefaultAccessorInterface::class),
            service(TransformerInterface::class),
            service(EntityFactory::class),
            service(IdentifierResolver::class),
            service(RepositoryRelationMapper::class),
        ]);

    $services->set(RepositoryRelationMapper::class, RepositoryRelationMapper::class)
        ->args([
            service(DefaultAccessorInterface::class),
            service(RelationResolver::class),
            service(FullCollectionBuilder::class),
        ]);

    $services->set(FullCollectionBuilder::class, FullCollectionBuilder::class)
        ->args([
            service(EntityFactory::class),
            service(IdentifierResolver::class),
        ]);

    $services->set(RelationResolver::class, RelationResolver::class)
        ->args([
            service(RepositoryInterface::class),
            service(DefaultAccessorInterface::class),
            service(IdentifierResolver::class),
        ]);

    $services->set(CollectionMapper::class, CollectionMapper::class)
        ->args([
            service(SingleEntityToDtoMapper::class),
            service(SingleDtoToEntityMapper::class),
            service(EntityFactory::class),
            service(IdentifierResolver::class),
        ]);

    // Configure Mapper
    $services->set(Mapper::class, Mapper::class)
        ->public()
        ->args([
            service(SingleEntityToDtoMapper::class),
            service(SingleDtoToEntityMapper::class),
            service(CollectionMapper::class),
        ]);

    $services->alias(MapperInterface::class, Mapper::class)->public();
};
