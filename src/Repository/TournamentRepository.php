<?php

namespace App\Repository;

use App\Entity\Season;
use App\Entity\Tournament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TournamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournament::class);
    }

    public function findBySeason(Season $season): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.season = :season')
            ->setParameter('season', $season)
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
