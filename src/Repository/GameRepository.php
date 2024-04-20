<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findLatestDataLast24Hours(?string $place = null): array
    {
        $now = new \DateTime();
        $twentyFourHoursAgo = (new \DateTime())->modify('-24 hours');

        $qb = $this->createQueryBuilder('g')
            ->where('g.createdAt BETWEEN :twentyFourHoursAgo AND :now')
            ->setParameter('twentyFourHoursAgo', $twentyFourHoursAgo)
            ->setParameter('now', $now);

        if ($place) {
            $qb->andWhere('g.place = :place')
                ->setParameter('place', $place);
        }

        $qb->orderBy('g.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function checkIfPlaceExists($place)
    {
        // Votre logique pour vérifier si le lieu existe dans la base de données
        $result = $this->createQueryBuilder('g')
            ->andWhere('g.place = :place')
            ->setParameter('place', $place)
            ->getQuery()
            ->getOneOrNullResult();

        return $result !== null;
    }
}
