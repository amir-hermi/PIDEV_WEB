<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function numberOfclient(){
        $entityManager = $this->getEntityManager();
        $query=$entityManager
            ->createQuery('SELECT count(p) FROM APP\Entity\Utilisateur p');
        return $query->getSingleScalarResult();
    }

    // /**
    //  * @return Utilisateur[] Returns an array of Utilisateur objects
    //  */

    public function Bloqueutilisateur()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.etat)')
            ->andWhere('u.etat = :val')
            ->setParameter('val', 'Bloquer')
            ->getQuery()->getSingleScalarResult()

            ;
    }

    public function totaleutilisateur()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.username)')
            ->andWhere('u.username != :val')
            ->setParameter('val', '')
            ->getQuery()->getSingleScalarResult()

            ;
    }


    public function Connecteutilisateur()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.activetoken)')
            ->andWhere('u.activetoken != :val')
            ->setParameter('val', '')
            ->getQuery()->getSingleScalarResult();

    }

    public function RoleUtilisateur()
    { $role = ['ROLE_USER'];
        return $this->createQueryBuilder('u')
            ->select('count(u.roles)')
            ->andWhere('u.roles = :val')
            ->setParameter('val', $role  )
            ->getQuery()->getSingleScalarResult();

    }



    public function findOneBySomeField()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles like :v')
            ->setParameter('v', '%'.'ROLE_LIVREUR'.'%')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function rechercheClient($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :val')
            ->orWhere('u.email = :val1')
            ->orWhere('u.etat =:val2')
            ->orWhere('u.lastname =:val3')
            ->setParameter('val', $value )
            ->setParameter('val1', $value )
            ->setParameter('val2', $value )
            ->setParameter('val3', $value )

            ->getQuery()->getResult()
            ;
    }
}
