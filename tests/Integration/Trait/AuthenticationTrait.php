<?php

namespace App\Tests\Integration\Trait;

use ApiPlatform\Symfony\Bundle\Test\Client;

trait AuthenticationTrait
{
    /**
     * Create authenticated client
     */
    public function createAuthenticatedClient(string $email = 'user@example.com'): Client
    {
        $client = static::createClient();

        $client->request(
            method: 'POST',
            url: '/api/login_check',
            options: [
                'json' => [
                    'username' => $email,
                    'password' => 'secret',
                ],
            ],
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $client->setDefaultOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $responseData['token'],
            ],
        ]);

        return $client;
    }
}
