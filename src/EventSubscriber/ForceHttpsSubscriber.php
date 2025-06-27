<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForceHttpsSubscriber implements EventSubscriberInterface
{
    private string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($this->env === 'dev') {
            return;
        }

        $request = $event->getRequest();
        $host = $request->getHost();
        $uri = $request->getRequestUri();

        $proto = $request->headers->get('X-Forwarded-Proto');
        $isHttps = $proto === 'https';

        $targetHost = $host;
        $shouldRedirect = false;

        if (!$isHttps) {
            $shouldRedirect = true;
        }

        if ($host === 'ecoride-app.fr') {
            $targetHost = 'www.ecoride-app.fr';
            $shouldRedirect = true;
        }

        if ($shouldRedirect) {
            $redirectUrl = 'https://' . $targetHost . $uri;
            $event->setResponse(new RedirectResponse($redirectUrl, 301));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }
}
