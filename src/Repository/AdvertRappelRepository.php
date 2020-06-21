<?php

namespace App\Repository;

use App\Entity\AdvertRappel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method AdvertRappel|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvertRappel|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvertRappel[]    findAll()
 * @method AdvertRappel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRappelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvertRappel::class);
    }
    ///
    ///On récupère les 3 Dernières Annonces enregistrées dans
    ///  la Base de Données
    //
    public function dernieresAnnonces()
    {
        $qb = $this
            ->createQueryBuilder('ar')
            ->setMaxResults(3)
            ->orderBy('ar.date', 'DESC')
            ->orderBy('ar.id', 'DESC')
        ;

        //
        return $qb->getQuery()->getResult();

    }
    ///
    /// On récupère toutes les entités triées par date
    ///Dans cette méthode, on va charger toutes les infos sur les Annonces
    /// afin d'éviter les dizaines de requêtes supplémentaires
    /// On doit afficher les données de l'entité "image" et des entités "category" liées à chaque annonce
    /// d'où il faut donc rajouter les jointures sur ces 2 entités
    ///
    /// on a ajouté deux arguments à cette méthode, on a besoin
    /// de la page actuelle ainsi que du nombre d'annonces par page
    /// afin de savoir quelles annonces récupérer exactement
    public function getAdverts($page, $nbParPage)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.image', 'im') //Jointure sur l'attribut "image"
            ->addSelect('im')
            ->leftJoin('a.categories', 'cat') //Jointure sur l'attribut "categories"
            ->addSelect('cat')
            ->orderBy('a.date', 'DESC')
            ->orderBy('a.id','DESC')
            ->getQuery();
        //
        $query
            //On définit l'annonce à partir de laquelle commencer la liste
            ->setFirstResult(($page - 1) * $nbParPage)
            //Ainsi que le nombre d'annonces à afficher sur une page
            ->setMaxResults($nbParPage);
        ///
        ///Enfin, on retourne l'objet "Paginator" correspondant à la requête construite
        return new Paginator($query);
        ///La Classe a un deuxième argurment qui est Facultatif et sa valeur est deja à "true" par défaut
        ///
        /// Cet Objet "Paginator" retourne une liste de "$nbParPage" annonces
        /// qui s'utilise comme n'importe quel tableau habituel
        /// NOTA: si on fait un "count" sur cette liste retournée,
        /// on n'obtiendra pas $nbParPage, mais le nombre total d'annonces présentes en BD
        /// cette information est importante pour calculer le nombre total de pages

        //
       // return $query->getQuery()->getResult();
    }
    ///
    ///Cette méthode permet de renvoyer le résultat suivant
