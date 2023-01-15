<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Psr4\ValueObject;

use Stringable;

final class Psr4NamespaceToPath implements Stringable
{
    public function __construct(
        private readonly string $namespace,
        private readonly string $path
    ) {
    }

    /**
     * For array_unique()
     */
    public function __toString(): string
    {
        return $this->namespace . $this->path;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
