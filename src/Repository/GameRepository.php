<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findOneByBggId(string $bggId): ?Game
    {
        return $this->createQueryBuilder('g')
            ->where('g.bggId = :bggId')
            ->setParameter('bggId', $bggId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
