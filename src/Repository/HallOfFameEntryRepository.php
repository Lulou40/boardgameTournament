<?php

namespace App\Repository;

use App\Entity\Group;

 use App\Entity\User;
use App\Entity\HallOfFameEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HallOfFameEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HallOfFameEntry::class);
    }

    /**
     * Hall of Fame d’un groupe
     */
    public function getHallOfFame(Group $group): array
    {
        return $this->createQueryBuilder('hof')
            ->join('hof.user', 'u')
            ->where('hof.group = :group')
            ->setParameter('group', $group)
            ->orderBy('hof.totalPointsGlobal', 'DESC')
            ->addOrderBy('hof.seasonsCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Position globale d’un joueur
     */

public function getUserRank(Group $group, User $user): ?int
{
    $result = $this->createQueryBuilder('hof')
        ->select('hof.globalRank AS globalRank')
        ->where('hof.group = :group')
        ->andWhere('hof.user = :user')
        ->setParameter('group', $group)
        ->setParameter('user', $user)
        ->getQuery()
        ->getOneOrNullResult();

    return $result['globalRank'] ?? null;
}
}
