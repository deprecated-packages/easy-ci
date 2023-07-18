<?php

declare (strict_types=1);
namespace EasyCI202307\Symplify\EasyTesting\Finder;

use EasyCI202307\Symfony\Component\Finder\Finder;
use EasyCI202307\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
final class FixtureFinder
{
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\Finder\FinderSanitizer
     */
    private $finderSanitizer;
    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @param string[] $sources
     * @return SmartFileInfo[]
     */
    public function find(array $sources) : array
    {
        $finder = new Finder();
        $finder->files()->in($sources)->name('*.php.inc')->path('Fixture')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
}
