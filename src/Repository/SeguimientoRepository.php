<?php

namespace App\Repository;

use App\Entity\Seguimiento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Seguimiento|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seguimiento|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seguimiento[]    findAll()
 * @method Seguimiento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeguimientoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seguimiento::class);
    }

    // /**
    //  * @return Seguimiento[] Returns an array of Seguimiento objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Seguimiento
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
