<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as SymfonyExtension;
use Zjk\DtoMapper\Contract\DataTransformerInterface;

/**
 * @psalm-type RowConfiguration = array{
 *     cache_pool?: string
 * }
 */
final class Extension extends SymfonyExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var RowConfiguration $config */
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader        = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container
            ->registerForAutoconfiguration(DataTransformerInterface::class)
            ->addTag('zjk_dto_mapper.transformer');

        if (isset($config['cache_pool'])) {
            $container->setAlias('zjk_dto_mapper.cache_pool', $config['cache_pool']);
            $loader->load('cache_services.xml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'zjk_dto_mapper';
    }
}
