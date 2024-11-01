<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\CreateAndUpdatedAtTrait;
use App\Repository\TicketRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', hardDelete: false)]
class Ticket
{
    use CreateAndUpdatedAtTrait;

    public function __construct(
        #[ORM\Column(type: 'integer')]
        private int $price,

        #[ORM\Column(type: 'ticket_type')]
        private string $type,

        #[ORM\Column(type: 'integer')]
        private int $numberAvailableTickets,

        #[ORM\ManyToOne(inversedBy: 'tickets')]
        #[ORM\JoinColumn(nullable: false)]
        private ?Event $event = null,

        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private ?UuidInterface $id = null,

        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?DateTimeImmutable $deletedAt = null,
    ) {
        $this->id = $id ?? Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    public function getPrice(): int
    {
        return $this->price;
    }
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }
    public function getEvent(): Event
    {
        return $this->event;
    }
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }
    public function getNumberAvailableTickets(): int
    {
        return $this->numberAvailableTickets;
    }
    public function setNumberAvailableTickets(int $numberAvailableTickets): void
    {
        $this->numberAvailableTickets = $numberAvailableTickets;
    }
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
