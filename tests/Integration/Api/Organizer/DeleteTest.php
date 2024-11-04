<?php

namespace App\Tests\Integration\Api\Organizer;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Organizer;
use App\Factory\EventFactory;
use App\Factory\OrganizerFactory;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DeleteTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    private Organizer $organizer;
    private Client $client;
    private EntityManagerInterface $entityManager;

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

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->client = static::createClient();
    }

    public function testDeleteOrganizer(): void
    {
        $organizers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(1, $organizers);
        $this->assertCurrentOrganizer($organizers);

        $this->makeRequest($this->organizer->getId()->toString());
        $organizers = $this->entityManager->getRepository(Organizer::class)->findAll();
        self::assertCount(0, $organizers);
    }

    /**
     * Call endpoint
     */
    private function makeRequest(string $id = ''): void
    {
        $url = sprintf('/api/organizers/%s', $id);

        $this->client->request(
            method: 'DELETE',
            url: $url
        );
    }

    private function assertCurrentOrganizer(array $organizers): void
    {
        $currentOrganizer = $organizers[0];
        assert($currentOrganizer instanceof Organizer);

        self::assertSame($this->organizer->getName(), $currentOrganizer->getName());
        self::assertSame($this->organizer->getId()->toString(), $currentOrganizer->getId()->toString());
        self::assertSame($this->organizer->getAddress(), $currentOrganizer->getAddress());
        self::assertSame($this->organizer->getCity(), $currentOrganizer->getCity());
    }
}
