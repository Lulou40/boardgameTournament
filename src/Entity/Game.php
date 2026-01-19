<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: 'jeu')]
#[ORM\UniqueConstraint(name: 'uq_game_bgg', columns: ['bgg_id'])]
#[ORM\Index(name: 'idx_game_name', columns: ['name'])]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $year = null;

    #[ORM\Column(name: 'nb_joueurs_min', type: 'smallint', nullable: true)]
    private ?int $playersMin = null;

    #[ORM\Column(name: 'nb_joueurs_max', type: 'smallint', nullable: true)]
    private ?int $playersMax = null;

    #[ORM\Column(name: 'duree_moyenne', type: 'smallint', nullable: true)]
    private ?int $avgDurationMinutes = null;

    #[ORM\Column(name: 'bgg_id', length: 50, nullable: true)]
    private ?string $bggId = null;

    #[ORM\Column(name: 'bgg_rank', type: 'integer', nullable: true)]
    private ?int $bggRank = null;

    #[ORM\Column(name: 'bgg_average', type: 'float', nullable: true)]
    private ?float $bggAverage = null;

    #[ORM\Column(name: 'bgg_users_rated', type: 'integer', nullable: true)]
    private ?int $bggUsersRated = null;

    #[ORM\Column(name: 'is_expansion', type: 'boolean', options: ['default' => false])]
    private bool $isExpansion = false;

    #[ORM\Column(name: 'image_url', length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(name: 'thumbnail_url', length: 500, nullable: true)]
    private ?string $thumbnailUrl = null;



    /* =======================
       Getters / Setters
       ======================= */

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getPlayersMin(): ?int
    {
        return $this->playersMin;
    }

    public function setPlayersMin(?int $playersMin): self
    {
        $this->playersMin = $playersMin;
        return $this;
    }

    public function getPlayersMax(): ?int
    {
        return $this->playersMax;
    }

    public function setPlayersMax(?int $playersMax): self
    {
        $this->playersMax = $playersMax;
        return $this;
    }

    public function getAvgDurationMinutes(): ?int
    {
        return $this->avgDurationMinutes;
    }

    public function setAvgDurationMinutes(?int $avgDurationMinutes): self
    {
        $this->avgDurationMinutes = $avgDurationMinutes;
        return $this;
    }

    public function getBggId(): ?string
    {
        return $this->bggId;
    }

    public function setBggId(?string $bggId): self
    {
        $this->bggId = $bggId;
        return $this;
    }

    public function getBggRank(): ?int
    {
        return $this->bggRank;
    }

    public function setBggRank(?int $bggRank): self
    {
        $this->bggRank = $bggRank;
        return $this;
    }

    public function getBggAverage(): ?float
    {
        return $this->bggAverage;
    }

    public function setBggAverage(?float $bggAverage): self
    {
        $this->bggAverage = $bggAverage;
        return $this;
    }

    public function getBggUsersRated(): ?int
    {
        return $this->bggUsersRated;
    }

    public function setBggUsersRated(?int $bggUsersRated): self
    {
        $this->bggUsersRated = $bggUsersRated;
        return $this;
    }

    public function isExpansion(): bool
    {
        return $this->isExpansion;
    }

    public function setIsExpansion(bool $isExpansion): self
    {
        $this->isExpansion = $isExpansion;
        return $this;
    }


    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
    public function setImageUrl(?string $url): self
    {
        $this->imageUrl = $url;
        return $this;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }
    public function setThumbnailUrl(?string $url): self
    {
        $this->thumbnailUrl = $url;
        return $this;
    }
}
