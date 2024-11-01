<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\EventEnum;
use App\Repository\EventRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['get_event']],
    denormalizationContext: ['groups' => ['write_event']],
    //security: "is_granted('ROLE_ADMIN')",
)]
class Event
{

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private string $name,

        #[ORM\Column(type: Types::DATETIME_MUTABLE)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private DateTimeInterface $date,

        #[ORM\Column(type: 'event_type')]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private string $type,

        #[ORM\Column(length: 255)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private string $city,

        #[ORM\Column(length: 255)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private string $country,

        #[ORM\Column(length: 255)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private string $address,

        #[ORM\ManyToOne(inversedBy: 'events')]
        #[ORM\JoinColumn(nullable: false)]
        #[Groups(['get_event', 'write_event'])]
        private ?Organizer $organizer = null,

        #[ORM\Column(type: Types::TEXT, nullable: true)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private ?string $description = null,

        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        #[Groups(['get_event', 'write_event', 'get_organizer_events'])]
        private ?UuidInterface $id = null,
    ) {
        $this->id = $id ?? Uuid::uuid4();
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
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    public function getAddress(): string
    {
        return $this->address;
    }
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }
    public function setOrganizer(Organizer $organizer): void
    {
        $this->organizer = $organizer;
    }
    public function getCity(): string
    {
        return $this->city;
    }
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
    public function getCountry(): string
    {
        return $this->country;
    }
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
}
