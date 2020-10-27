<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 23/07/18
 * Time: 17:53
 */

namespace App\EventListener;


//use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class KernelResponseListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->add([
            // https://www.owasp.org/index.php/Clickjacking_Defense_Cheat_Sheet
            // le X-Frame-Options est cool pour le deny, dans le cas ou on veut un allow from, il faudra utiliser le CSP avec un moins bon support
            // pour les vieux browsers, le X-frame-options servira de fallback dans le cas où le CSP n'est pas supporté
            // 'Content-Security-Policy' => 'frame-ancestors \'none\'',
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
        ]);
    }
}