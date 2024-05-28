<?php

namespace App\Repository;

use App\Entity\Viasdeadministracion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Viasdeadministracion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Viasdeadministracion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Viasdeadministracion[]    findAll()
 * @method Viasdeadministracion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViasdeadministracionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Viasdeadministracion::class);
    }

    // /**
    //  * @return Viasdeadministracion[] Returns an array of Viasdeadministracion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Viasdeadministracion
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
