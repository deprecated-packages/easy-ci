<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Testing\Command;

use EasyCI202402\Nette\Utils\FileSystem;
use EasyCI202402\Symfony\Component\Console\Command\Command;
use EasyCI202402\Symfony\Component\Console\Input\InputArgument;
use EasyCI202402\Symfony\Component\Console\Input\InputInterface;
use EasyCI202402\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202402\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Testing\Printer\PHPUnitXmlPrinter;
use Symplify\EasyCI\Testing\UnitTestFilePathsFinder;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202402\Webmozart\Assert\Assert;
final class DetectUnitTestsCommand extends Command
{
    /**
     * @readonly
     * @var \Symplify\EasyCI\Testing\Printer\PHPUnitXmlPrinter
     */
    private $phpunitXmlPrinter;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Symplify\EasyCI\Testing\UnitTestFilePathsFinder
     */
    private $unitTestFilePathsFinder;
    /**
     * @var string
     */
    private const OUTPUT_FILENAME = 'phpunit-unit-files.xml';
    public function __construct(PHPUnitXmlPrinter $phpunitXmlPrinter, SymfonyStyle $symfonyStyle, UnitTestFilePathsFinder $unitTestFilePathsFinder)
    {
        $this->phpunitXmlPrinter = $phpunitXmlPrinter;
        $this->symfonyStyle = $symfonyStyle;
        $this->unitTestFilePathsFinder = $unitTestFilePathsFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('detect-unit-tests');
        $this->setDescription('Get list of tests in specific directory, that are considered "unit".
They depend only on bare PHPUnit test case, but not on KernelTestCase. Move the generated file to your phpunit.xml test group.');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to directory with tests');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        Assert::isArray($sources);
        Assert::allString($sources);
        $unitTestCasesClassesToFilePaths = $this->unitTestFilePathsFinder->findInDirectories($sources);
        if ($unitTestCasesClassesToFilePaths === []) {
            $this->symfonyStyle->note('No unit tests found in provided paths');
            return self::SUCCESS;
        }
        $filesPHPUnitXmlContents = $this->phpunitXmlPrinter->printFiles($unitTestCasesClassesToFilePaths, (string) \getcwd());
        FileSystem::write(self::OUTPUT_FILENAME, $filesPHPUnitXmlContents);
        $successMessage = \sprintf('List of %d unit tests was dumped into "%s"', \count($unitTestCasesClassesToFilePaths), self::OUTPUT_FILENAME);
        $this->symfonyStyle->success($successMessage);
        return self::SUCCESS;
    }
}
