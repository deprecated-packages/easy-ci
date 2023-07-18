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

use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpFoundation\Response;
use EasyCI202307\Symfony\Component\HttpKernel\KernelInterface;
use EasyCI202307\Symfony\Component\Stopwatch\Stopwatch;
use EasyCI202307\Symfony\Component\Stopwatch\StopwatchEvent;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class TimeDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface|null
     */
    private $kernel;
    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch|null
     */
    private $stopwatch;
    public function __construct(KernelInterface $kernel = null, Stopwatch $stopwatch = null)
    {
        $this->kernel = $kernel;
        $this->stopwatch = $stopwatch;
    }
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        if (null !== $this->kernel) {
            $startTime = $this->kernel->getStartTime();
        } else {
            $startTime = $request->server->get('REQUEST_TIME_FLOAT');
        }
        $this->data = ['token' => $request->attributes->get('_stopwatch_token'), 'start_time' => $startTime * 1000, 'events' => [], 'stopwatch_installed' => \class_exists(Stopwatch::class, \false)];
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
        ($stopwatch = $this->stopwatch) ? $stopwatch->reset() : null;
    }
    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        if (null !== $this->stopwatch && isset($this->data['token'])) {
            $this->setEvents($this->stopwatch->getSectionEvents($this->data['token']));
        }
        unset($this->data['token']);
    }
    /**
     * @param StopwatchEvent[] $events The request events
     */
    public function setEvents(array $events)
    {
        foreach ($events as $event) {
            $event->ensureStopped();
        }
        $this->data['events'] = $events;
    }
    /**
     * @return StopwatchEvent[]
     */
    public function getEvents() : array
    {
        return $this->data['events'];
    }
    /**
     * Gets the request elapsed time.
     */
    public function getDuration() : float
    {
        if (!isset($this->data['events']['__section__'])) {
            return 0;
        }
        $lastEvent = $this->data['events']['__section__'];
        return $lastEvent->getOrigin() + $lastEvent->getDuration() - $this->getStartTime();
    }
    /**
     * Gets the initialization time.
     *
     * This is the time spent until the beginning of the request handling.
     */
    public function getInitTime() : float
    {
        if (!isset($this->data['events']['__section__'])) {
            return 0;
        }
        return $this->data['events']['__section__']->getOrigin() - $this->getStartTime();
    }
    public function getStartTime() : float
    {
        return $this->data['start_time'];
    }
    public function isStopwatchInstalled() : bool
    {
        return $this->data['stopwatch_installed'];
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return 'time';
    }
}
