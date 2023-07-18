<?php

declare (strict_types=1);
namespace EasyCI202307;

use EasyCI202307\Symfony\Component\Console\Application;
use EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor;
use Symplify\EasyCI\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer;
use Symplify\EasyCI\Config\ConfigFileAnalyzer\NonExistingClassConstantConfigFileAnalyzer;
use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Console\EasyCIApplication;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;
use Symplify\EasyCI\Twig\TwigTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202307\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI202307\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Config/EasyCIConfig.php']);
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../packages')->exclude([__DIR__ . '/../packages/Psr4/ValueObject']);
    // for autowired commands
    $services->alias(Application::class, EasyCIApplication::class);
    $services->set(ClassLikeExistenceChecker::class);
    // tagged services
    $services->set(NonExistingClassConstantConfigFileAnalyzer::class)->tag(ConfigFileAnalyzerInterface::class);
    $services->set(NonExistingClassConfigFileAnalyzer::class)->tag(ConfigFileAnalyzerInterface::class);
    $services->set(ClassAndConstantExistanceFileProcessor::class)->args([tagged_iterator(ConfigFileAnalyzerInterface::class)]);
    $services->set(ConstantPathTwigTemplateAnalyzer::class)->tag(TwigTemplateAnalyzerInterface::class);
    $services->set(MissingClassConstantTwigAnalyzer::class)->tag(TwigTemplateAnalyzerInterface::class);
    $services->set(TwigTemplateProcessor::class)->args([tagged_iterator(TwigTemplateAnalyzerInterface::class)]);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::EXCLUDED_CHECK_PATHS, []);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
};
