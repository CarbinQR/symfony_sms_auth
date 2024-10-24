<?php

namespace App\EventListener\Auth;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsEventListener(event: 'kernel.controller', method: 'onKernelController')]
class RateLimitListener
{
    private RateLimiterFactory $anonymousApiLimiter;

    public function __construct(RateLimiterFactory $anonymousApiLimiter)
    {
        $this->anonymousApiLimiter = $anonymousApiLimiter;
    }

    /**
     * Limit the number of requests from one IP to a single route. It might be preferable to move this to middleware
     * and combine parameters for verification, such as IP + phone number, etc. Additionally, routes should be
     * moved to groups for more flexible management.
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        if ($request->attributes->get('_rate_limited')) {
            $limiter = $this->anonymousApiLimiter->create($routeName . '_' . $request->getClientIp());

            if (!$limiter->consume()->isAccepted()) {
                throw new TooManyRequestsHttpException('Too many requests');
            }
        }
    }
}