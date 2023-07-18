<?php

declare (strict_types=1);
namespace EasyCI202307\Symplify\SmartFileSystem\Finder;

use EasyCI202307\Symfony\Component\Finder\Finder;
use EasyCI202307\Symplify\SmartFileSystem\FileSystemFilter;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 * @see \Symplify\SmartFileSystem\Tests\Finder\SmartFinder\SmartFinderTest
 */
final class SmartFinder
{
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\Finder\FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\FileSystemFilter
     */
    private $fileSystemFilter;
    public function __construct(FinderSanitizer $finderSanitizer, FileSystemFilter $fileSystemFilter)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileSystemFilter = $fileSystemFilter;
    }
    /**
     * @param string[] $directoriesOrFiles
     * @return SmartFileInfo[]
     */
    public function findPaths(array $directoriesOrFiles, string $path) : array
    {
        $directories = $this->fileSystemFilter->filterDirectories($directoriesOrFiles);
        $fileInfos = [];
        if ($directories !== []) {
            $finder = new Finder();
            $finder->name('*')->in($directories)->path($path)->files()->sortByName();
            $fileInfos = $this->finderSanitizer->sanitize($finder);
        }
        return $fileInfos;
    }
    /**
     * @param string[] $directoriesOrFiles
     * @param string[] $excludedDirectories
     * @return SmartFileInfo[]
     */
    public function find(array $directoriesOrFiles, string $name, array $excludedDirectories = []) : array
    {
        $directories = $this->fileSystemFilter->filterDirectories($directoriesOrFiles);
        $fileInfos = [];
        if ($directories !== []) {
            $finder = new Finder();
            $finder->name($name)->in($directories)->files()->sortByName();
            if ($excludedDirectories !== []) {
                $finder->exclude($excludedDirectories);
            }
            $fileInfos = $this->finderSanitizer->sanitize($finder);
        }
        $files = $this->fileSystemFilter->filterFiles($directoriesOrFiles);
        foreach ($files as $file) {
            $fileInfos[] = new SmartFileInfo($file);
        }
        return $fileInfos;
    }
}
