<?php

namespace App\Service\Sms;

use App\Entity\User;
use App\Factory\Sms\SmsProviderFactory;
use App\Service\Cache\CacheServiceInterface;
use App\Service\User\AuthService;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SmsService
{
    private const REQUESTS_LIMIT = 3;
    private SmsProviderFactory $smsProviderFactory;

    public function __construct(
        readonly CacheServiceInterface $cacheService,
        readonly UserService           $userService,
        SmsProviderFactory             $smsProviderFactory)
    {
        $this->smsProviderFactory = $smsProviderFactory;
    }

    /**
     * In practice, we take credentials from the providers table and perform the necessary
     * manipulations for authorization with the provider. Currently, the code is returned only for testing.
     */
    public function sendAuthCode(array $requestData): array
    {
        $phoneNumber = $requestData['phone'];
        $user = $this->userService->findOrCreateUserByPhone($phoneNumber);
        $code = random_int(000000, 999999);

        $this->smsProviderFactory
            ->getProviderForUser($user)
            ->auth()
            ->send($user->getPhone(), "Auth code: $code");
        $this->cacheCode($user, $code);

        // Process provider response, logging

        return ['code' => $code];
    }

    private function cacheCode(User $user, int $code): void
    {
        $cacheKey = AuthService::CACHE_PREFIX . $user->getPhone();
        $cacheData = $this->cacheService->get($cacheKey);

        if ($cacheData && ($cacheData['count'] == self::REQUESTS_LIMIT)) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Request limit');
        }

        $writeData = [
            'user' => $user->getId(),
            'code' => $code,
            'count' => 1,
        ];

        if ($cacheData) {
            $writeData['count'] += $cacheData['count'];
        }

        $this->cacheService->set(
            $cacheKey,
            json_encode($writeData, JSON_THROW_ON_ERROR),
            AuthService::CACHE_TTL
        );
    }
}