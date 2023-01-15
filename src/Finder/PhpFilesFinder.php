<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Finder;

use SplFileInfo;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI202301\Symplify\SmartFileSystem\Finder\SmartFinder;
use EasyCI202301\Webmozart\Assert\Assert;
final class PhpFilesFinder
{
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\Finder\SmartFinder
     */
    private $smartFinder;
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(SmartFinder $smartFinder, ParameterProvider $parameterProvider)
    {
        $this->smartFinder = $smartFinder;
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @return string[]
     */
    public function findPhpFiles(InputInterface $input) : array
    {
        $excludedCheckPaths = $this->parameterProvider->provideArrayParameter(Option::EXCLUDED_CHECK_PATHS);
        $paths = (array) $input->getArgument(Option::SOURCES);
        // fallback to config paths
        if ($paths === []) {
            $paths = $this->parameterProvider->provideArrayParameter(Option::PATHS);
        }
        $fileInfos = $this->smartFinder->find($paths, '*.php', $excludedCheckPaths);
        $filePaths = \array_map(static function (SplFileInfo $fileInfo) : string {
            return $fileInfo->getRealPath();
        }, $fileInfos);
        Assert::allString($filePaths);
        return $filePaths;
    }
}
