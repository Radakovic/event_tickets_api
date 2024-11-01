<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\CreateAndUpdatedAtTrait;
use App\Repository\TicketRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['get_ticket'], 'skip_null_values' => false],
    denormalizationContext: ['groups' => ['write_ticket']],
    //security: "is_granted('ROLE_ADMIN')",
)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', hardDelete: false)]
class Ticket
{
    use CreateAndUpdatedAtTrait;

    public function __construct(
        #[ORM\Column(type: 'integer')]
        #[Groups(['get_ticket', 'write_ticket', 'get_event_ticket'])]
        private float $price,

        #[ORM\Column(type: 'ticket_type')]
        #[Groups(['get_ticket', 'write_ticket', 'get_event_ticket'])]
        private string $type,

        #[ORM\Column(type: 'integer')]
        #[Groups(['get_ticket', 'write_ticket', 'get_event_ticket'])]
        private int $numberAvailableTickets,

        #[ORM\ManyToOne(inversedBy: 'tickets')]
        #[ORM\JoinColumn(nullable: false)]
        #[Groups(['get_ticket', 'write_ticket'])]
        private ?Event $event = null,

        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        #[Groups(['get_ticket', 'get_event_ticket'])]
        private ?UuidInterface $id = null,

        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?DateTime $deletedAt = null,
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
    public function getPrice(): float
    {
        return $this->price / 100;
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
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }
    public function setDeletedAt(?DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
