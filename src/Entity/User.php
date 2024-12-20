<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\DataProvider\UserManagersProvider;
use App\Entity\Traits\CreateAndUpdatedAtTrait;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', hardDelete: false)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/users_managers',
            security: "(is_granted('ROLE_ADMIN'))",
            provider: UserManagersProvider::class,
        )
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use CreateAndUpdatedAtTrait;
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        #[Groups(['get_organizer_events'])]
        private string $firstName,

        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        #[Groups(['get_organizer_events'])]
        private string $lastName,

        #[ORM\Column(type: 'string', length: 180, unique: true)]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Email]
        #[Groups(['get_organizer_events'])]
        private string $email,

        #[ORM\Column(type: 'json')]
        private array $roles,

        #[ORM\OneToMany(targetEntity: Organizer::class, mappedBy: 'manager')]
        private ?Collection $organizers = null,

        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?DateTimeImmutable $deletedAt = null,

        #[ORM\Column(type: 'string')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 20)]
        private ?string $password = null,

        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private ?UuidInterface $id = null,
    ) {
        $this->id = $id ?? Uuid::uuid4();
        $this->organizers = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): void
    {
        if ($roles === []) {
            $roles[] = 'ROLE_USER';
        }
        $this->roles = $roles;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    /**
     * @codeCoverageIgnore
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
    public function getOrganizers(): Collection
    {
        return $this->organizers;
    }
    public function addOrganizer(Organizer $organizer): void
    {
        if (!$this->organizers->contains($organizer)) {
            $this->organizers->add($organizer);
            $organizer->setManager($this);
        }
    }
    public function removeOrganizer(Organizer $organizer): void
    {
        if ($this->organizers->contains($organizer)) {
            $this->organizers->removeElement($organizer);
        }
    }
}
