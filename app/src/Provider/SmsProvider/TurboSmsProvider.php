<?php

namespace App\Provider\SmsProvider;

use App\Entity\User;
use App\Provider\Contract\SmsProviderInterface;
use GuzzleHttp\Client;

class TurboSmsProvider extends AbstractSmsProvider implements SmsProviderInterface
{
    public function __construct(User $user)
    {
        $this->provider = $user->getProvider();
        parent::__construct();
    }

    /**
     * In practice, we take the credentials from the providers tab and perform the necessary
     * manipulations for authorization with the provider.
     */
    public function auth(): static
    {
        $this->client = new Client([
            'base_uri' => '$this->provider->getUrl()',
            'headers' => [
                'API-Key' => ' $this->provider->getApiKey()',
                'APP-Token' => '$this->provider->getAppToken()',
                'Content-Type' => 'application/json',
            ],
        ]);

        return $this;
    }

    public function send(string $phoneNumber, string $message): array
    {
        $body = [
            'content' => $message,
            'to_number' => $phoneNumber
        ];

        try {
            $response = $this->client->post('/sms/send', [
                'body' => json_encode($body, JSON_THROW_ON_ERROR)
            ]);

            $responseBody = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            // Further actions with the response, logging...

            return $responseBody;
        } catch (\Exception $e) {
            // send sms error log
        }

        return [];
    }
}