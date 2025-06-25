<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForceHttpsSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $host = $request->getHost();
        $isSecure = $request->isSecure();

        $shouldRedirect = false;
        $targetHost = $host;

        if (!$isSecure) {
            $shouldRedirect = true;
        }

        if ($host === 'ecoride-app.fr') {
            $targetHost = 'www.ecoride-app.fr';
            $shouldRedirect = true;
        }

        if ($shouldRedirect) {
            $uri = $request->getRequestUri();
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
