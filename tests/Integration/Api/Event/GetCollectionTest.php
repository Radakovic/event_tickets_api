<?php

namespace App\Tests\Integration\Api\Event;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Event;
use App\Factory\EventFactory;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetCollectionTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    /**
     * @var array<int, Event>
     */
    private array $events;
    private Client $client;

    protected function setUp(): void
    {
        $this->firstOrganizer = OrganizerFactory::createOne(
            function (): array {
                return [
                    'manager' => UserFactory::createOne(),
                ];
            }
        );
        $this->secondOrganizer = OrganizerFactory::createOne(
            function (): array {
                return [
                    'manager' => UserFactory::createOne(),
                ];
            }
        );

        $this->events = EventFactory::createMany(
            15,
            function () {
                return [
                    'organizer' => OrganizerFactory::random(),
                ];
            }
        );

        $this->client = static::createClient();
    }
    /**
     * Test will show result of get all {@see Event} endpoint
     */
    public function testGetEvents(): void
    {
        $this->makeRequest();
        $this->assertResponse();
    }

    /**
     * Test will show how client can manipulate with number of items in response
     */
    public function testGetCollectionWithNumberOfItems(): void
    {
        $this->makeRequest('/api/events?page=1&itemsPerPage=5');
        self::assertResponseStatusCodeSame(200);
        $this->assertResponse(itemsPerPage: 5);
    }
    /**
     * Call endpoint
     */
    private function makeRequest(string $url = '/api/events'): void
    {
        $this->client->request(
            method: 'GET',
            url: $url,
        );
    }
    /**
     * Assert collection response
     */
    private function assertResponse(int $totalItems = 15, int $itemsPerPage = 15): void
    {
        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/api/contexts/Event',
            '@id' => '/api/events',
            '@type' => 'Collection',
            'totalItems' => $totalItems,
        ]);

        $responseData = $this->client->getResponse()->toArray();
        self::assertCount($itemsPerPage, $responseData['member']);
    }
}
