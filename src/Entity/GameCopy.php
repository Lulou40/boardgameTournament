<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'exemplaire_jeu')]
#[ORM\Index(name: 'idx_copy_owner', columns: ['proprietaire_id'])]
#[ORM\Index(name: 'idx_copy_game', columns: ['jeu_id'])]
class GameCopy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: 'jeu_id', nullable: false, onDelete: 'RESTRICT')]
    private Game $game;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'gameCopies')]
    #[ORM\JoinColumn(name: 'proprietaire_id', nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $conditionState = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $purchaseDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int { return $this->id; }

    public function getGame(): Game { return $this->game; }
    public function setGame(Game $game): self { $this->game = $game; return $this; }

    public function getOwner(): User { return $this->owner; }
    public function setOwner(User $owner): self { $this->owner = $owner; return $this; }
}
