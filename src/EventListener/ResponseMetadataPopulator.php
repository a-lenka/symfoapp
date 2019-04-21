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
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('X-Target-URL', $event->getRequest()->getRequestUri());
    }
}
