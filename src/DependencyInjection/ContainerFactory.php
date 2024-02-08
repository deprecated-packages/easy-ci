<?php

declare (strict_types=1);
namespace Symplify\EasyCI\DependencyInjection;

use EasyCI202402\Illuminate\Container\Container;
use EasyCI202402\Symfony\Component\Console\Application;
use EasyCI202402\Symfony\Component\Console\Input\ArrayInput;
use EasyCI202402\Symfony\Component\Console\Output\ConsoleOutput;
use EasyCI202402\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Command\CheckCommentedCodeCommand;
use Symplify\EasyCI\Command\CheckConflictsCommand;
use Symplify\EasyCI\Command\FindMultiClassesCommand;
use Symplify\EasyCI\Command\NamespaceToPSR4Command;
use Symplify\EasyCI\Command\ValidateFileLengthCommand;
use Symplify\EasyCI\Testing\Command\DetectUnitTestsCommand;
final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create() : Container
    {
        $container = new Container();
        // console
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
        });
        $container->singleton(Application::class, function (Container $container) : Application {
            $application = new Application('Easy CI toolkit');
            $commands = [$container->make(CheckCommentedCodeCommand::class), $container->make(CheckConflictsCommand::class), $container->make(ValidateFileLengthCommand::class), $container->make(DetectUnitTestsCommand::class), $container->make(FindMultiClassesCommand::class), $container->make(NamespaceToPSR4Command::class)];
            $application->addCommands($commands);
            // remove basic command to make output clear
            $this->hideDefaultCommands($application);
            return $application;
        });
        return $container;
    }
    public function hideDefaultCommands(Application $application) : void
    {
        $application->get('list')->setHidden(\true);
        $application->get('completion')->setHidden(\true);
        $application->get('help')->setHidden(\true);
    }
}
