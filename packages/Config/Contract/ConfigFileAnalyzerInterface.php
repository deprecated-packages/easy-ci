<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Config\Contract;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
interface ConfigFileAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos) : array;
}
