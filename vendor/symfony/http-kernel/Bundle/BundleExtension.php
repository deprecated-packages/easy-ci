<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\Bundle;

use EasyCI202307\Symfony\Component\Config\Definition\Configuration;
use EasyCI202307\Symfony\Component\Config\Definition\ConfigurationInterface;
use EasyCI202307\Symfony\Component\DependencyInjection\ContainerBuilder;
use EasyCI202307\Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface;
use EasyCI202307\Symfony\Component\DependencyInjection\Extension\Extension;
use EasyCI202307\Symfony\Component\DependencyInjection\Extension\ExtensionTrait;
use EasyCI202307\Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use EasyCI202307\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @internal
 */
class BundleExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface
     */
    private $subject;
    /**
     * @var string
     */
    private $alias;
    use ExtensionTrait;
    public function __construct(ConfigurableExtensionInterface $subject, string $alias)
    {
        $this->subject = $subject;
        $this->alias = $alias;
    }
    public function getConfiguration(array $config, ContainerBuilder $container) : ?ConfigurationInterface
    {
        return new Configuration($this->subject, $container, $this->getAlias());
    }
    public function getAlias() : string
    {
        return $this->alias;
    }
    public function prepend(ContainerBuilder $container) : void
    {
        $callback = function (ContainerConfigurator $configurator) use($container) {
            $this->subject->prependExtension($configurator, $container);
        };
        $this->executeConfiguratorCallback($container, $callback, $this->subject);
    }
    public function load(array $configs, ContainerBuilder $container) : void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $callback = function (ContainerConfigurator $configurator) use($config, $container) {
            $this->subject->loadExtension($config, $configurator, $container);
        };
        $this->executeConfiguratorCallback($container, $callback, $this->subject);
    }
}
