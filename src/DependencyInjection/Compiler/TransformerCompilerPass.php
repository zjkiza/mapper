<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zjk\DtoMapper\Transformer\Transformer;

final class TransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(Transformer::class)) {
            return;
        }

        $definition = $container->getDefinition(Transformer::class);

        $transformers = [];
        foreach (\array_keys($container->findTaggedServiceIds('zjk_dto_mapper.transformer')) as $id) {
            $transformers[$id] = new Reference($id);
        }

        $definition->setArgument('$transformers', $transformers);
    }
}
