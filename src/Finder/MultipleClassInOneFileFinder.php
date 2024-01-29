<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Finder;

use Symplify\EasyCI\RobotLoader\PhpClassLoader;
final class MultipleClassInOneFileFinder
{
    /**
     * @readonly
     * @var \Symplify\EasyCI\RobotLoader\PhpClassLoader
     */
    private $phpClassLoader;
    public function __construct(PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;
    }
    /**
     * @param string[] $directories
     * @return string[][]
     */
    public function findInDirectories(array $directories) : array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);
        $classesByFile = [];
        foreach ($fileByClasses as $class => $file) {
            $classesByFile[$file][] = $class;
        }
        return \array_filter($classesByFile, static function (array $classes) : bool {
            return \count($classes) >= 2;
        });
    }
}
