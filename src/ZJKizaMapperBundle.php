<?php

declare(strict_types=1);

namespace Zjk\DtoMapper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zjk\DtoMapper\DependencyInjection\Compiler\TransformerCompilerPass;
use Zjk\DtoMapper\DependencyInjection\Extension;

final class ZJKizaMapperBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TransformerCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ExtensionInterface
    {
        return new Extension();
    }
}
