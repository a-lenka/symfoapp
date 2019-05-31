<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class ResponseHeaderSetter
 * @package App\EventListener
 */
class ResponseHeaderSetter
{
    /**
     * @param FilterResponseEvent $event
     */
    final public function onKernelResponse(FilterResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('X-Target-URL', $event->getRequest()->getRequestUri());
    }
}
