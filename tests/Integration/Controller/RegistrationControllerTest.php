<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RegistrationControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testRegistration(): void
    {
        $this->client->request(
            method: 'POST',
            uri: '/registration',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(
                [
                    'firstName' => 'Pera',
                    'lastName' => 'Mitic',
                    'email' => 'pera_davitelj@gmail.com',
                    'password' => 'davitelj',
                ],
                JSON_THROW_ON_ERROR
            ));
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
