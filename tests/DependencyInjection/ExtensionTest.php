<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\DependencyInjection;

use Doctrine\ORM\EntityManagerInterface;
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
use Zjk\DtoMapper\Reader\CachedMetadataReader;
use Zjk\DtoMapper\Transformer\Transformer;
use Zjk\DtoMapper\Builder\Create\EntityMetadataBuilderCreate;
use Zjk\DtoMapper\Builder\Create\PropertyBuilderCreate;
use Zjk\DtoMapper\Contract\DefaultAccessorInterface;
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

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 0, new Reference(EntityManagerInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 1, new Reference(RepositoryInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 2, new Reference(MetadataReaderInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 3, new Reference(DefaultAccessorInterface::class));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Mapper::class, 4, new Reference(TransformerInterface::class));
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

    protected function getContainerExtensions(): array
    {
        return [
            new Extension(),
        ];
    }
}
