<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\Cache\CacheServiceInterface;
use App\Service\Sms\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthService
{
    private const REQUESTS_LIMIT = 3;

    private const CODE_TTL = 15; //15 минут

    private const CACHE_TTL = 60 * self::CODE_TTL; //15 минут

    private const CACHE_PREFIX = 'auth_code:';

    public function __construct(
        readonly EntityManagerInterface   $entityManager,
        readonly SmsService               $smsService,
        readonly CacheServiceInterface    $cacheService,
        readonly UserService              $userService,
        readonly JWTTokenManagerInterface $jwtManager
    )
    {
    }

    /**
     * на практиці беремо креди з таби провайдерів та проводимо необхідні
     * маніпуляціїї для авторизаціїї у провайдера. Зараз повертається код лише для тесту
     */
    public function sendAuthCode(array $requestData): array
    {
        $phoneNumber = $requestData['phone'];
        $user = $this->userService->findOrCreateUserByPhone($phoneNumber);
        $code = random_int(000000, 999999);
        if ($user) {
            try {
                $this->smsService->sendAuthCode(
                    $user,
                    "Auth code: $code"
                );
            } catch (\Exception $e) {

            }
        }

        $this->cacheCode($user, $code);
        //логування

        return ['code' => $code];
    }

    private function cacheCode(User $user, int $code): void
    {
        $cacheData = $this->getDataFromCache($user->getPhone());
        $writeData = [
            'user' => $user->getId(),
            'code' => $code,
            'count' => 1,
        ];

//        if ($cacheData && ($cacheData['count'] == self::REQUESTS_LIMIT)) {
//            throw new \Exception('Request limit', 403);
//        }

        if ($cacheData) {
            $writeData['count'] += $cacheData['count'];
        }

        $this->cacheService->set(
            self::CACHE_PREFIX . $user->getPhone()
            , json_encode($writeData),
            self::CACHE_TTL
        );
    }

    private function getDataFromCache(string $phoneNumber): ?array
    {
        $cacheData = $this->cacheService->get(self::CACHE_PREFIX . $phoneNumber);

        return (empty($cacheData))
            ? $cacheData
            : json_decode($cacheData, true);
    }

    public function verifyCode(array $requestData)
    {
        $phoneNumber = $requestData['phone'];
        $code = $requestData['code'];
        $cacheData = $this->getDataFromCache($phoneNumber);
        if (!$cacheData) {
            throw new \Exception('Phone number not found or code expired');
        }
        if ($cacheData && ($cacheData['code'] != $code)) {
            throw new \Exception('Invalid code');
        }

        $this->cacheService->delete(self::CACHE_PREFIX . $phoneNumber);
        $user = $this->userService->findOrCreateUserByPhone($phoneNumber);

        return [
            'user' => $this->userService->makeItem($user),
            'token' => $this->jwtManager->create($user),
        ];
    }
}