<?php

namespace App\Repository;

use App\Entity\Commandestock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commandestock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commandestock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commandestock[]    findAll()
 * @method Commandestock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandestockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandestock::class);
    }

    // /**
    //  * @return Commandestock[] Returns an array of Commandestock objects
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
    public function findOneBySomeField($value): ?Commandestock
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    function OrderByDatevalid(){
        return $this->createQueryBuilder('c')
            ->orderBy('c.date','DESC')
            ->setMaxResults(1)
            ->getQuery()->getResult();
        //valid
    }
    function OrderByQuantite(){
        return $this->createQueryBuilder('c')
            ->orderBy('c.quantite','ASC')
            //->setMaxResults(1)
            ->getQuery()->getResult();
        //notvalid
    }

}
