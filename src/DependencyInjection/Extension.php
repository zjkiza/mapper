<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Zjk\DtoMapper\Contract\DataTransformerInterface;
use Zjk\DtoMapper\Contract\EntityStrategyInterface;
use Zjk\DtoMapper\Contract\PropertyStrategyInterface;

/**
 * @psalm-type RowConfiguration = array{
 *     cache_pool?: string
 * }
 */
final class Extension extends SymfonyExtension
{
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var RowConfiguration $config */
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader        = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $container
            ->registerForAutoconfiguration(DataTransformerInterface::class)
            ->addTag('zjk_dto_mapper.transformer');

        $container
            ->registerForAutoconfiguration(PropertyStrategyInterface::class)
            ->addTag('zjk_dto_mapper.property_strategy');

        $container
            ->registerForAutoconfiguration(EntityStrategyInterface::class)
            ->addTag('zjk_dto_mapper.entity_strategy');

        if (isset($config['cache_pool'])) {
            $container->setAlias('zjk_dto_mapper.cache_pool', $config['cache_pool']);
            $loader->load('cache_services.php');
        }
    }

    #[\Override]
    public function getAlias(): string
    {
        return 'zjk_dto_mapper';
    }
}
