<?php

namespace App\EventListener\Auth;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.controller', method: 'onKernelController')]
class RateLimitListener
{
    private RateLimiterFactory $anonymousApiLimiter;

    public function __construct(RateLimiterFactory $anonymousApiLimiter)
    {
        $this->anonymousApiLimiter = $anonymousApiLimiter;
    }

    /**
     * Обмеження кількості запитів з одного IP. Можливо, бажано перенести у мідлвари та
     * комбінувати параметр для перевірки, накшкалт ІР + номер телефону, тощо...
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->get('_rate_limited')) {
            $limiter = $this->anonymousApiLimiter->create($request->getClientIp());
            if (!$limiter->consume(1)->isAccepted()) {
                throw new TooManyRequestsHttpException('Too many requests');
            }
        }
    }
}