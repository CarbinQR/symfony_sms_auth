<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends AbstractController
{
    public function success(array $data): JsonResponse
    {
        return $this->json(['status' => 'success', 'data' => $data, 'code' => Response::HTTP_OK]);
    }

    public function error(array $data): JsonResponse
    {
        return $this->json(['status' => 'error', 'message' => $data, 'code' => Response::HTTP_BAD_REQUEST]);
    }
}
