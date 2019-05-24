<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class ResponseMetadataPopulator
 * @package App\EventListener
 */
class ResponseMetadataPopulator
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
