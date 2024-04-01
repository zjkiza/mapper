<?php

declare(strict_types=1);

namespace DependencyInjection\CompillerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zjk\DtoMapper\DependencyInjection\Compiler\TransformerCompilerPass;
use Zjk\DtoMapper\Transformer\Transformer;
use Zjk\DtoMapper\Transformer\UpperTransformer;
use Zjk\DtoMapper\Transformer\UuidTransformer;

final class ConfigureTest extends AbstractCompilerPassTestCase
{
    public function testDefaultTransformerTagsServices(): void
    {
        $this->container->register(Transformer::class);

        $this->container->register(UpperTransformer::class)->addTag('zjk.mapper.transformer');
        $this->container->register(UuidTransformer::class)->addTag('zjk.mapper.transformer');

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Transformer::class, '$transformers', [
            UpperTransformer::class => new Reference(UpperTransformer::class),
            UuidTransformer::class => new Reference(UuidTransformer::class),
        ]);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TransformerCompilerPass());
    }
}
