<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\Output;

use EasyCI202301\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticReport;
final class StaticReportReporter
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    public function reportStaticClassMethods(StaticReport $staticReport) : void
    {
        $i = 1;
        foreach ($staticReport->getStaticClassMethodsWithStaticCalls() as $staticClassMethodWithStaticCalls) {
            // report static call name
            $message = \sprintf('<options=bold>%d) %s</>', $i, $staticClassMethodWithStaticCalls->getStaticClassMethodName());
            $this->symfonyStyle->writeln($message);
            // report file location
            $message = $staticClassMethodWithStaticCalls->getStaticCallFileLocationWithLine();
            $this->symfonyStyle->writeln($message);
            ++$i;
            // report usages
            if ($staticClassMethodWithStaticCalls->getStaticCalls() !== []) {
                $this->symfonyStyle->newLine();
                $this->symfonyStyle->writeln('Static calls in the code:');
                $this->symfonyStyle->listing($staticClassMethodWithStaticCalls->getStaticCallsFilePathsWithLines());
            } else {
                $this->symfonyStyle->warning('No static calls in the code... maybe in templates?');
            }
            $this->symfonyStyle->newLine(2);
        }
    }
    public function reportTotalNumbers(StaticReport $staticReport) : void
    {
        $this->symfonyStyle->title('Static Overview');
        if ($staticReport->getStaticClassMethodCount() === 0) {
            $this->symfonyStyle->success('No static class methods and static calls found. Are you sure this tool is working? ;)');
            return;
        }
        $staticMethodsMessage = \sprintf('* %d static methods', $staticReport->getStaticClassMethodCount());
        $this->symfonyStyle->writeln($staticMethodsMessage);
        $staticCallsMessage = \sprintf('* %d static calls', $staticReport->getStaticCallsCount());
        $this->symfonyStyle->writeln($staticCallsMessage);
        $this->symfonyStyle->newLine();
    }
}
