<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Organizer;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use App\Tests\Integration\Trait\AuthenticationTrait;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class GetCollectionAuthenticationTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    use AuthenticationTrait;

    /**
     * @var array<int, Organizer>
     */
    private array $organizers;
    private Organizer $privateOrganizer;
    private Client $client;

    protected function setUp(): void
    {
        UserFactory::createMany(3);
        OrganizerFactory::createMany(
            5,
            static function (): array {
                return [
                    'name' => 'Abc def',
                    'manager' => UserFactory::random(),
                ];
            }
        );

        $this->userAdmin = UserFactory::createOne(
            static function (): array {
                return [
                    'email' => 'userAdmin@example.com',
                    'roles' => ['ROLE_ADMIN'],
                ];
            }
        );
    }

    /**
     *  Get collection route for {@see Organizer} is allowed only for ROLE_ADMIN.
     *  All other users does not have access to this route.
     * @dataProvider dataGetCollectionOrganizers
     */
    public function testGetCollectionOrganizers(string $role, int $statusCode): void
    {
        UserFactory::createOne(
            static function () use ($role): array {
                return [
                    'email' => 'user@example.com',
                    'roles' => [$role],
                ];
            }
        );

        $this->client = $this->createAuthenticatedClient();
        $this->makeRequest();
        self::assertResponseStatusCodeSame($statusCode);
    }

    /**
     * Data provider for {@see testGetCollectionOrganizers}
     * @return array<string, array<string, string>>
     */
    public function dataGetCollectionOrganizers(): array
    {
        return [
            'user' => [
                '$role' => 'ROLE_USER',
                '$statusCode' => Response::HTTP_FORBIDDEN,
            ],
            'user_manager' => [
                '$role' => 'ROLE_MANAGER',
                '$statusCode' => Response::HTTP_OK,
            ],
            'user_admin' => [
                '$role' => 'ROLE_ADMIN',
                '$statusCode' => Response::HTTP_OK,
            ],
        ];
    }

    /**
     * Test will show that admin gets all {@see Organizer} from database
     */
    public function testAdminWillGetAllOrganizersFromDatabase(): void
    {
        $this->client = $this->createAuthenticatedClient($this->userAdmin->getEmail());
        $this->makeRequest();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertJsonContains([
            "@context" => "/api/contexts/Organizer",
            "@id" => "/api/organizers",
            "@type" => "Collection",
            "totalItems" => 5,
        ]);
    }

    /**
     * Test will show that Manager will see only his Organizers,
     * but Admins will see all Organizers from database.
     * @dataProvider dataManagersWillGetOnlyHisOrganizers
     */
    public function testManagersWillGetOnlyHisOrganizers(string $email, int $totalItems): void
    {
        $manager = UserFactory::createOne(
            static function (): array {
                return [
                    'roles' => ['ROLE_MANAGER'],
                    'email' => 'userManager@example.com'
                ];
            }
        );
        OrganizerFactory::createMany(
            3,
            static function () use ($manager): array {
                return [
                    'manager' => $manager,
                ];
            }
        );

        $this->client = $this->createAuthenticatedClient($email);
        $this->makeRequest();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertJsonContains([
            "@context" => "/api/contexts/Organizer",
            "@id" => "/api/organizers",
            "@type" => "Collection",
            "totalItems" => $totalItems,
        ]);
    }

    /**
     * Data provider for {@see testManagersWillGetOnlyHisOrganizers}
     */
    public function dataManagersWillGetOnlyHisOrganizers(): array
    {
        return [
            'manager' => [
                '$email' => 'userManager@example.com',
                '$totalItems' => 3,
            ],
            'admin' => [
                '$email' => 'userAdmin@example.com',
                '$totalItems' => 8,
            ],
        ];
    }

    /**
     * Test will show how client can manipulate with number of items in response
     */
    public function testGetCollectionWithNumberOfItems(): void
    {
        $this->client = $this->createAuthenticatedClient($this->userAdmin->getEmail());
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
            static function (): array {
                return [
                    'manager' => UserFactory::createOne(),
                ];
            }
        );

        $this->client = $this->createAuthenticatedClient($this->userAdmin->getEmail());
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
            static function (): array {
                return [
                    'name' => 'Ijkl mno',
                    'manager' => UserFactory::createOne(),
                ];
            }
        );

        $this->client = $this->createAuthenticatedClient($this->userAdmin->getEmail());
        $this->makeRequest('/api/organizers?name=Ijk');

        $this->assertResponse(totalItems: 1, itemsPerPage: 1);
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
    private function makeRequest(string $url = '/api/organizers'): void
    {
        $this->client->request(
            method: 'GET',
            url: $url
        );
    }
}
