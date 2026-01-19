<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'succes_debloque')]
#[ORM\UniqueConstraint(name: 'uq_unlock_achievement_user_season', columns: ['succes_id', 'utilisateur_id', 'saison_id'])]
#[ORM\Index(name: 'idx_unlock_user', columns: ['utilisateur_id'])]
class UnlockedAchievement
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Achievement::class)]
    #[ORM\JoinColumn(name: 'succes_id', nullable: false, onDelete: 'CASCADE')]
    private Achievement $achievement;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'unlockedAchievements')]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Season::class)]
    #[ORM\JoinColumn(name: 'saison_id', nullable: true, onDelete: 'SET NULL')]
    private ?Season $season = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $unlockedAt;

    public function __construct(Achievement $achievement, User $user, ?Season $season = null)
    {
        $this->achievement = $achievement;
        $this->user = $user;
        $this->season = $season;
        $this->unlockedAt = new \DateTimeImmutable();
    }
}
