<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SmsController extends ApiController
{
    #[Route('/api/sms/send', name: 'send_sms')]
    public function send(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SmsController.php',
        ]);
    }
}
