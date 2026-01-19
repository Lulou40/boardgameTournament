<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'stat_saison_joueur')]
#[ORM\UniqueConstraint(name: 'uq_stat_season_user', columns: ['saison_id', 'utilisateur_id'])]
#[ORM\Index(name: 'idx_stat_rank', columns: ['saison_id', 'rang_final'])]
class SeasonPlayerStat
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Season::class)]
    #[ORM\JoinColumn(name: 'saison_id', nullable: false, onDelete: 'CASCADE')]
    private Season $season;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'seasonStats')]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'integer')]
    private int $totalPoints = 0;

    #[ORM\Column(type: 'integer')]
    private int $totalWins = 0;

    #[ORM\Column(type: 'integer')]
    private int $totalMatches = 0;

    #[ORM\Column(name: 'rang_final', type: 'integer', nullable: true)]
    private ?int $finalRank = null;

    public function __construct(Season $season, User $user)
    {
        $this->season = $season;
        $this->user = $user;
    }
}
