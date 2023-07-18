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

use EasyCI202307\Symfony\Contracts\Service\ResetInterface;
/**
 * Resets provided services.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class ServicesResetter implements ResetInterface
{
    /**
     * @var \Traversable
     */
    private $resettableServices;
    /**
     * @var mixed[]
     */
    private $resetMethods;
    /**
     * @param \Traversable<string, object>   $resettableServices
     * @param array<string, string|string[]> $resetMethods
     */
    public function __construct(\Traversable $resettableServices, array $resetMethods)
    {
        $this->resettableServices = $resettableServices;
        $this->resetMethods = $resetMethods;
    }
    public function reset()
    {
        foreach ($this->resettableServices as $id => $service) {
            foreach ((array) $this->resetMethods[$id] as $resetMethod) {
                if ('?' === $resetMethod[0] && !\method_exists($service, $resetMethod = \substr($resetMethod, 1))) {
                    continue;
                }
                $service->{$resetMethod}();
            }
        }
    }
}
