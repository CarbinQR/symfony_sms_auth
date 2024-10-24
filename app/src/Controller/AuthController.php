<?php

namespace App\Controller;

use App\Request\Auth\VerifyAuthCodeRequest;
use App\Service\User\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends ApiController
{
    #[Route('/api/auth/verify', name: 'app_auth_verify_code', defaults: ['_rate_limited' => false], methods: 'POST')]
    public function verifyCode(
        Request               $request,
        AuthService           $authService,
        VerifyAuthCodeRequest $authCodeRequest
    ): JsonResponse
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
