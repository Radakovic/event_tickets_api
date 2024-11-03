<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Organizer;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetCollectionTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    /**
     * @var array<int, Organizer>
     */
    private array $organizers;
    private Client $client;

    protected function setUp(): void
    {
        $this->organizers = OrganizerFactory::createMany(
            5,
            function () {
                return [
                    'name' => 'Abc def',
                    'manager' => UserFactory::createOne(),
                ];
            }
        );

        $this->client = static::createClient();
    }
    /**
     * Test will show result of get all {@see Organizer} endpoint
     */
    public function testGetOrganizers(): void
    {
        $this->makeRequest();

        $this->assertResponse();
    }

    /**
     * Test will show how client can manipulate with number of items in response
     */
    public function testGetCollectionWithNumberOfItems(): void
    {
        $this->makeRequest('/api/organizers?itemsPerPage=2');

        $this->assertResponse(itemsPerPage: 2);
    }
    /**
     *  By default, Api platform returns 30 items per page if it is enabled pagination.
     *  In this test client will disable pagination, and it will get all 55 items in response.
     *  5 items will be created in setUp method, and 50 items in test.
     */
    public function testDisablePaginationFromClient(): void
    {
        $this->organizers = OrganizerFactory::createMany(
            50,
            function () {
                return [
                    'manager' => UserFactory::createOne(),
                ];
            }
        );

        $this->makeRequest('/api/organizers?pagination=false');

        $this->assertResponse(totalItems: 55, itemsPerPage: 55);
    }
    /**
     *  Test will show search filter by name. In setUp method all {@see Organizer} are with letters Abc def in name.
     *  In the test we created one more with name `Ijkl mno`. Only this {@see Organizer} will be in response.
     */
    public function testSearchOrganizersByName(): void
    {
        $this->organizers = OrganizerFactory::createMany(
            1,
            function () {
                return [
                    'name' => 'Ijkl mno',
                    'manager' => UserFactory::createOne(),
                ];
            }
        );

        $this->makeRequest('/api/organizers?name=Ijk');

        $this->assertResponse(totalItems: 1, itemsPerPage: 1);
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
     *  Assert collection response, assert totalItems, assert itemsPerPage.
     */
    private function assertResponse(int $totalItems = 5, int $itemsPerPage = 5): void
    {
        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/api/contexts/Organizer',
            '@id' => '/api/organizers',
            '@type' => 'Collection',
            'totalItems' => $totalItems,
        ]);

        $responseData = $this->client->getResponse()->toArray();
        self::assertCount($itemsPerPage, $responseData['member']);
    }
}
