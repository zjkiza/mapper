<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Resources;

use Zjk\DtoMapper\Tests\Resources\App\MapperBundleTestKernel;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return MapperBundleTestKernel::class;
    }
}
