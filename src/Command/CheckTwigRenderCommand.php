<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\MissingTwigTemplatePathReporter;
use Symplify\EasyCI\Template\RenderMethodTemplateExtractor;
use Symplify\EasyCI\Template\TemplatePathsResolver;
use Symplify\EasyCI\Twig\TwigAnalyzer;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class CheckTwigRenderCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Template\TemplatePathsResolver
     */
    private $templatePathsResolver;
    /**
     * @var \Symplify\EasyCI\Template\RenderMethodTemplateExtractor
     */
    private $renderMethodTemplateExtractor;
    /**
     * @var \Symplify\EasyCI\Twig\TwigAnalyzer
     */
    private $twigAnalyzer;
    /**
     * @var \Symplify\EasyCI\Console\Output\MissingTwigTemplatePathReporter
     */
    private $missingTwigTemplatePathReporter;
    public function __construct(TemplatePathsResolver $templatePathsResolver, RenderMethodTemplateExtractor $renderMethodTemplateExtractor, TwigAnalyzer $twigAnalyzer, MissingTwigTemplatePathReporter $missingTwigTemplatePathReporter)
    {
        $this->templatePathsResolver = $templatePathsResolver;
        $this->renderMethodTemplateExtractor = $renderMethodTemplateExtractor;
        $this->twigAnalyzer = $twigAnalyzer;
        $this->missingTwigTemplatePathReporter = $missingTwigTemplatePathReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-twig-render');
        $this->setDescription('Validate template paths in $this->render(...)');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project directories');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);
        $this->symfonyStyle->title('Analysing controllers and templates');
        $stats = [];
        $controllerFileInfos = $this->smartFinder->find($sources, '#Controller\\.php$#');
        $stats[] = \sprintf('%d controllers', \count($controllerFileInfos));
        $allowedTemplatePaths = $this->templatePathsResolver->resolveFromDirectories($sources);
        $stats[] = \sprintf('%d twig paths', \count($allowedTemplatePaths));
        $usedTemplatePaths = $this->renderMethodTemplateExtractor->extractFromFileInfos($controllerFileInfos);
        $stats[] = \sprintf('%d unique used templates in "$this->render()" method', \count($usedTemplatePaths));
        $this->symfonyStyle->listing($stats);
        $this->symfonyStyle->newLine(2);
        $errorMessages = $this->twigAnalyzer->analyzeFileInfos($controllerFileInfos, $allowedTemplatePaths);
        return $this->missingTwigTemplatePathReporter->report($errorMessages);
    }
}
