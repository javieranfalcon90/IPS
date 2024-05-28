<?php

namespace App\Repository;

use App\Entity\Titular;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Titular|null find($id, $lockMode = null, $lockVersion = null)
 * @method Titular|null findOneBy(array $criteria, array $orderBy = null)
 * @method Titular[]    findAll()
 * @method Titular[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TitularRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Titular::class);
    }

    // /**
    //  * @return Titular[] Returns an array of Titular objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Titular
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
