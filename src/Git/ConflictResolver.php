<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Git;

use EasyCI202301\Nette\Utils\Strings;
use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Git\ConflictResolver\ConflictResolverTest
 */
final class ConflictResolver
{
    /**
     * @see https://regex101.com/r/iYPxCV/2
     * @var string
     */
    private const CONFLICT_REGEX = '#^<<<<<<<#';
    /**
     * @api
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo) : int
    {
        $conflictsMatch = Strings::matchAll($fileInfo->getContents(), self::CONFLICT_REGEX);
        return \count($conflictsMatch);
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return int[]
     */
    public function extractFromFileInfos(array $fileInfos) : array
    {
        $conflictCountsByFilePath = [];
        foreach ($fileInfos as $fileInfo) {
            $conflictCount = $this->extractFromFileInfo($fileInfo);
            if ($conflictCount === 0) {
                continue;
            }
            // test fixtures, that should be ignored
            if (\strpos($fileInfo->getRealPath(), '/tests/Git/ConflictResolver/Fixture') !== \false) {
                continue;
            }
            $conflictCountsByFilePath[$fileInfo->getRelativeFilePathFromCwd()] = $conflictCount;
        }
        return $conflictCountsByFilePath;
    }
}
