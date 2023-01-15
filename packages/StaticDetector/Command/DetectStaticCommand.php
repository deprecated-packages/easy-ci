<?php

declare(strict_types=1);

namespace Symplify\EasyCI\StaticDetector\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use Symplify\EasyCI\StaticDetector\Output\StaticReportReporter;
use Symplify\EasyCI\StaticDetector\StaticScanner;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class DetectStaticCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private readonly StaticScanner $staticScanner,
        private readonly StaticNodeCollector $staticNodeCollector,
        private readonly StaticReportReporter $staticReportReporter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('detect-static');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more directories to detect static in'
        );
        $this->setDescription('Show what static method calls are called where');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, '*.php');

        $this->staticScanner->scanFileInfos($fileInfos);

        $this->symfonyStyle->title('Static Report');
        $staticReport = $this->staticNodeCollector->generateStaticReport();

        $this->staticReportReporter->reportStaticClassMethods($staticReport);
        $this->staticReportReporter->reportTotalNumbers($staticReport);

        return self::SUCCESS;
    }
}
