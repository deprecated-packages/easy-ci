<?php

declare (strict_types=1);
namespace EasyCI202307\Symplify\SymplifyKernel\Config\Loader;

use EasyCI202307\Symfony\Component\Config\FileLocator;
use EasyCI202307\Symfony\Component\Config\Loader\DelegatingLoader;
use EasyCI202307\Symfony\Component\Config\Loader\GlobFileLoader;
use EasyCI202307\Symfony\Component\Config\Loader\LoaderResolver;
use EasyCI202307\Symfony\Component\DependencyInjection\ContainerBuilder;
use EasyCI202307\Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use EasyCI202307\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
final class ParameterMergingLoaderFactory implements LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \EasyCI202307\Symfony\Component\Config\Loader\LoaderInterface
    {
        $fileLocator = new FileLocator([$currentWorkingDirectory]);
        $loaders = [new GlobFileLoader($fileLocator), new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator)];
        $loaderResolver = new LoaderResolver($loaders);
        return new DelegatingLoader($loaderResolver);
    }
}
