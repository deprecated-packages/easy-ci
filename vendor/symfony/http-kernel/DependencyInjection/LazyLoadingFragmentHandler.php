<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\DependencyInjection;

use EasyCI202307\Psr\Container\ContainerInterface;
use EasyCI202307\Symfony\Component\HttpFoundation\RequestStack;
use EasyCI202307\Symfony\Component\HttpKernel\Controller\ControllerReference;
use EasyCI202307\Symfony\Component\HttpKernel\Fragment\FragmentHandler;
/**
 * Lazily loads fragment renderers from the dependency injection container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LazyLoadingFragmentHandler extends FragmentHandler
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;
    /**
     * @var array<string, bool>
     */
    private $initialized = [];
    public function __construct(ContainerInterface $container, RequestStack $requestStack, bool $debug = \false)
    {
        $this->container = $container;
        parent::__construct($requestStack, [], $debug);
    }
    /**
     * {@inheritdoc}
     * @param string|\Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     */
    public function render($uri, string $renderer = 'inline', array $options = []) : ?string
    {
        if (!isset($this->initialized[$renderer]) && $this->container->has($renderer)) {
            $this->addRenderer($this->container->get($renderer));
            $this->initialized[$renderer] = \true;
        }
        return parent::render($uri, $renderer, $options);
    }
}
