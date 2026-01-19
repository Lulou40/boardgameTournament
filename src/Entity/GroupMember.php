<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'membre_groupe')]
#[ORM\UniqueConstraint(name: 'uq_group_member', columns: ['groupe_id', 'utilisateur_id'])]
#[ORM\Index(name: 'idx_member_user', columns: ['utilisateur_id'])]
class GroupMember
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MEMBER = 'membre';

    public const STATUS_ACTIVE = 'actif';
    public const STATUS_INVITED = 'invite';

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(name: 'groupe_id', nullable: false, onDelete: 'CASCADE')]
    private Group $group;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'groupMemberships')]
    #[ORM\JoinColumn(name: 'utilisateur_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 20)]
    private string $role = self::ROLE_MEMBER;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $joinedAt;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ACTIVE;

    public function __construct(Group $group, User $user)
    {
        $this->group = $group;
        $this->user = $user;
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function getGroup(): Group { return $this->group; }
    public function getUser(): User { return $this->user; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }

    public function getJoinedAt(): \DateTimeImmutable { return $this->joinedAt; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
}
