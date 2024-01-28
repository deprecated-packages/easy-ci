<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ValueObject;

use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;

final class FileError implements FileErrorInterface
{
    public function __construct(
        private readonly string $errorMessage,
        private readonly string $filePath
    ) {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getRelativeFilePath(): string
    {
        return $this->filePath;
    }
}
