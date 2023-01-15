<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Finder\ProjectFilesFinder;
use Symplify\EasyCI\Resolver\TooLongFilesResolver;
use Symplify\PackageBuilder\ValueObject\Option;

final class ValidateFileLengthCommand extends Command
{
    public function __construct(
        private readonly ProjectFilesFinder $projectFilesFinder,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly TooLongFilesResolver $tooLongFilesResolver
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('validate-file-length');

        $this->setDescription('[CI] Make sure the file path length are not breaking normal Windows max length');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);

        $fileInfos = $this->projectFilesFinder->find($sources);
        $tooLongFileInfos = $this->tooLongFilesResolver->resolve($fileInfos);

        if ($tooLongFileInfos === []) {
            $message = sprintf('Checked %d files - all fit max file length', count($fileInfos));
            $this->symfonyStyle->success($message);

            return self::SUCCESS;
        }

        foreach ($tooLongFileInfos as $tooLongFileInfo) {
            $message = sprintf(
                'Paths for file "%s" has %d chars, but must be shorter than %d.',
                $tooLongFileInfo->getRealPath(),
                strlen($tooLongFileInfo->getRealPath()),
                TooLongFilesResolver::MAX_FILE_LENGTH
            );

            $this->symfonyStyle->warning($message);
        }

        return self::FAILURE;
    }
}
