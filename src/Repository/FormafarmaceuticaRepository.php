<?php

namespace App\Repository;

use App\Entity\Formafarmaceutica;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Formafarmaceutica|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formafarmaceutica|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formafarmaceutica[]    findAll()
 * @method Formafarmaceutica[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormafarmaceuticaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formafarmaceutica::class);
    }

    // /**
    //  * @return Formafarmaceutica[] Returns an array of Formafarmaceutica objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Formafarmaceutica
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
