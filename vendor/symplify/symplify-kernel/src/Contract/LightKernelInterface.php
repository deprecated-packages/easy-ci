<?php

declare (strict_types=1);
namespace EasyCI202307\Symplify\SymplifyKernel\Contract;

use EasyCI202307\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface;
    public function getContainer() : ContainerInterface;
}
