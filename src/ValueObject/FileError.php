<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
final class FileError implements FileErrorInterface
{
    /**
     * @readonly
     * @var string
     */
    private $errorMessage;
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $smartFileInfo;
    public function __construct(string $errorMessage, SmartFileInfo $smartFileInfo)
    {
        $this->errorMessage = $errorMessage;
        $this->smartFileInfo = $smartFileInfo;
    }
    public function getErrorMessage() : string
    {
        return $this->errorMessage;
    }
    public function getRelativeFilePath() : string
    {
        return $this->smartFileInfo->getRelativeFilePath();
    }
}
