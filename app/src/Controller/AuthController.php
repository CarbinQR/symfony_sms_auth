<?php

namespace App\Controller;

use App\Request\Auth\SendAuthCodeRequest ;
use App\Service\User\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends ApiController
{
    #[Route('/api/auth', name: 'app_auth', defaults: ['_rate_limited' => true], methods: 'POST')]
    public function sendAuthCode(Request $request, AuthService $authService, SendAuthCodeRequest $sendAuthCodeRequest): JsonResponse
    {
        return $this->success(
            $authService->sendAuthCode(
                json_decode(
                    $request->getContent(), true, 512, JSON_THROW_ON_ERROR
                )
            )
        );
    }

    #[Route('/api/verify', name: 'app_verify_code', defaults: ['_rate_limited' => true], methods: 'POST')]
    public function verifyCode(Request $request, AuthService $authService): JsonResponse
    {
        return $this->success(
            $authService->verifyCode(
                json_decode(
                    $request->getContent(), true, 512, JSON_THROW_ON_ERROR
                )
            )
        );
    }
}
