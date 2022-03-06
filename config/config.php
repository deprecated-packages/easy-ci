<?php

declare (strict_types=1);
namespace EasyCI20220306;

use EasyCI20220306\Composer\Semver\Semver;
use EasyCI20220306\Composer\Semver\VersionParser;
use EasyCI20220306\Nette\Neon\Decoder;
use EasyCI20220306\PhpParser\NodeFinder;
use EasyCI20220306\PhpParser\Parser;
use EasyCI20220306\PhpParser\ParserFactory;
use EasyCI20220306\PhpParser\PrettyPrinter\Standard;
use EasyCI20220306\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\Console\EasyCIApplication;
use EasyCI20220306\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function EasyCI20220306\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/config-packages.php');
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // for autowired commands
    $services->alias(\EasyCI20220306\Symfony\Component\Console\Application::class, \Symplify\EasyCI\Console\EasyCIApplication::class);
    $services->set(\EasyCI20220306\Composer\Semver\VersionParser::class);
    $services->set(\EasyCI20220306\Composer\Semver\Semver::class);
    // neon
    $services->set(\EasyCI20220306\Nette\Neon\Decoder::class);
    // php-parser
    $services->set(\EasyCI20220306\PhpParser\ParserFactory::class);
    $services->set(\EasyCI20220306\PhpParser\Parser::class)->factory([\EasyCI20220306\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220306\PhpParser\ParserFactory::class), 'create'])->args([\EasyCI20220306\PhpParser\ParserFactory::PREFER_PHP7]);
    $services->set(\EasyCI20220306\PhpParser\PrettyPrinter\Standard::class);
    $services->set(\EasyCI20220306\PhpParser\NodeFinder::class);
    $services->set(\EasyCI20220306\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
};
