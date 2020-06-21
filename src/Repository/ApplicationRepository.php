<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }
    //
    /// Pour notre Plateforme d'Annonces
    /// La méthode ci-dessous permet de récupérer les X dernières candidatures
    /// avec leur annonce associée.
    public function getApplicationsWithAdvert($limit)
    {
        //On récupère les X dernières Candidatures
        $qb=$this
            ->createQueryBuilder('a')
            ->innerJoin('a.advert', 'adv') //On fait une jointure avec l'entité Advert avec pour alias "adv"
            ->addSelect('adv') //On sélectionne l'entité AdvertRappel avec "addSelect" pour ne pas écraser le "select('a')"
            ->setMaxResults($limit) //On retourne les $limit résultats
            ->orderBy('a.date','DESC')
            ;
        ///
        /// On retourn le résultat
        return $qb->getQuery()->getResult();


    }

    // /**
    //  * @return Application[] Returns an array of Application objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
