<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Zjk\DtoMapper\Tests\Resources\App\MapperBundleTestKernel;

(new Filesystem())->remove([
    __DIR__.'/Resources/var/cache',
    __DIR__.'/Resources/var/log',
]);

$kernel = new MapperBundleTestKernel('test', false);

$kernel->boot();

$output   = new ConsoleOutput();
$commands = [
    [
        'command' => 'doctrine:schema:drop',
        '--env'   => 'test',
        '--force' => null,
    ],
    [
        'command' => 'doctrine:schema:create',
        '--env'   => 'test',
    ],
    [
        'command'          => 'doctrine:fixtures:load',
        '--env'            => 'test',
        '--no-interaction' => null,
    ],
];

$application = new Application($kernel);

$application->setAutoExit(false);

try {
    foreach ($commands as $command) {
        if (Command::SUCCESS !== $application->run(new ArrayInput($command), $output)) {
            throw new RuntimeException('Unable to reset environment to initial state.');
        }
    }
} finally {
    $kernel->shutdown();
}
