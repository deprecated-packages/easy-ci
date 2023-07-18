<?php

declare (strict_types=1);
namespace EasyCI202307;

use EasyCI202307\Symfony\Component\Console\Application;
use EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202307\Symplify\EasyTesting\Command\ValidateFixtureSkipNamingCommand;
use function EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('EasyCI202307\Symplify\EasyTesting\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/DataProvider', __DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(Application::class)->call('add', [service(ValidateFixtureSkipNamingCommand::class)]);
};