///
///
///
///
///
///

    ///LES METHODES au-dessus, sont issues de TP
    /// LES METHODES EN DESSOUS sont issues de l'apprentissage
    ///
    //Le QueryBuilder, La Query et Les résultats
    public function myFindAll()
    {
        //1. Méthode 1: En passant par l'EntityManager (Méthode non-recommandée)
        //Le QueryBuilder
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('a') //'a' est un alias qui représente l'entité (AdvertRappel) dont dépend le repositpry AdvertRappelRepository
            ->from($this->_entityName, 'a');
        //
        ///Dans un repository, $this->_entityName est le namespace de l'entité gérée
        ///Ici, il vaut donc App\Entity\AdvertRappel
        ///
        ///  //2. Méthode 2: En passant par le raccourci (Méthode Recommandée)
        /// C'est la méthode ci-dessous qu'on va utiliser
        /// Elle se décrit en 3 étapes:

        /// a) On crée le QueryBuilder
        $queryBuilder = $this->createQueryBuilder('a');
        ///Avec cette deuxième méthode, on n'ajoute pas de critère ou tri particulier...
        /// Seuelemnt avec cette ligne de code, notre requête est finie
        ///
        /// b) On récupère la QUERY à partir du QueryBuilder
        $query = $queryBuilder->getQuery();
        //
        //c) On récupère les résultats à partir de la Query
        $results = $query->getResult();
        //
        ///On peut résumer la méthode 2 Simplement comme ceci

        $results = $this->createQueryBuilder('a')->getQuery()->getResult();

        /*
            try{
                $results=$query->getOneOrNullResult();
            } catch (NonUniqueResultException $e){
                //
                echo "Une Erreur Inconnue s'est Produite!!";

            }
    */

        /*
        $results=$query->getOneOrNullResult();
        if ($results== new NonUniqueResultException){
            //
             throw new NotFoundHttpException("Une Erreur Inconnue s'est Produite!!");
            //
        }
        */
        //On retourne ces résultats
        return $results;
        ///NOTA: Cette Méthode myFindAll() retourne exactement le même résultat
        /// Qu'un findAll(), i.e un tableau de toutes les entités AdvertRappel
        /// dans notre Base de Données
        /// POUR RECUPERER ces résultats depuis un contrôleur, il faut faire
        /// comme avec n'importe quelle autre méthode du repository
    }
    //
    /// NOTA: Le QueryBuilder dispose de plusieurs méthodes
    ///  afin de constuire une requête.
    /// Il y a une ou plusieurs méthodes par partie de requête:
    ///  le WHERE, le ORDER BY, le FROM, etc.
    /// Voyons cela dans la méthode ci-dessous
    //
    public function myFindOne($id)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.id=:id') // :nom_du_paramètre
            ->setParameter('id', $id);
        //
        return $qb->getQuery()->getResult();
    }
    //
    ///Voici un autre exemple pour utiliser le "andWhere()"
    ///  ainsi que le "orderBy"
    /// Pour cela, créons une méthode pour récupérer toutes
    ///  les annonces écrites par un auteur avant une année donnée
    ///
    public function findByAuthorAndDate($author, $year)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.author=:author')
            ->setParameter('author', $author)
            ->andWhere('a.date < :year')
            ->setParameter('year', $year)
            ->orderBy('a.date', 'DESC');
        //
        return $qb->getQuery()->getResult();
        //
    }
    ///
    /// Avec le QueryBuilder, il est pratique de créer des requêtes avec plein de conditions/Jointures, etc.
    /// Voyons donc une application de ce principe, en considérant que la condition
    /// "annonces postées durant l'année en cours" est une condition dont on va se resservir souvent

    public function whereCurrentYear(QueryBuilder $qb)
    {
        $qb
            ->andWhere('a.date BETWEEN :start AND :end')
            ->setParameter('start', new \DateTime(date('Y') . '-01-01')) //Date entre le 1er Janvier de cette année
            ->setParameter('end', new \DateTime(date('Y') . '-12-31')); // et le 31 Décemebre de cette même année
        ///REMARQUE:
        /// Notons donc que cette méthode ne traite pas une Query, mais
        /// bien uniquement le QueryBuilder. c'est en cela que ce dernier est très pratique
    }
    ///Pour utiliser cette méthode "whereCurrentYear", voici la démarche
    ///
    public function myFind()
    {
        $qb = $this->createQueryBuilder('a');
        //
        //On peut ajouter ce qu'on veut avant
        $qb
            ->where('a.author=:author')
            ->setParameter('author', 'Francis N\'TATA');
        //
        //On applique notre condition sur le QueryBuilder
        $this->whereCurrentYear($qb);
        //
        //On peut ajouter ce qu'on veut après
        $qb->orderBy('a.date', 'DESC');
        //
        return $qb->getQuery()->getResult();
    }
    ///
    /// DQL: Doctrine Query Language(DQL)
    public function myFindAllDQL()
    {
        //Avec cette méthode on récupère toutes les instances contenues dans la Base de Données
        //elle équivalente à la méthode FindAll() du repository
        $query = $this->_em->createQuery('SELECT a FROM App\Entity\AdvertRappel a');
        //
        $results = $query->getResult();
        //
        return $results;
    }

    ///
    public function myFindDQL($id)
    {
        $query = $this->_em->createQuery('SELECT a FROM App\Entity\AdvertRappel a WHERE a.id=:id');
        $query->setParameter('id', $id);
        //
        //Utilisation de getSingleResult car la requête ne doit retourner qu'un seul résulat
        return $query->getSingleResult();
        //

    }
    ///LES JOINTURES: pour nous permettre de récupérer
    /// les éléments des autres entités à partir d'une autre entité
    /// cette méthode récupère(ou charge) les "Applications" Pour Toute Annonce (Advert)
    public function getAdvertWithApplications()
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.applications', 'app')
            ->addSelect('app');
        //
        return $qb->getQuery()->getResult();
        //
    }
    ///NOTA:On ne peut faire une jointure que si l'entité du FROM possède un attribut vers l'entité à joindre!
    /// cela veut dire que soit l'entité du FROM est l'entité propriétaire de la relation
    /// soit la relation est bidirectionnelle
    ///
    /// Nous ajoutons ici, une autre méthode pour récupérer toutes les annonces
    ///  qui correspondent à une liste de catégories.
    /// par exemple : On veut toutes les annonces dans les catégories
    ///  "Développement Web et Développement Mobile"
    ///
    public function getAdvertWithCategories(array $categoryNames)
    {
        //On pourra appeler cette méthode en faisant:
        //$repo->getAdvertWithCategories(array('Développement Web', 'Développement Mobile'))
        $qb = $this
            ->createQueryBuilder('ar')
            ->innerJoin('ar.categories', 'cat')
            ->addSelect('cat');
        ///On a besoin de toutes les annonces qui correspondent à une liste de catégories
        //Ainsi, On filtre sur le nom des catégories à l'aide d'un IN
        $qb->where($qb->expr()->in('c.name', $categoryNames));
        //
        //On retourne le résultat
        return $qb->getQuery()->getResult();
    }
    //
    ///LES MET

    //

    // /**
    //  * @return AdvertRappel[] Returns an array of AdvertRappel objects
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
    public function findOneBySomeField($value): ?AdvertRappel
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
