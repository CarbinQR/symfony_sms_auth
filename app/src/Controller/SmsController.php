<?php

namespace App\Controller;

use App\Request\Sms\SendAuthCodeRequest;
use App\Service\Sms\SmsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SmsController extends ApiController
{
    #[Route('/api/sms/send/auth', name: 'app_sms_send_auth', defaults: ['_rate_limited' => true], methods: 'POST')]
    public function sendAuthCode(
        SmsService $smsService,
        SendAuthCodeRequest $request
    ): JsonResponse
    {
        return $this->success(
            $smsService->sendAuthCode(
                json_decode(
                    $request->getRequest()->getContent(), true, 512, JSON_THROW_ON_ERROR
                )
            )
        );
    }
}
