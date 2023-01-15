<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter;
use Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder;
use Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Webmozart\Assert\Assert;

final class CheckActiveClassCommand extends Command
{
    public function __construct(
        private readonly ClassNamesFinder $classNamesFinder,
        private readonly UseImportsResolver $useImportsResolver,
        private readonly PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter,
        private readonly UnusedClassReporter $unusedClassReporter,
        private readonly SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-active-class');
        $this->setDescription('Check classes that are not used in any config and in the code');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phpFileInfos = $this->findPhpFiles($input);

        $phpFilesCount = is_countable($phpFileInfos) ? count($phpFileInfos) : 0;
        $this->symfonyStyle->progressStart($phpFilesCount);

        $usedNames = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFileInfo);
            $usedNames = array_merge($usedNames, $currentUsedNames);

            $this->symfonyStyle->progressAdvance();
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFileInfos);

        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames
        );

        return $this->unusedClassReporter->reportResult($possiblyUnusedFilesWithClasses, $existingFilesWithClasses);
    }

    ///**
    // * @return string[]
    // */
    //private function findPhpFiles(InputInterface $input): array
    //{
    //    $excludedCheckPaths = $this->parameterProvider->provideArrayParameter(Option::EXCLUDED_CHECK_PATHS);
    //
    //    $paths = (array)$input->getArgument(Option::SOURCES);
    //
    //    // fallback to config paths
    //    if ($paths === []) {
    //        $paths = $this->parameterProvider->provideArrayParameter(Option::PATHS);
    //    }
    //
    //    $fileInfos = $this->smartFinder->find($paths, '*.php', $excludedCheckPaths);
    //
    //    $filePaths = array_map(
    //        fn (\SplFileInfo $fileInfo): string => $fileInfo->getRealPath(),
    //        $fileInfos
    //    );
    //
    //    Assert::allString($filePaths);
    //    return $filePaths;
    //}
}
