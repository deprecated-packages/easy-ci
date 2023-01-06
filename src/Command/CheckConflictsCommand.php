<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Git\ConflictResolver;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI202301\Symplify\PackageBuilder\ValueObject\Option;
final class CheckConflictsCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Git\ConflictResolver
     */
    private $conflictResolver;
    public function __construct(ConflictResolver $conflictResolver)
    {
        $this->conflictResolver = $conflictResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-conflicts');
        $this->setDescription('Check files for missed git conflicts');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($source, '*', ['vendor']);
        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($fileInfos);
        if ($conflictsCountByFilePath === []) {
            $message = \sprintf('No conflicts found in %d files', \count($fileInfos));
            $this->symfonyStyle->success($message);
            return self::SUCCESS;
        }
        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = \sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
        }
        return self::FAILURE;
    }
}
