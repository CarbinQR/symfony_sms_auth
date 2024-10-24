<?php

namespace App\Factory\Sms;

use App\Entity\User;
use App\Provider\Contract\SmsProviderInterface;
use App\Provider\SmsProvider\TurboSmsProvider;

class SmsProviderFactory
{
    private array $providers;

    /**
     * In practice, you can use the name of the provider entity class, something like:
     * [TurboSms::class, ...], and slightly modify the logic in getProviderForUser().
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