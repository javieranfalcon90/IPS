<?php

namespace App\Repository;

use App\Entity\Seccionfaltante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Seccionfaltante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seccionfaltante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seccionfaltante[]    findAll()
 * @method Seccionfaltante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeccionfaltanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seccionfaltante::class);
    }

    // /**
    //  * @return Seccionfaltante[] Returns an array of Seccionfaltante objects
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
    public function findOneBySomeField($value): ?Seccionfaltante
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
