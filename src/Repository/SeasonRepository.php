<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function findActiveSeasonForGroup(Group $group): ?Season
    {
        return $this->createQueryBuilder('s')
            ->where('s.group = :group')
            ->andWhere('s.status = :status')
            ->setParameter('group', $group)
            ->setParameter('status', Season::STATUS_ACTIVE)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFinishedSeasons(Group $group): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.group = :group')
            ->andWhere('s.status = :status')
            ->setParameter('group', $group)
            ->setParameter('status', Season::STATUS_FINISHED)
            ->orderBy('s.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
