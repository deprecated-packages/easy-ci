<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\EventListener;

use EasyCI202307\Psr\Log\LoggerInterface;
use EasyCI202307\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpFoundation\RequestStack;
use EasyCI202307\Symfony\Component\HttpFoundation\Response;
use EasyCI202307\Symfony\Component\HttpKernel\Event\ExceptionEvent;
use EasyCI202307\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use EasyCI202307\Symfony\Component\HttpKernel\Event\RequestEvent;
use EasyCI202307\Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use EasyCI202307\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use EasyCI202307\Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use EasyCI202307\Symfony\Component\HttpKernel\Kernel;
use EasyCI202307\Symfony\Component\HttpKernel\KernelEvents;
use EasyCI202307\Symfony\Component\Routing\Exception\MethodNotAllowedException;
use EasyCI202307\Symfony\Component\Routing\Exception\NoConfigurationException;
use EasyCI202307\Symfony\Component\Routing\Exception\ResourceNotFoundException;
use EasyCI202307\Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use EasyCI202307\Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use EasyCI202307\Symfony\Component\Routing\RequestContext;
use EasyCI202307\Symfony\Component\Routing\RequestContextAwareInterface;
/**
 * Initializes the context from the request and sets request attributes based on a matching route.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @final
 */
class RouterListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Routing\Matcher\RequestMatcherInterface|\Symfony\Component\Routing\Matcher\UrlMatcherInterface
     */
    private $matcher;
    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    private $context;
    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    /**
     * @var string|null
     */
    private $projectDir;
    /**
     * @var bool
     */
    private $debug;
    /**
     * @param RequestContext|null $context The RequestContext (can be null when $matcher implements RequestContextAwareInterface)
     *
     * @throws \InvalidArgumentException
     * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface|\Symfony\Component\Routing\Matcher\RequestMatcherInterface $matcher
     */
    public function __construct($matcher, RequestStack $requestStack, RequestContext $context = null, LoggerInterface $logger = null, string $projectDir = null, bool $debug = \true)
    {
        if (null === $context && !$matcher instanceof RequestContextAwareInterface) {
            throw new \InvalidArgumentException('You must either pass a RequestContext or the matcher must implement RequestContextAwareInterface.');
        }
        $this->matcher = $matcher;
        $this->context = $context ?? $matcher->getContext();
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->projectDir = $projectDir;
        $this->debug = $debug;
    }
    private function setCurrentRequest(Request $request = null)
    {
        if (null !== $request) {
            try {
                $this->context->fromRequest($request);
            } catch (\UnexpectedValueException $e) {
                throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
            }
        }
    }
    /**
     * After a sub-request is done, we need to reset the routing context to the parent request so that the URL generator
     * operates on the correct context again.
     */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        $this->setCurrentRequest($this->requestStack->getParentRequest());
    }
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $this->setCurrentRequest($request);
        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }
        // add attributes based on the request (routing)
        try {
            // matching a request is more powerful than matching a URL path + context, so try that first
            if ($this->matcher instanceof RequestMatcherInterface) {
                $parameters = $this->matcher->matchRequest($request);
            } else {
                $parameters = $this->matcher->match($request->getPathInfo());
            }
            ($logger = $this->logger) ? $logger->info('Matched route "{route}".', ['route' => $parameters['_route'] ?? 'n/a', 'route_parameters' => $parameters, 'request_uri' => $request->getUri(), 'method' => $request->getMethod()]) : null;
            $request->attributes->add($parameters);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
        } catch (ResourceNotFoundException $e) {
            $message = \sprintf('No route found for "%s %s"', $request->getMethod(), $request->getUriForPath($request->getPathInfo()));
            if ($referer = $request->headers->get('referer')) {
                $message .= \sprintf(' (from "%s")', $referer);
            }
            throw new NotFoundHttpException($message, $e);
        } catch (MethodNotAllowedException $e) {
            $message = \sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getUriForPath($request->getPathInfo()), \implode(', ', $e->getAllowedMethods()));
            throw new MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e);
        }
    }
    public function onKernelException(ExceptionEvent $event)
    {
        if (!$this->debug || !($e = $event->getThrowable()) instanceof NotFoundHttpException) {
            return;
        }
        if ($e->getPrevious() instanceof NoConfigurationException) {
            $event->setResponse($this->createWelcomeResponse());
        }
    }
    public static function getSubscribedEvents() : array
    {
        return [KernelEvents::REQUEST => [['onKernelRequest', 32]], KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]], KernelEvents::EXCEPTION => ['onKernelException', -64]];
    }
    private function createWelcomeResponse() : Response
    {
        $version = Kernel::VERSION;
        $projectDir = \realpath((string) $this->projectDir) . \DIRECTORY_SEPARATOR;
        $docVersion = \substr(Kernel::VERSION, 0, 3);
        \ob_start();
        include \dirname(__DIR__) . '/Resources/welcome.html.php';
        return new Response(\ob_get_clean(), Response::HTTP_NOT_FOUND);
    }
}
