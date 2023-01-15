<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\Command;

use EasyCI202301\Symfony\Component\Console\Command\Command;
use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202301\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter;
use Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder;
use Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\Finder\PhpFilesFinder;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI202301\Symplify\SmartFileSystem\Finder\SmartFinder;
use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
use EasyCI202301\Webmozart\Assert\Assert;
final class CheckActiveClassCommand extends Command
{
    /**
     * @readonly
     * @var \Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder
     */
    private $classNamesFinder;
    /**
     * @readonly
     * @var \Symplify\EasyCI\ActiveClass\UseImportsResolver
     */
    private $useImportsResolver;
    /**
     * @readonly
     * @var \Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter
     */
    private $possiblyUnusedClassesFilter;
    /**
     * @readonly
     * @var \Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter
     */
    private $unusedClassReporter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Symplify\EasyCI\Finder\PhpFilesFinder
     */
    private $phpFilesFinder;
    public function __construct(ClassNamesFinder $classNamesFinder, UseImportsResolver $useImportsResolver, PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter, UnusedClassReporter $unusedClassReporter, SymfonyStyle $symfonyStyle, PhpFilesFinder $phpFilesFinder)
    {
        $this->classNamesFinder = $classNamesFinder;
        $this->useImportsResolver = $useImportsResolver;
        $this->possiblyUnusedClassesFilter = $possiblyUnusedClassesFilter;
        $this->unusedClassReporter = $unusedClassReporter;
        $this->symfonyStyle = $symfonyStyle;
        $this->phpFilesFinder = $phpFilesFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-active-class');
        $this->setDescription('Check classes that are not used in any config and in the code');
        $this->addArgument(Option::SOURCES, InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'One or more paths with templates');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($input);
        $this->symfonyStyle->progressStart(\count($phpFilePaths));
        $usedNames = [];
        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = \array_merge($usedNames, $currentUsedNames);
            $this->symfonyStyle->progressAdvance();
        }
        $usedNames = \array_unique($usedNames);
        \sort($usedNames);
        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths);
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter($existingFilesWithClasses, $usedNames);
        return $this->unusedClassReporter->reportResult($possiblyUnusedFilesWithClasses, $existingFilesWithClasses);
    }
}
