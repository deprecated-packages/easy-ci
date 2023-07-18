<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\EventDispatcher;

/**
 * A read-only proxy for an event dispatcher.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImmutableEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    public function dispatch(object $event, string $eventName = null) : object
    {
        return $this->dispatcher->dispatch($event, $eventName);
    }
    /**
     * @return never
     * @param callable|mixed[] $listener
     */
    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * @return never
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * @return never
     * @param callable|mixed[] $listener
     */
    public function removeListener(string $eventName, $listener)
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * @return never
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    public function getListeners(string $eventName = null) : array
    {
        return $this->dispatcher->getListeners($eventName);
    }
    /**
     * @param callable|mixed[] $listener
     */
    public function getListenerPriority(string $eventName, $listener) : ?int
    {
        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }
    public function hasListeners(string $eventName = null) : bool
    {
        return $this->dispatcher->hasListeners($eventName);
    }
}
