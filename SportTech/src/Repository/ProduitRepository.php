<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */

    public function countNike($idmarque)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.marque', 'm', "WITH", 'p.marque = m.id')
            ->select('count(m.libelle) , m.libelle')
            ->andWhere('m.id = :val')
            ->setParameter('val', $idmarque)
            ->groupBy('m.libelle')
            ->getQuery()->getResult()
            ;
    }
    public function countAdidas()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.marque', 'm', "WITH", 'p.marque = m.id')
            ->select('count(m.libelle)')
            ->andWhere('m.libelle = :val')
            ->setParameter('val', 'Adidas')
            ->getQuery()->getSingleScalarResult()
            ;
    }

    public function countPuma()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.marque', 'm', "WITH", 'p.marque = m.id')
            ->select('count(m.libelle)')
            ->andWhere('m.libelle = :val')
            ->setParameter('val', 'Puma')
            ->getQuery()->getSingleScalarResult()
            ;
    }
    public function ListProduitParSousCategorie($categorie , $souscategorie)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.sousCategire', 's', "WITH", 'p.sousCategire = s.id')
            ->innerJoin('s.categorie', 'c', "WITH", 's.categorie = c.id')
            ->andWhere('c.libelle = :valc')
            ->andWhere('s.libelle = :val')
            ->setParameter('valc', $categorie)
            ->setParameter('val', $souscategorie)
            ->getQuery()->getResult()
            ;
    }





    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
