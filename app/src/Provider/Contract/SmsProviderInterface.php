<?php

namespace App\Provider\Contract;

interface SmsProviderInterface
{
    public function auth(): static;
    public function send(string $phoneNumber, string $message): array;
}