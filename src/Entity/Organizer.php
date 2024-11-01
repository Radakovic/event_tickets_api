<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\CreateAndUpdatedAtTrait;
use App\Repository\OrganizerRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\JoinColumn;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrganizerRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', hardDelete: false)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['get_organizer_events']],
        ),
        new GetCollection(),
        new Patch(),
        new Post(),
    ],
    normalizationContext: ['groups' => ['get_organizer']],
    denormalizationContext: ['groups' => ['write_organizer']],
    //security: "is_granted('ROLE_ADMIN')",
)]
class Organizer
{
    use CreateAndUpdatedAtTrait;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Groups(['get_organizer', 'write_organizer', 'get_event', 'get_organizer_events'])]
        private string $name,

        #[ORM\Column(length: 255)]
        #[Groups(['get_organizer', 'write_organizer'])]
        private string $city,

        #[ORM\Column(length: 255)]
        #[Groups(['get_organizer', 'write_organizer'])]
        private string $address,

        /**
         * @var Collection<int, Event>
         */
        #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'organizer', orphanRemoval: true)]
        #[Groups(['get_organizer_events'])]
        private ?Collection $events = null,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'organizers')]
        #[JoinColumn(nullable: false)]
        private ?User $manager = null,

        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?DateTimeImmutable $deletedAt = null,

        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        #[Groups(['get_event', 'get_organizer_events'])]
        private ?UuidInterface $id = null,
    ) {
        $this->id = $id ?? Uuid::uuid4();
        $this->events = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getCity(): string
    {
        return $this->city;
    }
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
    public function getAddress(): string
    {
        return $this->address;
    }
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
    public function getManager(): User
    {
        return $this->manager;
    }
    public function setManager(User $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }
    public function addEvent(Event $event): void
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOrganizer($this);
        }
    }
    public function removeEvent(Event $event): void
    {
        if ($this->events->removeElement($event) && $event->getOrganizer() === $this) {
            $event->setOrganizer(null);
        }
    }
}
