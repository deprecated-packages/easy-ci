<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\DataCollector;

use EasyCI202307\Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpFoundation\RequestStack;
use EasyCI202307\Symfony\Component\HttpFoundation\Response;
use EasyCI202307\Symfony\Component\VarDumper\Cloner\Data;
use EasyCI202307\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use EasyCI202307\Symfony\Contracts\Service\ResetInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see TraceableEventDispatcher
 *
 * @final
 */
class EventDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|null
     */
    private $dispatcher;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack|null
     */
    private $requestStack;
    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $currentRequest;
    public function __construct(EventDispatcherInterface $dispatcher = null, RequestStack $requestStack = null)
    {
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
    }
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->currentRequest = $this->requestStack && $this->requestStack->getMainRequest() !== $request ? $request : null;
        $this->data = ['called_listeners' => [], 'not_called_listeners' => [], 'orphaned_events' => []];
    }
    public function reset()
    {
        $this->data = [];
        if ($this->dispatcher instanceof ResetInterface) {
            $this->dispatcher->reset();
        }
    }
    public function lateCollect()
    {
        if ($this->dispatcher instanceof TraceableEventDispatcher) {
            $this->setCalledListeners($this->dispatcher->getCalledListeners($this->currentRequest));
            $this->setNotCalledListeners($this->dispatcher->getNotCalledListeners($this->currentRequest));
            $this->setOrphanedEvents($this->dispatcher->getOrphanedEvents($this->currentRequest));
        }
        $this->data = $this->cloneVar($this->data);
    }
    /**
     * @see TraceableEventDispatcher
     */
    public function setCalledListeners(array $listeners)
    {
        $this->data['called_listeners'] = $listeners;
    }
    /**
     * @see TraceableEventDispatcher
     * @return mixed[]|\Symfony\Component\VarDumper\Cloner\Data
     */
    public function getCalledListeners()
    {
        return $this->data['called_listeners'];
    }
    /**
     * @see TraceableEventDispatcher
     */
    public function setNotCalledListeners(array $listeners)
    {
        $this->data['not_called_listeners'] = $listeners;
    }
    /**
     * @see TraceableEventDispatcher
     * @return mixed[]|\Symfony\Component\VarDumper\Cloner\Data
     */
    public function getNotCalledListeners()
    {
        return $this->data['not_called_listeners'];
    }
    /**
     * @param array $events An array of orphaned events
     *
     * @see TraceableEventDispatcher
     */
    public function setOrphanedEvents(array $events)
    {
        $this->data['orphaned_events'] = $events;
    }
    /**
     * @see TraceableEventDispatcher
     * @return mixed[]|\Symfony\Component\VarDumper\Cloner\Data
     */
    public function getOrphanedEvents()
    {
        return $this->data['orphaned_events'];
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return 'events';
    }
}
