<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\Organizer;
use App\Entity\User;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrganizerTest extends KernelTestCase
{
    private Organizer $organizer;
    private UuidInterface $id;

    protected function setUp(): void
    {
        $this->id = Uuid::uuid4();

        $this->organizer = new Organizer(
            name: 'Test Organizer',
            city: 'Roma',
            address: 'Some address',
            id: $this->id
        );
    }
    /**
     * Test {@see Organizer} getter methods
     */
    public function testOrganizerGetters(): void
    {
        self::assertSame('Test Organizer', $this->organizer->getName());
        self::assertSame('Roma', $this->organizer->getCity());
        self::assertSame('Some address', $this->organizer->getAddress());
        self::assertSame($this->id, $this->organizer->getId());
    }
    /**
     * Test {@see Organizer} setter methods
     */
    public function testOrganizerSetters(): void
    {
        $deletedAt = new DateTime();

        $this->organizer->setName('New name');
        $this->organizer->setCity('Sofia');
        $this->organizer->setAddress('New address');
        $this->organizer->setDeletedAt($deletedAt);

        self::assertSame('New name', $this->organizer->getName());
        self::assertSame('Sofia', $this->organizer->getCity());
        self::assertSame('New address', $this->organizer->getAddress());
        self::assertSame($deletedAt, $this->organizer->getDeletedAt());
    }
    /**
     * Test relation {@see User} with {@see Organizer}
     */
    public function testOrganizerRelations(): void
    {
        $eventMock = $this->createMock(Event::class);
        $managerMock = $this->createMock(User::class);

        $this->organizer->addEvent($eventMock);
        $this->organizer->setManager($managerMock);

        self::assertCount(1, $this->organizer->getEvents());
        self::assertEquals($eventMock, $this->organizer->getEvents()->first());
        self::assertEquals($managerMock, $this->organizer->getManager());

        $this->organizer->removeEvent($eventMock);
        self::assertCount(0, $this->organizer->getEvents());
    }
}
