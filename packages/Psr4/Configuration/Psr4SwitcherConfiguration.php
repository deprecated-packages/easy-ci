<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Psr4\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCI\Psr4\Exception\ConfigurationException;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;

final class Psr4SwitcherConfiguration
{
    /**
     * @var string[]
     */
    private array $source = [];

    private ?string $composerJsonPath = null;

    public function __construct(
        private readonly FileSystemGuard $fileSystemGuard
    ) {
    }

    /**
     * @api
     * For testing
     */
    public function loadForTest(string $composerJsonPath): void
    {
        $this->composerJsonPath = $composerJsonPath;
    }

    public function loadFromInput(InputInterface $input): void
    {
        $composerJsonPath = (string) $input->getOption(Option::COMPOSER_JSON);
        if ($composerJsonPath === '') {
            throw new ConfigurationException(sprintf('Provide composer.json via "--%s"', Option::COMPOSER_JSON));
        }

        $this->fileSystemGuard->ensureFileExists($composerJsonPath, __METHOD__);

        $this->composerJsonPath = $composerJsonPath;
        $this->source = (array) $input->getArgument(Option::SOURCES);
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->source;
    }

    public function getComposerJsonPath(): string
    {
        if ($this->composerJsonPath === null) {
            throw new ConfigurationException();
        }

        return $this->composerJsonPath;
    }
}
