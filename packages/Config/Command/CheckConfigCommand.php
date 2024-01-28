<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Config\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\ValueObject\ConfigFileSuffixes;

final class CheckConfigCommand extends Command
{
    public function __construct(
        private readonly ClassAndConstantExistanceFileProcessor $classAndConstantExistanceFileProcessor,
        private readonly FileErrorsReporter $fileErrorsReporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-config');

        $this->setDescription('Check YAML configs for existing classes and class constants');
        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directories or files to check'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument('sources');
        $fileInfos = $this->smartFinder->find($sources, ConfigFileSuffixes::provideRegex(), ['Fixture']);

        $message = sprintf(
            'Checking %d files with "%s" suffixes',
            count($fileInfos),
            implode('", "', ConfigFileSuffixes::SUFFIXES)
        );
        $this->symfonyStyle->note($message);

        $fileErrors = $this->classAndConstantExistanceFileProcessor->processFileInfos($fileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}
