<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
final class LineAwareFileError implements FileErrorInterface
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
    /**
     * @readonly
     * @var int
     */
    private $line;
    public function __construct(string $errorMessage, SmartFileInfo $smartFileInfo, int $line)
    {
        $this->errorMessage = $errorMessage;
        $this->smartFileInfo = $smartFileInfo;
        $this->line = $line;
    }
    public function getErrorMessage() : string
    {
        return $this->errorMessage;
    }
    public function getRelativeFilePath() : string
    {
        $relativeFilePath = $this->smartFileInfo->getRelativeFilePath();
        return $relativeFilePath . ':' . $this->line;
    }
}
