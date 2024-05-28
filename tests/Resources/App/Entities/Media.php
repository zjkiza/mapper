<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Resources\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Zjk\DtoMapper\Contract\IdentifierInterface;

#[ORM\Entity()]
class Media implements IdentifierInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    /**
     * This is OWNER of this relation, unidirectional.
     *
     * @var Collection<string|int,Expert>
     */
    #[ORM\ManyToMany(targetEntity: Expert::class)]
    #[ORM\JoinTable(name: 'media_expert')]
    private Collection $expert;

    /**
     * This is OWNER of this relation, unidirectional.
     *
     * @var Collection<string|int,Image>
     */
    #[ORM\ManyToMany(targetEntity: Image::class, cascade: ['all'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'media_image')]
    private Collection $image;

    public function __construct(?UuidInterface $id = null)
    {
        $this->id = $id ?? Uuid::uuid4();
        $this->expert = new ArrayCollection();
        $this->image = new ArrayCollection();
    }

    public function getIdentifier(): string
    {
        return $this->id->toString();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getExpert(): Collection
    {
        return $this->expert;
    }

    public function getImage(): Collection
    {
        return $this->image;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function addExpert(Expert $expert): void
    {
        if ($this->expert->contains($expert)) {
            return;
        }

        $this->expert->add($expert);
    }

    public function removeExpert(Expert $expert): void
    {
        if ($this->expert->contains($expert)) {
            $this->expert->removeElement($expert);
        }
    }

    public function addImage(Image $image): void
    {
        if ($this->image->contains($image)) {
            return;
        }

        $this->image->add($image);
    }

    public function removeImage(Image $image): void
    {
        if ($this->image->contains($image)) {
            $this->image->removeElement($image);
        }
    }
}
