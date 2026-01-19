<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'participation_saison')]
#[ORM\UniqueConstraint(name: 'uq_season_participation', columns: ['saison_id', 'utilisateur_id'])]
#[ORM\Index(name: 'idx_participation_user', columns: ['utilisateur_id'])]
class SeasonParticipation
{
    public const STATUS_ACTIVE = 'actif';
    public const STATUS_DROPPED = 'abandon';

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'participations')]
    #[ORM\JoinColumn(name: 'saison_id', nullable: false, onDelete: 'CASCADE')]
    private Season $season;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'seasonParticipations')]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $registeredAt;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ACTIVE;

    public function __construct(Season $season, User $user)
    {
        $this->season = $season;
        $this->user = $user;
        $this->registeredAt = new \DateTimeImmutable();
    }

    public function getSeason(): Season { return $this->season; }
    public function getUser(): User { return $this->user; }

    public function getRegisteredAt(): \DateTimeImmutable { return $this->registeredAt; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
}
