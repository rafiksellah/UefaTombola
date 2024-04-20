<?php

namespace App\Repository;

use App\Entity\GiftQuantity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftQuantity>
 *
 * @method GiftQuantity|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftQuantity|null findOneBy(array $criteria, array $orderBy = null)
 * @method GiftQuantity[]    findAll()
 * @method GiftQuantity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GiftQuantityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftQuantity::class);
    }

//    /**
//     * @return GiftQuantity[] Returns an array of GiftQuantity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GiftQuantity
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
