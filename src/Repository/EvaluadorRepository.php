<?php

namespace App\Repository;

use App\Entity\Evaluador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evaluador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluador[]    findAll()
 * @method Evaluador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluador::class);
    }

    // /**
    //  * @return Evaluador[] Returns an array of Evaluador objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evaluador
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
