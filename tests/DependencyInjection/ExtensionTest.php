<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\DependencyInjection;

use Doctrine\Persistence\ManagerRegistry;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;
use Zjk\DtoMapper\Accessor\DefaultAccessor;
use Zjk\DtoMapper\Accessor\Method\DoctrineProxyMethodAccessor;
use Zjk\DtoMapper\Accessor\Method\PrivateMethodAccess;
use Zjk\DtoMapper\Accessor\Property\DoctrineProxyPropertyAccessor;
use Zjk\DtoMapper\Accessor\Property\PrivatePropertyAccess;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Contract\MethodAccessorInterface;
use Zjk\DtoMapper\Contract\PropertyAccessInterface;
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
use Zjk\DtoMapper\Reader\CachedMetadataReader;
use Zjk\DtoMapper\Transformer\Transformer;
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
use Zjk\DtoMapper\Contract\EntityStrategyInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;
use Zjk\DtoMapper\Contract\RepositoryInterface;
use Zjk\DtoMapper\DependencyInjection\Extension;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use Zjk\DtoMapper\Repository\DoctrineRepositoryRegistry;
use Zjk\DtoMapper\Transformer\UpperTransformer;
use Zjk\DtoMapper\Transformer\UuidTransformer;

final class ExtensionTest extends AbstractExtensionTestCase
{
    public function testDefaultsServiceConfig(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(DoctrineRepositoryRegistry::class, DoctrineRepositoryRegistry::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(DoctrineRepositoryRegistry::class, 0, new Reference(ManagerRegistry::class));
        $this->assertContainerBuilderHasAlias(RepositoryInterface::class, DoctrineRepositoryRegistry::class);

        $this->assertContainerBuilderHasService(PropertyBuilderCreate::class, PropertyBuilderCreate::class);
        $this->assertContainerBuilderHasService(EntityMetadataBuilderCreate::class, EntityMetadataBuilderCreate::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(ReflectionMetadata::class, 0, new Reference(PropertyBuilderCreate::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(ReflectionMetadata::class, 1, new Reference(EntityMetadataBuilderCreate::class));

        $this->assertContainerBuilderHasServiceDefinitionWithTag(UuidTransformer::class, 'zjk_dto_mapper.transformer');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(UpperTransformer::class, 'zjk_dto_mapper.transformer');

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Transformer::class, 0, new Reference(DefaultAccessorInterface::class));
        $this->assertContainerBuilderHasAlias(TransformerInterface::class, Transformer::class);

        $this->assertContainerBuilderHasService(PrivatePropertyAccess::class, PrivatePropertyAccess::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(DoctrineProxyPropertyAccessor::class, 0, new Reference(PrivatePropertyAccess::class));
        $this->assertContainerBuilderHasAlias(PropertyAccessInterface::class, DoctrineProxyPropertyAccessor::class);

        $this->assertContainerBuilderHasService(PrivateMethodAccess::class, PrivateMethodAccess::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(DoctrineProxyMethodAccessor::class, 0, new Reference(PrivateMethodAccess::class));
        $this->assertContainerBuilderHasAlias(MethodAccessorInterface::class, DoctrineProxyMethodAccessor::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(DefaultAccessor::class, 0, new Reference(MethodAccessorInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(DefaultAccessor::class, 1, new Reference(PropertyAccessInterface::class));
        $this->assertContainerBuilderHasAlias(DefaultAccessorInterface::class, DefaultAccessor::class);

        $this->assertContainerBuilderHasService(IdentifierResolver::class, IdentifierResolver::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(IdentifierResolver::class, 0, new Reference(MetadataReaderInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(IdentifierResolver::class, 1, new Reference(DefaultAccessorInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(IdentifierResolver::class, 2, new Reference(TransformerInterface::class));

        $this->assertContainerBuilderHasService(EntityFactory::class, EntityFactory::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(EntityFactory::class, 0, new Reference(RepositoryInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(EntityFactory::class, 1, new Reference(MetadataReaderInterface::class));

        $this->assertContainerBuilderHasService(SingleEntityToDtoMapper::class, SingleEntityToDtoMapper::class);
        $this->assertContainerBuilderHasService(SingleDtoToEntityMapper::class, SingleDtoToEntityMapper::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(SingleDtoToEntityMapper::class, 6, new Reference(RepositoryRelationMapper::class));
        $this->assertContainerBuilderHasService(RelationResolver::class, RelationResolver::class);

        $this->assertContainerBuilderHasService(RepositoryRelationMapper::class, RepositoryRelationMapper::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(RepositoryRelationMapper::class, 0, new Reference(DefaultAccessorInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(RepositoryRelationMapper::class, 1, new Reference(RelationResolver::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(RepositoryRelationMapper::class, 2, new Reference(FullCollectionBuilder::class));

        $this->assertContainerBuilderHasService(FullCollectionBuilder::class, FullCollectionBuilder::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(FullCollectionBuilder::class, 0, new Reference(EntityFactory::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(FullCollectionBuilder::class, 1, new Reference(IdentifierResolver::class));
        $this->assertContainerBuilderHasService(CollectionMapper::class, CollectionMapper::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 0, new Reference(SingleEntityToDtoMapper::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 1, new Reference(SingleDtoToEntityMapper::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 2, new Reference(CollectionMapper::class));
        $this->assertContainerBuilderHasAlias(MapperInterface::class, Mapper::class);
    }

    public function testWithRedisServiceConfig(): void
    {
        $this->load([
            'cache_pool' => 'cache.adapter.redis',
        ]);

        $this->assertContainerBuilderHasAlias(MetadataReaderInterface::class, CachedMetadataReader::class);
        $this->assertContainerBuilderHasAlias('zjk_dto_mapper.cache_pool', 'cache.adapter.redis');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(CachedMetadataReader::class, 0, new Reference(ReflectionMetadata::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(CachedMetadataReader::class, 1, new Reference('zjk_dto_mapper.cache_pool'));
    }

    public function testPropertyStrategiesAreTagged(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(CollectionStrategy::class, 'zjk_dto_mapper.property_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(DtoStrategy::class, 'zjk_dto_mapper.property_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(GetterStrategy::class, 'zjk_dto_mapper.property_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(IdentifierStrategy::class, 'zjk_dto_mapper.property_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(RepositoryStrategy::class, 'zjk_dto_mapper.property_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(SetterStrategy::class, 'zjk_dto_mapper.property_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(TransformerStrategy::class, 'zjk_dto_mapper.property_strategy');
    }

    public function testEntityStrategiesAreTagged(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(EntityStrategy::class, 'zjk_dto_mapper.entity_strategy');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(NewEntityStrategy::class, 'zjk_dto_mapper.entity_strategy');
    }

    public function testPropertyStrategyAutoconfiguration(): void
    {
        $this->load();

        $autoconfigured = $this->container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(PropertyStrategyInterface::class, $autoconfigured);
        $this->assertTrue($autoconfigured[PropertyStrategyInterface::class]->hasTag('zjk_dto_mapper.property_strategy'));
    }

    public function testEntityStrategyAutoconfiguration(): void
    {
        $this->load();

        $autoconfigured = $this->container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(EntityStrategyInterface::class, $autoconfigured);
        $this->assertTrue($autoconfigured[EntityStrategyInterface::class]->hasTag('zjk_dto_mapper.entity_strategy'));
    }

    #[\Override]
    protected function getContainerExtensions(): array
    {
        return [
            new Extension(),
        ];
    }
}
