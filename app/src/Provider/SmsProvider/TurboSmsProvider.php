<?php

namespace App\Provider\SmsProvider;

use App\Entity\User;
use App\Provider\Contract\SmsProviderInterface;

class TurboSmsProvider extends AbstractSmsProvider implements SmsProviderInterface
{

    public function __construct(User $user)
    {
        $this->provider = $user->getProvider();
        parent::__construct();
    }
    /**
     * на практиці беремо креди з таби провайдерів та проводимо необхідні
     * маніпуляціїї для авторизаціїї у провайдера
     */
    public function auth(): static
    {
//        $this->client = new Client([
//            'base_uri' => $this->provider->getUrl(),
//            'headers' => [
//                'API-Key' => ' $this->provider->getApiKey()',
//                'APP-Token' => '$this->provider->getAppToken()',
//                'Content-Type' => 'application/json',
//            ],
//        ]);

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

            //Подальші дії з респонсом, логування...

            return $responseBody;
        } catch (\Exception $requestException) {
            // Логування та подальші дії
        }

        return [];
    }
}