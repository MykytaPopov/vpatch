<?php

declare(strict_types=1);

use MykytaPopov\VPatch\Command\GenerateCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/config'));
$loader->load('services.yaml');
$container->compile();

/** @var Application $application */
$application = $container->get(Application::class);

/** @var Command $generateCommand */
$generateCommand = $container->get(GenerateCommand::class);
$application->add($generateCommand);

$application->run();
