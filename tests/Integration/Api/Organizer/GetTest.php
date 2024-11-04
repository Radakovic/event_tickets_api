<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Organizer;
use App\Factory\EventFactory;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

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

        $this->client = static::createClient();
    }

    public function testGetItem(): void
    {
        $this->makeRequest($this->organizer->getId()->toString());

        $this->assertOrganizerResponse();
    }

    /**
     * Call endpoint
     */
    private function makeRequest(string $id = ''): void
    {
        $url = sprintf('/api/organizers/%s', $id);

        $this->client->request(
            method: 'GET',
            url: $url
        );
    }
    /**
     *  Assert organizer response.
     */
    private function assertOrganizerResponse(): void
    {
        $events = $this->organizer->getEvents();

        $expectedEventsResponse = [];
        foreach ($events as $event) {
            $expectedEventsResponse[] = [
                '@id' => sprintf('/api/events/%s', $event->getId()->toString()),
                '@type' => 'Event',
                'name' => $event->getName(),
                //'date' => $event->getDate(),
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
            '@id' => sprintf('/api/organizers/%s', $this->organizer->getId()->toString()),
            '@type' => 'Organizer',
            'name' => $this->organizer->getName(),
            'id' => $this->organizer->getId()->toString(),
            'events' => $expectedEventsResponse
        ]);

    }
}
