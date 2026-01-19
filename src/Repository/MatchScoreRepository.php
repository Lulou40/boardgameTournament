<?php

namespace App\Repository;

use App\Entity\MatchScore;
use App\Entity\Season;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MatchScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchScore::class);
    }

    /**
     * Total des points de classement d’un joueur sur une saison
     */
    public function getTotalRankingPointsForSeason(User $user, Season $season): int
{
    return (int) $this->createQueryBuilder('ms')
        ->select('COALESCE(SUM(ms.rankingPoints), 0)')
        ->join('ms.match', 'm')
        ->join('m.tournament', 't')
        ->where('ms.user = :user')
        ->andWhere('t.season = :season')
        ->andWhere('m.status = :status')
        ->setParameter('user', $user)
        ->setParameter('season', $season)
        ->setParameter('status', 'valide')
        ->getQuery()
        ->getSingleScalarResult();
}

    /**
     * Nombre de victoires sur une saison (position = 1)
     */
    public function countWinsForSeason(User $user, Season $season): int
    {
        return (int) $this->createQueryBuilder('ms')
            ->select('COUNT(ms)')
            ->join('ms.match', 'm')
            ->join('m.tournament', 't')
            ->where('ms.user = :user')
            ->andWhere('t.season = :season')
            ->andWhere('ms.position = 1')
            ->andWhere('m.status = :status')
            ->setParameter('user',  $user)
            ->setParameter('season', $season)
            ->setParameter('status', 'valide')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Nombre de matchs joués sur une saison
     */
    public function countMatchesForSeason(User $user, Season $season): int
    {
        return (int) $this->createQueryBuilder('ms')
            ->select('COUNT(ms)')
            ->join('ms.match', 'm')
            ->join('m.tournament', 't')
            ->where('ms.user = :user')
            ->andWhere('t.season = :season')
            ->andWhere('m.status = :status')
            ->setParameter('user',  $user)
            ->setParameter('season', $season)
            ->setParameter('status', 'valide')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
