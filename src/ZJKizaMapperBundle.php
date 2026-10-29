<?php

declare(strict_types=1);

namespace Zjk\DtoMapper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zjk\DtoMapper\DependencyInjection\Compiler\TransformerCompilerPass;
use Zjk\DtoMapper\DependencyInjection\Extension;

/**
 * Bundle implements BundleInterface which is deprecated since Symfony 8.1, but its replacement
 * (Symfony\Component\DependencyInjection\Kernel\BundleInterface) does not exist in Symfony 5.4 - 8.0.
 *
 * @psalm-suppress DeprecatedInterface
 */
final class ZJKizaMapperBundle extends Bundle
{
    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TransformerCompilerPass());
    }

    #[\Override]
    public function getContainerExtension(): ExtensionInterface
    {
        return new Extension();
    }
}
