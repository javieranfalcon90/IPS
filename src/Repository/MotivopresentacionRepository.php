<?php

namespace App\Repository;

use App\Entity\Motivopresentacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Motivopresentacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motivopresentacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motivopresentacion[]    findAll()
 * @method Motivopresentacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotivopresentacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motivopresentacion::class);
    }

    // /**
    //  * @return Motivopresentacion[] Returns an array of Motivopresentacion objects
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
    public function findOneBySomeField($value): ?Motivopresentacion
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
