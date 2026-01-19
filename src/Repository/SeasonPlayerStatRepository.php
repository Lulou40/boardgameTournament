<?php

namespace App\Repository;

use App\Entity\Season;
use App\Entity\SeasonPlayerStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SeasonPlayerStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeasonPlayerStat::class);
    }

    /**
     * Classement complet d’une saison
     */
    public function getSeasonRanking(Season $season): array
    {
        return $this->createQueryBuilder('sps')
            ->join('sps.user', 'u')
            ->where('sps.season = :season')
            ->setParameter('season', $season)
            ->orderBy('sps.totalPoints', 'DESC')
            ->addOrderBy('sps.totalWins', 'DESC')
            ->addOrderBy('sps.totalMatches', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Top N joueurs d’une saison
     */
    public function getSeasonTop(Season $season, int $limit = 3): array
    {
        return $this->createQueryBuilder('sps')
            ->where('sps.season = :season')
            ->setParameter('season', $season)
            ->orderBy('sps.totalPoints', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
