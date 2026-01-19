<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'hall_of_fame')]
#[ORM\UniqueConstraint(name: 'uq_hof_group_user', columns: ['groupe_id', 'utilisateur_id'])]
#[ORM\Index(name: 'idx_hof_rank', columns: ['groupe_id', 'rang_global'])]
class HallOfFameEntry
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'hallOfFameEntries')]
    #[ORM\JoinColumn(name: 'groupe_id', nullable: false, onDelete: 'CASCADE')]
    private Group $group;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'hallOfFameEntries')]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'integer')]
    private int $totalPointsGlobal = 0;

    #[ORM\Column(type: 'integer')]
    private int $seasonsCount = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $titlesCount = null;

    #[ORM\Column(name: 'rang_global', type: 'integer', nullable: true)]
    private ?int $globalRank = null;

    public function __construct(Group $group, User $user)
    {
        $this->group = $group;
        $this->user = $user;
    }
}
