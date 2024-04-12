<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Resources\App;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Zjk\DtoMapper\Tests\Resources\Fixtures\TestFixtures;
use Zjk\DtoMapper\ZJKizaMapperBundle;
use Ramsey\Uuid\Doctrine\UuidType;

final class MapperBundleTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \realpath(__DIR__.'/..'); // @phpstan-ignore-line
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DoctrineFixturesBundle(),
            new DAMADoctrineTestBundle(),
            new ZJKizaMapperBundle(),
        ];
    }

    public function configureContainer(ContainerConfigurator $container): void
    {
        $container->services()->set(TestFixtures::class)->tag('doctrine.fixture.orm')->autowire()->autoconfigure();

        $container->extension('framework', [
            'test'          => true,
            'property_info' => [
                'enabled' => true,
            ],
        ]);
        $container->extension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_mysql',
                'url'    => 'mysql://developer:developer@mysql_bundle_1/developer',
                'use_savepoints' => true,
                'types' => [
                    'uuid' => UuidType::class,
                ],
            ],
            'orm'  => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy'             => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping'                => true,
                'enable_lazy_ghost_objects'   => true,
                'report_fields_where_declared' => true,
                'mappings'                    => [
                    'Tests' => [
                        'is_bundle' => false,
                        'type'      => 'attribute',
                        'dir'       => __DIR__.'/Entities',
                        'prefix'    => 'Zjk\DtoMapper\Tests\Resources\App',
                    ],
                ],
            ],
        ]);
    }
}
