<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'match')]
#[ORM\Index(name: 'idx_match_tournament_date', columns: ['tournoi_id', 'date_match'])]
class GameMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'matches')]
    #[ORM\JoinColumn(name: 'tournoi_id', nullable: false, onDelete: 'CASCADE')]
    private Tournament $tournament;

    #[ORM\Column(name: 'date_match', type: 'datetime_immutable')]
    private \DateTimeImmutable $playedAt;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $tableNumber = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $roundNumber = null;

    #[ORM\Column(length: 20)]
    private string $status = 'en_attente';

    /** @var Collection<int, MatchScore> */
    #[ORM\OneToMany(mappedBy: 'match', targetEntity: MatchScore::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $scores;

    public function __construct()
    {
        $this->scores = new ArrayCollection();
        $this->playedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTournament(): Tournament { return $this->tournament; }
    public function setTournament(Tournament $tournament): self { $this->tournament = $tournament; return $this; }

    /** @return Collection<int, MatchScore> */
    public function getScores(): Collection { return $this->scores; }
}
