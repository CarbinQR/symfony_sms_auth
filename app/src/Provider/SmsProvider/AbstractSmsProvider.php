<?php

namespace App\Provider\SmsProvider;

use GuzzleHttp\Client;

class AbstractSmsProvider
{
    protected Client $client;

    // provider entity
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