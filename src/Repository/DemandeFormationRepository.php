<?php

namespace App\Repository;

use App\Entity\DemandeFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DemandeFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeFormation[]    findAll()
 * @method DemandeFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeFormationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DemandeFormation::class);
    }

    // /**
    //  * @return DemandeFormation[] Returns an array of DemandeFormation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DemandeFormation
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
