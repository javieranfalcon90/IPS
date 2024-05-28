<?php

namespace App\Repository;

use App\Entity\Motivodepresentacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Motivodepresentacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motivodepresentacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motivodepresentacion[]    findAll()
 * @method Motivodepresentacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotivodepresentacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motivodepresentacion::class);
    }

    // /**
    //  * @return Motivodepresentacion[] Returns an array of Motivodepresentacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Motivodepresentacion
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
