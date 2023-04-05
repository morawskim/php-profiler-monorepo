<?php

namespace Mmo\PhpProfilerCli\Infrastructure\Symfony\DependencyInjection;

use Composer\InstalledVersions;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AppCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = new Definition(Application::class);
        $definition->setPublic(true);
        $definition->addMethodCall('setName', ['mmo/php-profiler-cli']);
        $definition->addMethodCall('setVersion', [InstalledVersions::getVersion('mmo/php-profiler-cli') ?? 'unknown']);
        $container->setDefinition(Application::class, $definition);

        $definition = $container->getDefinition(Application::class);
        $definition->addMethodCall('addCommands', [
            array_map(static fn ($id) => new Reference($id), array_keys($container->findTaggedServiceIds('app_commands')))
        ]);
    }
}
