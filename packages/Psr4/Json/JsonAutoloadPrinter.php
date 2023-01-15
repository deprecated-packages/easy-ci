<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Psr4\Json;

use Nette\Utils\Json;
use Symplify\EasyCI\Psr4\FileSystem\Psr4PathNormalizer;
use Symplify\EasyCI\Psr4\ValueObject\Psr4NamespaceToPaths;

final class JsonAutoloadPrinter
{
    public function __construct(
        private readonly Psr4PathNormalizer $psr4PathNormalizer
    ) {
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespaceToPaths
     */
    public function createJsonAutoloadContent(array $psr4NamespaceToPaths): string
    {
        $normalizedJsonArray = $this->psr4PathNormalizer->normalizePsr4NamespaceToPathsToJsonsArray(
            $psr4NamespaceToPaths
        );

        $composerJson = [
            'autoload' => [
                'psr-4' => $normalizedJsonArray,
            ],
        ];

        return Json::encode($composerJson, Json::PRETTY);
    }
}
