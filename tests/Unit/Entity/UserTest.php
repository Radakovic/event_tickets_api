<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Organizer;
use App\Entity\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private User $user;
    private UuidInterface $id;

    protected function setUp(): void
    {
        $this->id = Uuid::uuid4();

        $this->user = new User(
            firstName: 'Test',
            lastName: 'Account',
            email: 'test@acount.com',
            roles: ['ROLE_USER', 'ROLE_MANAGER'],
            password: 'secret',
            id: $this->id,
        );
    }
    /**
     * Test {@see User} getter methods
     */
    public function testUserGetters(): void
    {
        self::assertEquals('Test', $this->user->getFirstName());
        self::assertEquals('Account', $this->user->getLastName());
        self::assertEquals('test@acount.com', $this->user->getEmail());
        self::assertEquals($this->id, $this->user->getId());
        self::assertEquals(['ROLE_USER', 'ROLE_MANAGER'], $this->user->getRoles());
    }
    /**
     * Test {@see User} setter methods
     */
    public function testUserSetters(): void
    {
        $deletedAt = new DateTimeImmutable();

        $this->user->setFirstName('John');
        $this->user->setLastName('Doe');
        $this->user->setEmail('john.doe@example.com');
        // Here we try to store user without any role
        $this->user->setRoles([]);
        $this->user->setDeletedAt($deletedAt);

        self::assertEquals('John', $this->user->getFirstName());
        self::assertEquals('Doe', $this->user->getLastName());
        self::assertEquals('john.doe@example.com', $this->user->getEmail());
        // Each user must have role at least ROLE_USER
        self::assertEquals(['ROLE_USER'], $this->user->getRoles());
        self::assertEquals($deletedAt, $this->user->getDeletedAt());
        self::assertEquals('john.doe@example.com', $this->user->getUserIdentifier());
    }
    /**
     * Test relation {@see User} with {@see Organizer}
     */
    public function testUserRelations(): void
    {
        $mockOrganizer = $this->createMock(Organizer::class);

        $this->user->addOrganizer($mockOrganizer);
        self::assertCount(1, $this->user->getOrganizers());
        self::assertEquals($mockOrganizer, $this->user->getOrganizers()->first());

        $this->user->removeOrganizer($mockOrganizer);
        self::assertCount(0, $this->user->getOrganizers());
    }
}
