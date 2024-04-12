<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$bootstrap = \getenv('BOOTSTRAP') ?: true;
$bootstrap = \is_bool($bootstrap) ? $bootstrap : !\in_array(\strtolower($bootstrap), ['false', '0', 'off', 'no'], true);

if (!$bootstrap) {
    return;
}

require_once __DIR__ . '/tests/bootstrap.php';
