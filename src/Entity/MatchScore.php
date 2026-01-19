<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'score_match')]
#[ORM\UniqueConstraint(name: 'uq_match_user', columns: ['match_id', 'utilisateur_id'])]
#[ORM\Index(name: 'idx_score_user', columns: ['utilisateur_id'])]
class MatchScore
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: GameMatch::class, inversedBy: 'scores')]
    #[ORM\JoinColumn(name: 'match_id', nullable: false, onDelete: 'CASCADE')]
    private GameMatch $match;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'matchScores')]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'integer')]
    private int $score = 0;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $position = null;

    #[ORM\Column(type: 'integer')]
    private int $rankingPoints = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isForfeit = false;

    public function __construct(GameMatch $match, User $user)
    {
        $this->match = $match;
        $this->user = $user;
    }

    public function getMatch(): GameMatch { return $this->match; }
    public function getUser(): User { return $this->user; }

    public function getScore(): int { return $this->score; }
    public function setScore(int $score): self { $this->score = $score; return $this; }

    public function getRankingPoints(): int { return $this->rankingPoints; }
    public function setRankingPoints(int $points): self { $this->rankingPoints = $points; return $this; }
}
