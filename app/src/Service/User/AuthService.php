<?php

namespace App\Service\User;

use App\Service\Cache\CacheServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    private const CODE_TTL = 15; //15 минут

    public const CACHE_TTL = 60 * self::CODE_TTL; //15 минут

    public const CACHE_PREFIX = 'auth_code:';

    public function __construct(
        readonly EntityManagerInterface   $entityManager,
        readonly CacheServiceInterface    $cacheService,
        readonly UserService              $userService,
        readonly JWTTokenManagerInterface $jwtManager
    )
    {
    }

    public function verifyCode(array $requestData)
    {
        $phoneNumber = $requestData['phone'];
        $code = $requestData['code'];
        $cacheData = $this->cacheService->get((self::CACHE_PREFIX . $phoneNumber));

        if (!$cacheData) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Phone number not found or code expired');
        }

        if ($cacheData && ($cacheData['code'] != $code)) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid code');
        }

        $this->cacheService->delete(self::CACHE_PREFIX . $phoneNumber);
        $user = $this->userService->findOrCreateUserByPhone($phoneNumber);

        return [
            'user' => $this->userService->makeItem($user),
            'token' => $this->jwtManager->create($user),
        ];
    }
}