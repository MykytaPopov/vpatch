<?php

declare(strict_types=1);

$autoloads = [
    '../../../autoload.php',
    'vendor/autoload.php',
];

foreach ($autoloads as $autoload) {
    if (!file_exists($autoload)) {
        continue;
    }

    require_once $autoload;
}
