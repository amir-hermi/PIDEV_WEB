<?php

namespace App\Repository;

use App\Entity\CategorieReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieReclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieReclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieReclamation[]    findAll()
 * @method CategorieReclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieReclamation::class);
    }

    // /**
    //  * @return CategorieReclamation[] Returns an array of CategorieReclamation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorieReclamation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
