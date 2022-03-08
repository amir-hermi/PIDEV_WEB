<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    // /**
    //  * @return Commande[] Returns an array of Commande objects
    //  */

    public function findNewCommande()
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->select('count(c.date_creation)')
            ->andWhere('c.date_creation = :val')
            ->setParameter('val', $date->format('y-m-j'))
            ->getQuery()->getSingleScalarResult()

        ;
    }

    public function findCommandesTritée()
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->select('count(c.status)')
            ->andWhere('c.status != :val')
            ->andWhere('c.date_creation = :val1')
            ->setParameter('val', 'En attente' )
            ->setParameter('val1', $date->format('y-m-j'))
            ->getQuery()->getSingleScalarResult()
            ;
    }

    public function findCommandesNonTritée()
    {
        $date = new \DateTime();
        return $this->createQueryBuilder('c')
            ->select('count(c.status)')
            ->andWhere('c.status = :val')
            ->andWhere('c.date_creation = :val1')
            ->setParameter('val', 'En attente' )
            ->setParameter('val1', $date->format('y-m-j'))
            ->getQuery()->getSingleScalarResult()
            ;
    }

    public function rechercheParRef($value)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.utilisateur', 'u', "WITH", 'c.utilisateur = u.id')
            ->andWhere('c.reference = :val')
            ->orWhere('u.username = :val1')
            ->orWhere('u.tel like :val2')
            ->setParameter('val', $value )
            ->setParameter('val1', $value )
            ->setParameter('val2', '%'.$value )
            ->orderBy('c.date_creation','DESC')
            ->getQuery()->getResult()
            ;
    }

    public function filtreCommande($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :val')
            ->setParameter('val', $value )
            ->orderBy('c.date_creation','DESC')
            ->getQuery()->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Commande
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
