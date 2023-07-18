<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Twig;

use EasyCI202307\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
final class TwigTemplateProcessor
{
    /**
     * @var TwigTemplateAnalyzerInterface[]
     * @readonly
     */
    private $twigTemplateAnalyzers;
    /**
     * @param RewindableGenerator<int, TwigTemplateAnalyzerInterface> $twigTemplateAnalyzers
     */
    public function __construct(iterable $twigTemplateAnalyzers)
    {
        $this->twigTemplateAnalyzers = \iterator_to_array($twigTemplateAnalyzers->getIterator());
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyzeFileInfos(array $fileInfos) : array
    {
        $templateErrors = [];
        foreach ($this->twigTemplateAnalyzers as $twigTemplateAnalyzer) {
            $currentTemplateErrors = $twigTemplateAnalyzer->analyze($fileInfos);
            $templateErrors = \array_merge($templateErrors, $currentTemplateErrors);
        }
        return $templateErrors;
    }
}
