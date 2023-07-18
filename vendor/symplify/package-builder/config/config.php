<?php

declare (strict_types=1);
namespace EasyCI202307;

use EasyCI202307\SebastianBergmann\Diff\Differ;
use EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202307\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use EasyCI202307\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use EasyCI202307\Symplify\PackageBuilder\Diff\DifferFactory;
use EasyCI202307\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use function EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(DifferFactory::class);
    $services->set(Differ::class)->factory([service(DifferFactory::class), 'create']);
    $services->set(PrivatesAccessor::class);
};
