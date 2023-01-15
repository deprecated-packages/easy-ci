<?php

declare(strict_types=1);

use Symplify\EasyCI\Config\EasyCIConfig;
use Symplify\EasyCI\Contract\Application\FileProcessorInterface;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;
use Symplify\EasyCI\ValueObject\ConfigFileSuffixes;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/packages',
        __DIR__ . '/config',
    ]);

    $easyCIConfig->typesToSkip([
        EasyCIConfig::class,
        FileProcessorInterface::class,
        ConstantPathTwigTemplateAnalyzer::class,
        MissingClassConstantTwigAnalyzer::class,
        ConfigFileSuffixes::class,
    ]);
};
