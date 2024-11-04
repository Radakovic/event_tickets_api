<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Organizer;
use App\Entity\User;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatchTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    private Client $client;
    private User $user;
    private Organizer $organizer;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->user = UserFactory::createOne();
        $this->organizer = OrganizerFactory::createOne(
            static function (): array {
                return [
                    'manager' => UserFactory::first()
                ];
            }
        );
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testPatchOrganizer(): void
    {
        $organizers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(1, $organizers);
        self::assertEquals($this->organizer->getName(), $organizers[0]->getName());
        self::assertEquals($this->organizer->getCity(), $organizers[0]->getCity());
        self::assertEquals($this->organizer->getAddress(), $organizers[0]->getAddress());
        self::assertEquals(
            $this->user->getId()->toString(),
            $organizers[0]->getManager()->getId()->toString()
        );

        $request = $this->makeRequest();
        $this->makeApiCall($this->organizer->getId()->toString(), $request);
        self::assertResponseStatusCodeSame(200);

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
        $organizers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(1, $organizers);

        $this->makeApiCall($this->organizer->getId()->toString(), $request);
        self::assertResponseStatusCodeSame(422);

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
                        'Content-Type' => 'application/merge-patch+json',
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
                        'Content-Type' => 'application/merge-patch+json',
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
                        'Content-Type' => 'application/merge-patch+json',
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
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => [
                'name' => 'Test Organizer',
                'city' => 'Test City',
                'address' => 'Test Address',
                'manager' => $this->getIriFromResource($this->user),
            ],
        ];
    }

    private function makeApiCall(string $id, array $request): void
    {
        $this->client->request(
            method: 'PATCH',
            url: sprintf('/api/organizers/%s', $id),
            options: $request
        );
    }
}
