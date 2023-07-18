<?php

declare (strict_types=1);
namespace EasyCI202307\Symplify\EasyTesting\Kernel;

use EasyCI202307\Psr\Container\ContainerInterface;
use EasyCI202307\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use EasyCI202307\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
