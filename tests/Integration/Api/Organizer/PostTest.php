<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Organizer;
use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PostTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    private Client $client;
    private User $user;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->user = UserFactory::createOne();
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testCreateOrganizer(): void
    {
        $organizers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(0, $organizers);

        $request = $this->makeRequest();
        $this->makeApiCall($request);
        self::assertResponseStatusCodeSame(201);

        $currentOrganisers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(1, $currentOrganisers);
        self::assertEquals('Test Organizer', $currentOrganisers[0]->getName());
        self::assertEquals('Test City', $currentOrganisers[0]->getCity());
        self::assertEquals('Test Address', $currentOrganisers[0]->getAddress());
        self::assertEquals(
            $this->user->getId()->toString(),
            $currentOrganisers[0]->getManager()->getId()->toString()
        );
    }

    /**
     * @dataProvider dataValidationErrors
     */
    public function testValidationErrors(array $request, string $error_message, string $propertyPath): void
    {
        $request['json']['manager'] = $this->getIriFromResource($this->user);
        $organizers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(0, $organizers);

        $this->makeApiCall($request);
        self::assertResponseStatusCodeSame(422);

        $currentOrganisers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(0, $currentOrganisers);
        self::assertJsonContains([
            "@context" => "/api/contexts/ConstraintViolationList",
            "@type" => "ConstraintViolationList",
            "status" => 422,
            "violations" => [[
                "propertyPath" => $propertyPath,
                "message" => $error_message,
            ]],
        ]);
    }

    /**
     * Data provider for {@see testValidationErrors}
     */
    public function dataValidationErrors(): array
    {
        return [
            'validate_name' => [
                '$request' => [
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                    ],
                    'json' => [
                        'name' => '',
                        'city' => 'Test City',
                        'address' => 'Test Address',
                    ],
                ],
                '$error_message' => 'This value should not be blank.',
                '$propertyPath' => 'name',
            ],
            'validate_city' => [
                '$request' => [
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                    ],
                    'json' => [
                        'name' => 'Test Organizer',
                        'city' => '',
                        'address' => 'Test Address',
                    ],
                ],
                '$error_message' => 'This value should not be blank.',
                '$propertyPath' => 'city',
            ],
            'validate_address' => [
                '$request' => [
                    'headers' => [
                        'Content-Type' => 'application/ld+json',
                    ],
                    'json' => [
                        'name' => 'Test Organizer',
                        'city' => 'Test City',
                        'address' => '',
                    ],
                ],
                '$error_message' => 'This value should not be blank.',
                '$propertyPath' => 'address',
            ],
        ];
    }

    private function makeRequest(): array
    {
        return [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'name' => 'Test Organizer',
                'city' => 'Test City',
                'address' => 'Test Address',
                'manager' => $this->getIriFromResource($this->user),
            ],
        ];
    }

    private function makeApiCall(array $request): void
    {
        $this->client->request(
            method: 'POST',
            url: '/api/organizers',
            options: $request
        );
    }
}
