<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[ORM\UniqueConstraint(name: 'uq_user_email', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'uq_user_pseudo', columns: ['pseudo'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $pseudo;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $passwordHash;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, GroupMember> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: GroupMember::class, orphanRemoval: true)]
    private Collection $groupMemberships;

    /** @var Collection<int, GameCopy> */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: GameCopy::class, orphanRemoval: true)]
    private Collection $gameCopies;

    /** @var Collection<int, SeasonParticipation> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SeasonParticipation::class, orphanRemoval: true)]
    private Collection $seasonParticipations;

    /** @var Collection<int, MatchScore> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MatchScore::class, orphanRemoval: true)]
    private Collection $matchScores;

    /** @var Collection<int, SeasonPlayerStat> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SeasonPlayerStat::class, orphanRemoval: true)]
    private Collection $seasonStats;

    /** @var Collection<int, HallOfFameEntry> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: HallOfFameEntry::class, orphanRemoval: true)]
    private Collection $hallOfFameEntries;

    /** @var Collection<int, UnlockedAchievement> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UnlockedAchievement::class, orphanRemoval: true)]
    private Collection $unlockedAchievements;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->groupMemberships = new ArrayCollection();
        $this->gameCopies = new ArrayCollection();
        $this->seasonParticipations = new ArrayCollection();
        $this->matchScores = new ArrayCollection();
        $this->seasonStats = new ArrayCollection();
        $this->hallOfFameEntries = new ArrayCollection();
        $this->unlockedAchievements = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getPseudo(): string { return $this->pseudo; }
    public function setPseudo(string $pseudo): self { $this->pseudo = $pseudo; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->passwordHash; }
    public function setPasswordHash(string $hash): self { $this->passwordHash = $hash; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    // --- Security ---
    public function getRoles(): array { return ['ROLE_USER']; }
    public function eraseCredentials(): void {}
    public function getUserIdentifier(): string { return $this->email; }

    /** @return Collection<int, GroupMember> */
    public function getGroupMemberships(): Collection { return $this->groupMemberships; }

    /** @return Collection<int, GameCopy> */
    public function getGameCopies(): Collection { return $this->gameCopies; }
}
