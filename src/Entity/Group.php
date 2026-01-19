<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'groupe')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, GroupMember> */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: GroupMember::class, orphanRemoval: true)]
    private Collection $memberships;

    /** @var Collection<int, Season> */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Season::class, orphanRemoval: true)]
    private Collection $seasons;

    /** @var Collection<int, HallOfFameEntry> */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: HallOfFameEntry::class, orphanRemoval: true)]
    private Collection $hallOfFameEntries;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->memberships = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->hallOfFameEntries = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, Season> */
    public function getSeasons(): Collection { return $this->seasons; }
}
