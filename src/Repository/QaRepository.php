<?php

namespace App\Repository;

use App\Entity\Qa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Qa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Qa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Qa[]    findAll()
 * @method Qa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Qa::class);
    }

    // /**
    //  * @return Qa[] Returns an array of Qa objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Qa
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
