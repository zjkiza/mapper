<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Zjk\DtoMapper\Contract\MetadataReaderInterface;
use Zjk\DtoMapper\Metadata\ReflectionMetadata;
use Zjk\DtoMapper\Reader\CachedMetadataReader;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(CachedMetadataReader::class, CachedMetadataReader::class)
        ->args([
            service(ReflectionMetadata::class),
            service('zjk_dto_mapper.cache_pool'),
        ]);

    $services->alias(MetadataReaderInterface::class, CachedMetadataReader::class);
};
