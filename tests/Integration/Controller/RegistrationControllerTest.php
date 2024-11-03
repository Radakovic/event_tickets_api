<?php

namespace App\Tests\Integration\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RegistrationControllerTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    private Client $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine.orm.entity_manager');
    }

    /**
     * Test successfully registered user
     */
    public function testRegistration(): void
    {
        $data = [
            'firstName' => 'Test',
            'lastName' => 'Account',
            'email' => 'testing@test.com',
            'password' => 'secret',
        ];

        $this->makeRequest($data);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * Test validations
     * @dataProvider dataRegistrationFailsValidations
     */
    public function testRegistrationFailsValidations(array $data): void
    {
        $this->makeRequest($data);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Data provider for {@see testRegistrationFailsValidations}
     * @return array<string, string>
     */
    public function dataRegistrationFailsValidations(): array
    {
        return [
            'firstName_required' => [
                '$data' => [
                    'firstName' => '',
                    'lastName' => 'Account',
                    'email' => 'testing@test.com',
                    'password' => 'secret',
                ],
            ],
            'lastName_required' => [
                '$data' => [
                    'firstName' => 'test',
                    'lastName' => '',
                    'email' => 'testing@test.com',
                    'password' => 'secret',
                ],
            ],
            'email_required' => [
                '$data' => [
                    'firstName' => 'Test',
                    'lastName' => 'Account',
                    'email' => '',
                    'password' => 'secret',
                ],
            ],
            'password_required' => [
                '$data' => [
                    'firstName' => 'Test',
                    'lastName' => 'Account',
                    'email' => 'testing@test.com',
                    'password' => '',
                ],
            ],
        ];
    }

    /**
     * Test user data after registration
     * @return void
     */
    public function testRegisteredUser(): void
    {
        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        self::assertCount(0, $allUsers);

        $data = [
            'firstName' => 'Test',
            'lastName' => 'Account',
            'email' => 'testing@test.com',
            'password' => 'secret',
        ];

        $this->makeRequest($data);
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        self::assertCount(1, $allUsers);
        $this->assertUserData($data);
    }

    private function makeRequest(array $data): void
    {
        $this->client->request(
            method: 'POST',
            url:'/registration',
            options: ['json' => $data]);
    }

    private function assertUserData(array $data): void
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);

        self::assertNotNull($user);
        self::assertEquals($data['firstName'], $user->getFirstName());
        self::assertEquals($data['lastName'], $user->getLastName());
        self::assertEquals($data['email'], $user->getEmail());
    }
}
