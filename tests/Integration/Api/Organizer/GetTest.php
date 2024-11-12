<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Event;
use App\Entity\Organizer;
use App\Factory\EventFactory;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use App\Tests\Integration\Trait\AuthenticationTrait;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    use AuthenticationTrait;

    private Organizer $organizer;
    private Client $client;

    protected function setUp(): void
    {
        $this->organizer = OrganizerFactory::createOne(
            static function () {
                return [
                    'name' => 'Abc def',
                    'manager' => UserFactory::createOne(),
                ];
            }
        );
        EventFactory::createMany(
            5,
            static function () {
                return [
                    'organizer' => OrganizerFactory::first()
                ];
            }
        );
    }

    /**
     * Test will show that only owner of organizer have access to {@see Organizer} object.
     * @dataProvider dataGetItemOrganizers
     */
    public function testGetItemOrganizers(string $email, string $role, int $statusCode): void
    {
        if ($email !== 'user@example.com') {
            $this->createNewUser($email, $role);
        }

        $user = UserFactory::createOne(
            static function (): array {
                return [
                    'email' => 'user@example.com',
                    'roles' => ['ROLE_MANAGER'],
                ];
            }
        );
        $organizer = OrganizerFactory::createOne(
            static function () use ($user): array {
                return [
                    'manager' => $user,
                ];
            }
        );

        $uri = sprintf('/api/organizers/%s', $organizer->getId()->toString());

        $this->client = $this->createAuthenticatedClient($email);
        $this->makeRequest($uri);
        self::assertResponseStatusCodeSame($statusCode);
    }

    /**
     * Data provider for {@see testGetItemOrganizers}
     * @return array<string, array<string, string>>
     */
    public function dataGetItemOrganizers(): array
    {
        return [
            'user_manager' => [
                '$email' => 'user@example.com',
                '$role' => 'ROLE_MANAGER',
                '$statusCode' => Response::HTTP_OK,
            ],
            'other_manager' => [
                '$email' => 'other_manager@example.com',
                '$role' => 'ROLE_MANAGER',
                '$statusCode' => Response::HTTP_NOT_FOUND,
            ],
            'user_admin' => [
                '$email' => 'user_admin@example.com',
                '$role' => 'ROLE_ADMIN',
                '$statusCode' => Response::HTTP_OK,
            ],
        ];
    }

    public function testGetItem(): void
    {
        $manager = UserFactory::createOne(
            static function (): array {
                return [
                    'email' => 'userManager@example.com',
                    'roles' => ['ROLE_MANAGER'],
                ];
            }
        );
        $organizer = OrganizerFactory::createOne(
            static function () use ($manager): array {
                return [
                    'manager' => $manager,
                ];
            }
        );
        $events = EventFactory::createMany(
            5,
            static function () use ($organizer): array {
                return [
                    'organizer' => $organizer,
                ];
            }
        );

        $this->client = $this->createAuthenticatedClient($manager->getEmail());

        $url = sprintf('/api/organizers/%s', $organizer->getId()->toString());
        $this->makeRequest($url);
        $this->assertOrganizerResponse($organizer, $events);
    }

    /**
     * Call endpoint
     */
    private function makeRequest(string $url = '/api/organizers'): void
    {
        $this->client->request(
            method: 'GET',
            url: $url
        );
    }

    /**
     * Assert organizer response.
     */
    private function assertOrganizerResponse(Organizer $organizer): void
    {
        $expectedEventsResponse = [];
        foreach ($organizer->getEvents() as $event) {
            $expectedEventsResponse[] = [
                '@id' => sprintf('/api/events/%s', $event->getId()->toString()),
                '@type' => 'Event',
                'name' => $event->getName(),
                'type' => $event->getType(),
                'city' => $event->getCity(),
                'country' => $event->getCountry(),
                'address' => $event->getAddress(),
                'description' => $event->getDescription(),
                'id' => $event->getId()->toString(),
            ];
        }

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/api/contexts/Organizer',
            '@id' => sprintf('/api/organizers/%s', $organizer->getId()->toString()),
            '@type' => 'Organizer',
            'name' => $organizer->getName(),
            'id' => $organizer->getId()->toString(),
            'events' => $expectedEventsResponse
        ]);
    }

    private function createNewUser(string $email, string $role): void
    {
        UserFactory::createOne(
            static function () use ($email, $role): array {
                return [
                    'email' => $email,
                    'roles' => [$role],
                ];
            }
        );
    }
}
