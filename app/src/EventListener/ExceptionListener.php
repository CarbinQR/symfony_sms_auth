<?php

namespace App\EventListener;

use App\Exception\InvalidJsonRequest;
use Fig\Http\Message\StatusCodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
//        $response = [
//            'status' => 'error',
//            'code' => $exception->getCode()
//        ];

        $response = new JsonResponse(
            data: [
                'status' => 'error',
                'code' => $exception->getCode(),
                'message' => $exception->getErrors()
            ],
            status: $exception->getCode(),
        );

//        if ($exception instanceof InvalidJsonRequest) {
//            $response['message'] = $exception->getErrors();
//            $response = new JsonResponse(
//                data: ['errors' => $exception->getErrors()],
//                status: Response::HTTP_BAD_REQUEST,
//            );
//            $event->setResponse($response);
//        }
        $event->setResponse($response);
    }
}