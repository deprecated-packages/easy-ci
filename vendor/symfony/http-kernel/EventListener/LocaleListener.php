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

use EasyCI202307\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpFoundation\RequestStack;
use EasyCI202307\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use EasyCI202307\Symfony\Component\HttpKernel\Event\KernelEvent;
use EasyCI202307\Symfony\Component\HttpKernel\Event\RequestEvent;
use EasyCI202307\Symfony\Component\HttpKernel\KernelEvents;
use EasyCI202307\Symfony\Component\Routing\RequestContextAwareInterface;
/**
 * Initializes the locale based on the current request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Routing\RequestContextAwareInterface|null
     */
    private $router;
    /**
     * @var string
     */
    private $defaultLocale;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    /**
     * @var bool
     */
    private $useAcceptLanguageHeader;
    /**
     * @var mixed[]
     */
    private $enabledLocales;
    public function __construct(RequestStack $requestStack, string $defaultLocale = 'en', RequestContextAwareInterface $router = null, bool $useAcceptLanguageHeader = \false, array $enabledLocales = [])
    {
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->useAcceptLanguageHeader = $useAcceptLanguageHeader;
        $this->enabledLocales = $enabledLocales;
    }
    public function setDefaultLocale(KernelEvent $event)
    {
        $event->getRequest()->setDefaultLocale($this->defaultLocale);
    }
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $this->setLocale($request);
        $this->setRouterContext($request);
    }
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        if (null !== ($parentRequest = $this->requestStack->getParentRequest())) {
            $this->setRouterContext($parentRequest);
        }
    }
    private function setLocale(Request $request)
    {
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        } elseif ($this->useAcceptLanguageHeader) {
            if ($preferredLanguage = $request->getPreferredLanguage($this->enabledLocales)) {
                $request->setLocale($preferredLanguage);
            }
            $request->attributes->set('_vary_by_language', \true);
        }
    }
    private function setRouterContext(Request $request)
    {
        (($router = $this->router) ? $router->getContext() : null)->setParameter('_locale', $request->getLocale());
    }
    public static function getSubscribedEvents() : array
    {
        return [KernelEvents::REQUEST => [
            ['setDefaultLocale', 100],
            // must be registered after the Router to have access to the _locale
            ['onKernelRequest', 16],
        ], KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]]];
    }
}
