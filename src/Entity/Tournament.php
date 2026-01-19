<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tournoi')]
#[ORM\Index(name: 'idx_tournament_season', columns: ['saison_id'])]
#[ORM\Index(name: 'idx_tournament_game', columns: ['jeu_id'])]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'tournaments')]
    #[ORM\JoinColumn(name: 'saison_id', nullable: false, onDelete: 'CASCADE')]
    private Season $season;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'jeu_id', nullable: false, onDelete: 'RESTRICT')]
    private Game $game;

    #[ORM\Column(length: 140)]
    private string $name;

    #[ORM\Column(length: 30)]
    private string $format; // suisse, round_robin, elim…

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(length: 20)]
    private string $status = 'planifie';

    /** @var Collection<int, GameMatch> */
    #[ORM\OneToMany(mappedBy: 'tournament', targetEntity: GameMatch::class, orphanRemoval: true)]
    private Collection $matches;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getSeason(): Season { return $this->season; }
    public function setSeason(Season $season): self { $this->season = $season; return $this; }

    public function getGame(): Game { return $this->game; }
    public function setGame(Game $game): self { $this->game = $game; return $this; }
}
