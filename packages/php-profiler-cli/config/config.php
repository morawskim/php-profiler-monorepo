<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Console\Command\Command;

return function(ContainerConfigurator $configurator) {
    // default configuration for services in *this* file
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->instanceof(Command::class)
        ->tag('app_commands');

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('Mmo\PhpProfilerCli\\', '../src/*')
        ->exclude([
            '../src/{DependencyInjection,Entity,Tests,Kernel.php}',
            '../src/{Converter,Dto,FlameGraph,Reader,TextUI}',
        ]);
};
