<?php

namespace App\Controller;

use App\Entity\AdvertRappel;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

//

/**
 * @Route("/advert")
 */
class AdvertRappelTpController extends AbstractController
{
    /**
     * @Route("/{page}", name="oc_advert_index_rappel_tp",
     *      requirements={"page"="\d+"},
     *     defaults={"page"=1})
     */
    public function index($page)
    {
        //Une page doit être supérieure ou égale à 1
        if ($page < 1) {
            //On déclenche une exception NotFoundHttpException
            throw $this->createNotFoundException('Page "' . $page . '"inexistante.');
        }
        //Sinon, on récupère la liste d'annonces, puis on la passe au Template
        //
        // Notre liste d'annonce en dur
        /*
        $listAdverts = array(
            array(
                'title' => 'Recherche développpeur Symfony',
                'id' => 1,
                'author' => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Mission de webmaster',
                'id' => 2,
                'author' => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Offre de stage webdesigner',
                'id' => 3,
                'author' => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date' => new \Datetime())
        );
        */
        //On récupère la liste d'annonces depuis la Base de Données
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(AdvertRappel::class);
        //
        $listAdverts = $repository->findAll();
        //
        return $this->render('advert_rappel_tp/index.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }
    //

    /**
     * @Route("/view/{id}", name="oc_advert_view_rappel_tp",
     *     requirements={"id"="\d+"})
     */
    public function view($id)
    {
        //Ici, on récupère l'annonce correspondante à l'id $id
        //Récupération en Dur

        $advert = array(
            'title' => 'Recherche développpeur Symfony2',
            'id' => $id,
            'author' => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
            'date' => new \Datetime()
        );
        //

        return $this->render('advert_rappel_tp/view.html.twig', array(
            'advert' => $advert));
    }
    //

    /**
     * @Route("/add", name="oc_advert_add_rappel_tp")
     */
    public function add(Request $request)
    {
        //

        //Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            //Ici, On s'occupe de la gestion du Formulaire
            //
            $this->addFlash('notice', 'Annonce bien enregistrée.');
            //
            //Puis on redirige vers la page de visualisation de cette annonce
            return $this->redirectToRoute('oc_advert_view_rappel_tp', ['id' => 5]);
        }
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('advert_rappel_tp/add.html.twig');

    }
    //

    /**
     * @Route("/edit/{id}", name="oc_advert_edit_rappel_tp",
     *     requirements={"id"="\d+"})
     */
    public function edit($id, Request $request)
    {
        //Ici, on Récupère l'annonce correspondante à $id
        /*
        $advert = array(
            'title' => 'Recherche développpeur Symfony',
            'id' => $id,
            'author' => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
            'date' => new \Datetime()
        );
        return $this->render('advert_rappel_tp/edit.html.twig', array(
            'advert'=>$advert
        ));
        */
        //On va Ajouter une Annonce(AdvertRappel) existante
        // à plusieurs catégories Existantes
        //On récupère d'abord l'Entity Manager
        $em = $this->getDoctrine()->getManager();
        //
        //On récupère l'annonce $id! Nul Besoin de Déclarer $advert en tant qu'objet AdvertRappel
        $advert = $em->getRepository(AdvertRappel::class)->find($id);
        //
        if (null == $advert) {
            throw new NotFoundHttpException("L'annonce d'id" . $id . "n'existe pas!");
        }
        //
        //La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository(Category::class)->findAll();
        //
        //On boucle sur les catégories pour les lier à l'annonce
        //On va Ajouter Chaque Catégorie UNE à UNE à l'annonce récupéré ci-haut
        foreach ($listCategories as $category) {
            $advert->addCategory($category); //Le setter addCategory()
        }
        //
        //Pour persister le changement dans la relation,
        // il faut persister l'entité propriétaire
        //Ici, Advert est le propriétaire,
        // donc inutilise de le persister car on l'a récupéré depuis Doctrine
        //
        //On déclenche l'enregistrement
        $em->flush();

        //

        //Même mécanisme que pour l'ajout
        if ($request->isMethod('POST')) {
            //Ici, On s'occupe de la gestion du Formulaire
            //
            $this->addFlash('notice', 'Annonce bien modifiée.');
            //
            //Puis on redirige vers la page de visualisation de cette annonce
            return $this->redirectToRoute('oc_advert_viewAction', array('id' => $advert->getId()));
        }
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->redirectToRoute('oc_advert_viewAction', array(
            'id' => $advert->getId()
        ));


    }
    //

    /**
     * @Route("/delete/{id}", name="oc_advert_delete_rappel_tp",
     *     requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        //
        $em = $this->getDoctrine()->getManager();

        //Ici, on récupère l'annonce correspondant à $id
        $advert = $em->getRepository(AdvertRappel::class)->find($id);
        //
        if (null == $advert) {
            throw new NotFoundHttpException("L'annonce d'id" . $id . "n'existe pas!");
        }
        //
        //$advert= new AdvertRappel();
        //On boucle sur les catégories de l'annonce pour les supprimer
        //On récupère ainsi, toutes les catégories liées à
        //l'annonce $advert par le getter
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        //
        // Pour persister le changement dans la relation,
        // il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire,
        // donc inutile de la persister car on l'a récupérée
        // depuis Doctrine

        // On déclenche la modification
        $em->flush();
        //


        //ici, on gérera la suppression de l'annonce en question
        return $this->redirectToRoute('oc_advert_viewAction', array(
            'id' => $advert->getId()
        ));
        return $this->render('advert_rappel_tp/delete.html.twig');

    }
    //
    /*
     * @Route("/menuAction", name="oc_advert_menu_rappel_tp")

    //Ce Controller est inclus dans un Template Père:
    /*
     *Pour Inclure un controller dans un template, il faut créer une action comme menuAction()
     * Qui enverra les variables au Template (autre que celui qui l'inclut le controller) chargé de les traiter

    public function menu($limit)
    {
        //On fixe une liste qui sera affichée peu importe les pages demandées
        //
        //On fixe une liste en Dur
        /*
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche Développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de WebMaster'),
            array('id' => 9, 'title' => 'Offre de Stage WebDesigner')
        );
        *
        //On récupère les 3 dernières annonces enregistrées en Base de Données
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(AdvertRappel::class);
        //
        $listAdverts = $repository->dernieresAnnonces();

        return $this->render('advert_rappel_tp/menu.html.twig',
            array(
                'listAdverts' => $listAdverts
            ));
    }
*/
}
