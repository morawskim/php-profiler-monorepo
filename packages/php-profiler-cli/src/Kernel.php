<?php

namespace Mmo\PhpProfilerCli;

use Mmo\PhpProfilerCli\Infrastructure\Symfony\DependencyInjection\AppCompilerPass;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class Kernel
{
    public function run(): void
    {
        $container = $this->buildContainer();
        $app = $container->get(Application::class);
        $app->run();
    }

    private function buildContainer(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addCompilerPass(new AppCompilerPass());

        $loaderPhp = new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../config'),
            null,
        );

        $loader = new DelegatingLoader(new LoaderResolver([$loaderPhp]));
        $loader->load('config.php');
        $containerBuilder->compile();

        return $containerBuilder;
    }
}
