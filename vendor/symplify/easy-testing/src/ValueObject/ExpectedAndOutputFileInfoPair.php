<?php

declare (strict_types=1);
namespace EasyCI20220125\Symplify\EasyTesting\ValueObject;

use EasyCI20220125\Symplify\SmartFileSystem\SmartFileInfo;
use EasyCI20220125\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class ExpectedAndOutputFileInfoPair
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $expectedFileInfo;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $outputFileInfo;
    public function __construct(\EasyCI20220125\Symplify\SmartFileSystem\SmartFileInfo $expectedFileInfo, ?\EasyCI20220125\Symplify\SmartFileSystem\SmartFileInfo $outputFileInfo)
    {
        $this->expectedFileInfo = $expectedFileInfo;
        $this->outputFileInfo = $outputFileInfo;
    }
    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getExpectedFileContent() : string
    {
        return $this->expectedFileInfo->getContents();
    }
    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getOutputFileContent() : string
    {
        if (!$this->outputFileInfo instanceof \EasyCI20220125\Symplify\SmartFileSystem\SmartFileInfo) {
            throw new \EasyCI20220125\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->outputFileInfo->getContents();
    }
    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function doesOutputFileExist() : bool
    {
        return $this->outputFileInfo !== null;
    }
}