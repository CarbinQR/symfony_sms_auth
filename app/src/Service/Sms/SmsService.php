<?php

namespace App\Service\Sms;

use App\Entity\User;
use App\Factory\Sms\SmsProviderFactory;

class SmsService
{
    private SmsProviderFactory $smsProviderFactory;

    public function __construct(SmsProviderFactory $smsProviderFactory)
    {
        $this->smsProviderFactory = $smsProviderFactory;
    }

    public function sendAuthCode(User $user, string $message): array
    {
        return $this->smsProviderFactory
            ->getProviderForUser($user)
            ->auth()
            ->send($user->getPhone(), $message);
    }
}