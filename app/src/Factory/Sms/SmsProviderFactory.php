<?php

namespace App\Factory\Sms;

use App\Entity\User;
use App\Provider\Contract\SmsProviderInterface;
use App\Provider\SmsProvider\TurboSmsProvider;

class SmsProviderFactory
{
    private array $providers;

    /**
     * На практиці можна використовувати ім'я класа сутності провайдера, щось типу:
     * [TurboSms::class, ...], а у getProviderForUser() трохи переробити логіку..
     */
    public function __construct()
    {
        $this->providers = [
            'turbo_sms' => TurboSmsProvider::class,
        ];
    }

    public function getProviderForUser(User $user): SmsProviderInterface
    {
        if (isset($this->providers[$user->getProvider()])) {
            $providerClass = $this->providers[$user->getProvider()];

            return new $providerClass($user);
        }

        throw new \Exception("SMS provider not found for user.");
    }
}