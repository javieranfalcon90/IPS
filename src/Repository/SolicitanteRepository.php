<?php

namespace App\Repository;

use App\Entity\Solicitante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Solicitante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Solicitante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Solicitante[]    findAll()
 * @method Solicitante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolicitanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Solicitante::class);
    }

    // /**
    //  * @return Solicitante[] Returns an array of Solicitante objects
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
    public function findOneBySomeField($value): ?Solicitante
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
