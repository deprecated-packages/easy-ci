<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Finder;

use EasyCI202307\Symplify\SmartFileSystem\Finder\SmartFinder;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
final class ProjectFilesFinder
{
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\Finder\SmartFinder
     */
    private $smartFinder;
    public function __construct(SmartFinder $smartFinder)
    {
        $this->smartFinder = $smartFinder;
    }
    /**
     * @param string[] $sources
     * @return SmartFileInfo[]
     */
    public function find(array $sources) : array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = \getcwd() . \DIRECTORY_SEPARATOR . $source;
        }
        return $this->smartFinder->find($paths, '*');
    }
}
