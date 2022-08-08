<?php

declare (strict_types=1);
namespace Symplify\EasyCI\SymfonyNameToTypeService;

use EasyCI202208\Nette\Utils\FileSystem;
use EasyCI202208\Nette\Utils\Strings;
use EasyCI202208\Symfony\Component\Finder\SplFileInfo;
use EasyCI202208\Webmozart\Assert\Assert;
/**
 * @see \Symplify\EasyCI\Tests\SymfonyNameToTypeService\NameToTypeServiceReplacer\NameToTypeServiceReplacerTest
 */
final class NameToTypeServiceReplacer
{
    /**
     * @param SplFileInfo[] $configFileInfos
     * @param array<string, string> $serviceTypesByName
     */
    public function replaceInFileInfos(array $configFileInfos, array $serviceTypesByName) : int
    {
        Assert::allIsInstanceOf($configFileInfos, SplFileInfo::class);
        $changedFilesCount = 0;
        foreach ($configFileInfos as $configFileInfo) {
            $originalFileContents = $configFileInfo->getContents();
            $changedFileContents = $this->replaceInFileInfo($configFileInfo, $serviceTypesByName);
            if ($changedFileContents === $originalFileContents) {
                continue;
            }
            ++$changedFilesCount;
            // change file contents
            FileSystem::write($configFileInfo->getPathname(), $changedFileContents);
        }
        return $changedFilesCount;
    }
    /**
     * @param array<string, string> $serviceMap
     */
    public function replaceInFileInfo(SplFileInfo $configFileInfo, array $serviceMap) : string
    {
        $yamlContents = $configFileInfo->getContents();
        // replace service string-names with type-names
        foreach ($serviceMap as $serviceName => $serviceType) {
            // skip command and controller
            if (\substr_compare($serviceType, 'Command', -\strlen('Command')) === 0) {
                continue;
            }
            if (\substr_compare($serviceName, 'Controller', -\strlen('Controller')) === 0) {
                continue;
            }
            $regexesToReplaces = $this->createRegexesToReplaces($serviceName, $serviceType);
            foreach ($regexesToReplaces as $regexPattern => $replace) {
                $yamlContents = Strings::replace($yamlContents, $regexPattern, $replace);
            }
        }
        return $yamlContents;
    }
    /**
     * @return array<string, string>
     */
    private function createRegexesToReplaces(string $serviceName, string $serviceType) : array
    {
        $regexesToReplaces = [];
        // double slashed is needed to keep in quoted string
        $lowercasedServiceType = \strtolower($serviceType);
        $singleQuotedServiceType = \preg_quote($lowercasedServiceType);
        $doubleQuotedServiceType = \preg_quote($singleQuotedServiceType);
        // A. service name
        $desiredPattern = '#\\b(' . \preg_quote($serviceName, '#') . '):#ms';
        $newTypeName = $singleQuotedServiceType . ':';
        $regexesToReplaces[$desiredPattern] = $newTypeName;
        // B. service alias
        $quotedNamePattern = '#"(' . \preg_quote($serviceName, '#') . ')"#ms';
        // double slashed is needed to keep in quoted string
        $newQuotedName = '"' . $doubleQuotedServiceType . '"';
        $regexesToReplaces[$quotedNamePattern] = $newQuotedName;
        // C. service reference
        $nameReferencePattern = '#@(' . \preg_quote($serviceName, '#') . ')#ms';
        $newReferencedType = '@' . $doubleQuotedServiceType;
        $regexesToReplaces[$nameReferencePattern] = $newReferencedType;
        return $regexesToReplaces;
    }
}