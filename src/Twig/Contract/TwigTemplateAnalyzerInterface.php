<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Twig\Contract;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
interface TwigTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos) : array;
}
