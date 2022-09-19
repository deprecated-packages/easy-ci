<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI202209\Nette\Utils\Json;
use EasyCI202209\Symfony\Component\Console\Input\InputArgument;
use EasyCI202209\Symfony\Component\Console\Input\InputInterface;
use EasyCI202209\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Composer\SupportedPhpVersionResolver;
use Symplify\EasyCI\Exception\ShouldNotHappenException;
use EasyCI202209\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class PhpVersionsJsonCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const COMPOSER_JSON_FILE_PATH = 'composer_json_file_path';
    /**
     * @var \Symplify\EasyCI\Composer\SupportedPhpVersionResolver
     */
    private $supportedPhpVersionResolver;
    public function __construct(SupportedPhpVersionResolver $supportedPhpVersionResolver)
    {
        $this->supportedPhpVersionResolver = $supportedPhpVersionResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('php-versions-json');
        $this->addArgument(self::COMPOSER_JSON_FILE_PATH, InputArgument::OPTIONAL, 'Path to composer.json', \getcwd() . '/composer.json');
        $this->setDescription('Generate supported PHP versions based on `composer.json` in JSON format. Useful for PHP matrix build in CI');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $composerJsonFilePath = (string) $input->getArgument(self::COMPOSER_JSON_FILE_PATH);
        $this->fileSystemGuard->ensureFileExists($composerJsonFilePath, __METHOD__);
        $supportedPhpVersions = $this->supportedPhpVersionResolver->resolveFromComposerJsonFilePath($composerJsonFilePath);
        if ($supportedPhpVersions === []) {
            $message = \sprintf('No PHP versions were resolved from "%s"', $composerJsonFilePath);
            throw new ShouldNotHappenException($message);
        }
        // output must be without spaces, otherwise it breaks the GitHub Actions json
        $jsonContent = Json::encode($supportedPhpVersions);
        $this->symfonyStyle->writeln($jsonContent);
        return self::SUCCESS;
    }
}
