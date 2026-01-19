<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'succes')]
#[ORM\UniqueConstraint(name: 'uq_achievement_code', columns: ['code'])]
class Achievement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private string $code;

    #[ORM\Column(length: 140)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    private string $type; // saison|global

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $condition = null;

    public function getId(): ?int { return $this->id; }
}
