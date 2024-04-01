<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as SymfonyExtension;
use Zjk\DtoMapper\Contract\DataTransformerInterface;

final class Extension extends SymfonyExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader        = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container
            ->registerForAutoconfiguration(DataTransformerInterface::class)
            ->addTag('zjk.mapper.transformer');
    }
}
