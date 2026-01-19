<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'saison')]
#[ORM\Index(name: 'idx_season_group_status', columns: ['groupe_id', 'statut'])]
class Season
{
    public const STATUS_PLANNED = 'planifiee';
    public const STATUS_ACTIVE = 'en_cours';
    public const STATUS_FINISHED = 'terminee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'seasons')]
    #[ORM\JoinColumn(name: 'groupe_id', nullable: false, onDelete: 'CASCADE')]
    private Group $group;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(name: 'statut', length: 20)]
    private string $status = self::STATUS_PLANNED;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $pointsRules = null;

    /** @var Collection<int, SeasonParticipation> */
    #[ORM\OneToMany(mappedBy: 'season', targetEntity: SeasonParticipation::class, orphanRemoval: true)]
    private Collection $participations;

    /** @var Collection<int, Tournament> */
    #[ORM\OneToMany(mappedBy: 'season', targetEntity: Tournament::class, orphanRemoval: true)]
    private Collection $tournaments;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->tournaments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
    public function setGroup(Group $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
