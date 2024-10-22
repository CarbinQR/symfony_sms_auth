<?php

namespace App\Provider\SmsProvider;

use GuzzleHttp\Client;

class AbstractSmsProvider
{
    protected Client $client;

    // сутність провайдера
    protected $provider;

    public function __construct()
    {
        $this->auth();
    }

    public function auth(): static
    {
        $this->client = new Client();

        return $this;
    }
}