<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ProjectFilesFinder
{
    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public function find(array $sources): array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = getcwd() . DIRECTORY_SEPARATOR . $source;
        }

        $finder = Finder::create()
            ->files()
            ->in($paths)
            ->sortByName();

        return $finder->getIterator()
            ->getArrayCopy();
    }
}
