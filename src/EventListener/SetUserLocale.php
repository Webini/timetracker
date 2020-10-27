<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class SetUserLocale
{
    /**
     * @param KernelEvent $event
     */
    public function onKernelRequest(KernelEvent $event)
    {
        $request = $event->getRequest();
        $header = $request->headers->get('accept-language');
        if (empty($header)) {
            return;
        }

        $favoriteLocale = AcceptHeader::fromString($header)->first();
        if ($favoriteLocale === null) {
            return;
        }

        $request->setLocale($favoriteLocale->getValue());
    }
}