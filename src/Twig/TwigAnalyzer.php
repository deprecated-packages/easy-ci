<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Twig;

use Symplify\EasyCI\Template\RenderMethodTemplateExtractor;
use EasyCI202307\Symplify\SmartFileSystem\SmartFileInfo;
final class TwigAnalyzer
{
    /**
     * @readonly
     * @var \Symplify\EasyCI\Template\RenderMethodTemplateExtractor
     */
    private $renderMethodTemplateExtractor;
    public function __construct(RenderMethodTemplateExtractor $renderMethodTemplateExtractor)
    {
        $this->renderMethodTemplateExtractor = $renderMethodTemplateExtractor;
    }
    /**
     * @param SmartFileInfo[] $controllerFileInfos
     * @param string[] $allowedTemplatePaths
     * @return string[]
     */
    public function analyzeFileInfos(array $controllerFileInfos, array $allowedTemplatePaths) : array
    {
        $usedTemplatePathsByControllerPath = $this->renderMethodTemplateExtractor->extractFromFileInfos($controllerFileInfos);
        $errorMessages = [];
        foreach ($usedTemplatePathsByControllerPath as $relativeControllerFilePath => $usedTemplatePaths) {
            foreach ($usedTemplatePaths as $usedTemplatePath) {
                if (\in_array($usedTemplatePath, $allowedTemplatePaths, \true)) {
                    continue;
                }
                $errorMessages[] = \sprintf('Template reference "%s" used in "%s" controller was not found in existing templates', $usedTemplatePath, $relativeControllerFilePath);
            }
        }
        return $errorMessages;
    }
}
