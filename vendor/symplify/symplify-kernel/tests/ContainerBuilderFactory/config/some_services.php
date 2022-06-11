<?php

declare (strict_types=1);
namespace EasyCI20220611;

use EasyCI20220611\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220611\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
