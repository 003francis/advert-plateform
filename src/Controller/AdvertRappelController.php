<?php

namespace App\Controller;

use App\Entity\AdvertEntity;
use App\Entity\AdvertRappel;
use App\Entity\AdvertSkill;
use App\Entity\Application;
use App\Entity\Image;
use App\Entity\Skill;
use App\Form\AdvertRappelEditType;
use App\Form\AdvertRappelType;
use App\LesEvenements\NosPropresEvenements\Events\AdvertEvents;
use App\LesEvenements\NosPropresEvenements\Events\MessagePostEvent;
use App\Repository\AdvertRappelRepository;
use App\Services\LesEvenements\BetaListener;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

//
//On met le namespace de notre Service "MessageGenerator"
use App\Services\Messages\MessageGenerator;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validation;

//use Symfony\Component\Security\Core\User\UserInterface;

//

//
/**
 * @Route("/rappelsf4calc")
 */
//

class AdvertRappelController extends AbstractController
{
    //
    private $happymessage;
    //
    //Constructeur de la classe
    /*
    public function __construct(MessageGenerator $happymessage)
    {
        $this->happymessage=$happymessage;
    }
    //
    */

    /**
     * @Route("/advert/{page}", name="oc_advert_index",
     *     requirements={"page"="\d+"},
     *     defaults={"page"=1})
     */
    public function index($page)
    {
        //
        //Une page doit être supérieure ou égale à 1
        if ($page < 1) {
            //On déclenche une exception NotFoundHttpException
            throw $this->createNotFoundException('Page "' . $page . '"inexistante.');
        }
        ///
        //On fixe le nombre d'annonces par page à 3
        $nbParPage = 3;
        //
        ///
        //On récupère la liste d'annonces depuis la Base de Données
        $listAdverts = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(AdvertRappel::class)
            ->getAdverts($page, $nbParPage);
        //
        //On récupère notre Objet Paginator
        /// L'objectif ici est de récupérer les annonces en parties, et
        /// les afficher par page
        // $listAdverts = $repository->getAdverts($page, $nbParPage);
        ///
        /// On calcule le nombre total de pages grâce au count($listAdverts)
        /// qui retourne le nombre total d'annonces
        ///
        $nbPages = ceil(count($listAdverts) / $nbParPage);
        //
        //Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
            throw $this->createNotFoundException("La Page '" . $page . "' n'existe pas.");
        }
        ///
        ///On donne toutes les infos nécessaires à la vue
        return $this->render('advert_rappel_tp/index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages' => $nbPages,
            'page' => $page,
        ));
        ///

        ///Apprentissage Normal
        ///
        //LES SERVICES
        //On récupère le Service déclaré dans services.yaml
        //
        /*
                $antispam= $this->container->get('antispam');
                //Considérons que $text contient le texte d'un message
                $text='Je serai un des développeurs SF les plus recherchés en RDC...';
                if ($antispam->isSpam($text)){
                    throw new \Exception('Votre Message a été détecté comme Spam!');

                }
                return new Response('Vous êtes dans le Bon');
        */
        //
        //$container: est l'attribut dans lequel le container(conteneur) est disponible
        //Ainsi, pour avoir accès au Conteneur:
        //  $mailer1=$this->container->get('mailer');

        //Cette façon ci-dessus est pareille à celle ci-dessous:
        // $mailer2=$this->get('mailer');


        //Génération des Url par Le Routeur à partir du nom d'une route
        //
        $url = $this->generateUrl(
            'oc_advert_view', //1er argument: le nom de la route
            ['id' => 5]               //2e argument: les paramètres
        );
        //$url vaut /advert/view/5
        //  return new Response("L'URL de l'annonce d'id 5 est:" .$url);

        /* //Message Par défaut lors de la création avec
       /// La commande par php bin/console make:controller
*/
        return $this->render('advert_rappel/index.html.twig',
            ['name' => 'Francis N\'TATA', 'url' => $url,]);

        /*
        $content= "Notre propre Hello World!";
        return new Response($content);
        */
    }
    // On crée une deuxième action de test

    /**
     * @Route("/byebye-world", name="advert_rappel2")
     */
    public function index2()
    {
        $contenu = "Au revoir à Tous...";
        return new Response($contenu);

    }
    //@Route("/advert/viewAction/{id}", name="oc_advert_viewAction")
    //La route ci-dessus marche sans configuration au préalable
    /**
     * @Route("/advert/viewAction/{id}", name="oc_advert_viewAction")
     */
    /* DEFINITION EXPLICITE DE ParamConverter
     ***************************************
     * @Route("/advert/viewAction/{advert_id}", name="oc_advert_viewAction")
     *
     * On dit à DoctrineParamConverter comment il doit utiliser le paramètre advert_id
     *
     * @ParamConverter("advert", options={"mapping": {"advert_id":"id"}})
     * Ici, on fait correspondre advert_id à id(qui est bel et bien un champ de l'entité advert)
     */
    //On Injecte(Type-hint) l'Objet AdevrtRappel $advert pour profiter du DoctrineParameterConverter
    public function viewAction(AdvertRappel $advert)
        /*
         * Avec cette façon de définir les arguments de la méthode viewAction(), alors
         * $advert est une instance de l'entité Advert, portant l'id $id (qui est spécifié dans la route)
         * D'où, on enlève l'id dans la définition de la méthode
         * Et en faisant ainsi, ça ne sert plus à rien de récupérer $advert via Entity Manager
         */

    {
        $em = $this->getDoctrine()->getManager();
        /*
        //
        //L'usage Du Message Flash qui vient de la Méthode add()
        $em = $this->getDoctrine()->getManager();
        ///On récupère l'annonce d'id $id
        /// On va éventuellement récupérer l'annonce ("AdvertRappel") avec ses "Application"
        $advert = $em->getRepository(AdvertRappel::class)->find($id);
        // $advert = new AdvertRappel();
        // Quand l'Id recherché n'est pas trouvé
        if (null == $advert) {
            //
            throw new NotFoundHttpException("L'Annonce d'id " . $id . " n'existe pas!");

        }
*/
        //
        //On récupère la liste de candidatures(Application) de cette Annonce
        $listApplications = $em
            ->getRepository(Application::class)
            ->findBy(array('advert' => $advert));
        //
        ///On va récuperer les Compétences et leur niveau à partir
        /// d'une Annonce
        //On récupère la liste des AdvertSkill en les filtrant selon l'annonce d'id $id
        $listAdvertSkills = $em
            ->getRepository(AdvertSkill::class)
            ->findBy(array('advert' => $advert));

        return $this->render('advert_rappel_tp/view.html.twig', array(
            'advert' => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills
        ));
        //
        ///Apprentissage Normal
        //MANIPULER LA SESSION
        //On récupère le contenu de la variable userId
        $userId = $session->get('userId');
        //
        //On définit une nouvelle valeur pour cette variable userId
        // $session->set('userdId', 91);
        //On renvoie une réponse
        return new Response("<body>I'm the Text Page!!!</body>");
        //
    }
    /**
     * @Route("/advert/addAction", name="oc_advert_addAction")
     *
     * On peut définir un contrôle d'accès lors de cette action
     * en passant aussi par les Annotations
     * (au lieu du service authorization_checker implémenté par l'interface AuthorizationCheckerInterface:
     *
     * @Security("has_role('ROLE_AUTEUR')", message="ACCES REFUSE!!! Seul un 'AUTEUR' peut ajouter une ANNONCE...")
     *
     * on peut ajouter plusieurs variables et fonctions dans l'argument de l'annotation ci-dessus
     */
    ///NOTA: l'annotation @Security() est dépréciée et n'est pas dispo dans symfony 5
    /// et l'annotation @IsGranted a les mêmes fonctionnalités. Pour l'utiliser @IsGranted("ROLE_AUTEUR")
    ///
    //@Security("has_role('ROLE_AUTEUR') and has_role('ROLE_AUTRE')", message="ACCES REFUSE!!! Seul un 'AUTEUR' peut ajouter une ANNONCE...")

    ///NOTA: Pour vérifier simplement que l'utilisateur est authentifié, et donc qu'il n'est pas anonyme,
    /// on peut utiliser le rôle spécial IS_AUTHENTICATED_REMEMBERED
    //
    public function addAction(Request $request, AuthorizationCheckerInterface $authorizationChecker, EventDispatcherInterface $dispatcher)
    {
        /* Si l'on n'a pas défini l'annotation '@Security("has_role('ROLE_AUTEUR')")'
         * Pour un Evéntuel CONTROLE D'ACCES, Alors On peut utiliser le 'if' juste en dessous
        ////
        /// LE CONTROLE D'ACCES
        ///On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        /// Qui lui permet d'enregistrer une Annonce
        if (!$authorizationChecker->isGranted('ROLE_AUTEUR')){
            //ici, l'utilisateur n'a pas ce rôle. D'où il ne pourra pas enregistrer UNE ANNONCE
            //On lui envoie un message d'ACCES REFUSE
            throw new AccessDeniedException('ACCES REFUSE!!! Seul un "AUTEUR" peut Ajouter une ANNONCE...');
        }
        */
        //  $security->isGranted('ROLE_AUTEUR');
        ///
        /// Ici, l'utilisateur a les droits suffisants
        /// et On peut Ajouter une Annonce comme, on sait le faie
        ///
        $em = $this->getDoctrine()->getManager();
        $advert = new AdvertRappel(); // ce n'est plus de créer un Objet AdvertRappel à ce niveau si nous l'avons déclarer dans l'argument de la méthode
        ///
        /// On préemplit notre objet avec la dernière annonce enregistrée
        /*
        $advert=$em
            ->getRepository(AdvertRappel::class)
            ->findOneBy(
                array(), //Pas de Critères
                array('id'=>'DESC') //On récupère la dernière Annonce
            );
        */
        ///Par défaut, l'AUTEUR de l'annonce sera Le Nom de l'Utisateur Connecté
        $advert->setAuthor($this->getUser()->getUsername());
        ///
        /// On ajoute L'Utilisateur connecté comme valeur du Champ "User" de l'objet $advert
        $advert->setUser($this->getUser()->getUsername());
        ///
        ///Et Notre Formulaire, va préemplir ses Champs avec les Données contenues dans l'Objet $advert
        ///On crée le Formulaire
        ///
        $formAdvert = $this->createForm(AdvertRappelType::class, $advert);
        //
        //Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            //Ici, On s'occupe de la gestion du Formulaire
            ///
            /// C'est ici qu'on hydrate l'objet $advert
            $formAdvert->handleRequest($request);
            ///
            if ($formAdvert->isValid()) {
                ///
                /*
                 * **********************************
                 * DECLENCHEMENT DE NOTRE EVENEMENT défini dans la Classe "MessagePostEvent"
                 * ------------------------------------
                 * Déclencheret utiliser un évènement se fait assez naturellement
                 * lorsqu'on a bien défini l'évènement et ses attributs.
                 * En utilisant la déclaration de notre Evènement,
                 * voici comment on pourrait déclencher l'évènement avant "l'enregistrement effectif" de l'annonce
                 */
                ///On crée l'évènement avec ses 2 arguments
                $event = new MessagePostEvent($advert->getContent(), $this->getUser()->getUsername());
                ///
                /// On Déclenche l'évènement (à partir du service EventDispatcher injecté dans l'argument de la méthode "addAction"
                //$this->get('event_dispatcher')->dispatch($event, AdvertEvents::POST_MESSAGE);
                //Ou mieux
                $dispatcher->dispatch($event, AdvertEvents::POST_MESSAGE);
                ///
                ///On récupère ce qui a été modifié par le ou les listeners, ici le message
                $advert->setContent($event->getMessage());
                ///


                /// La ligne ci-dessous, nous permet de déplacer l'image téléchargée
                /// là oùon veut la stocker
                //  $advert->getImage()->upload();
                ///la méthode "upload()" est définie dans l'entité "Image"
                /// Nous avons mis ce bout de code(sur la manipulation de l'image) ci-dessus en commentaire car ce n'est pas important de traiter l'image à ce niveau
                ///
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();
                //
                $this->addFlash('notice', 'Annonce bien enregistrée.');
                //
                //Puis on redirige vers la page de visualisation de cette annonce
                return $this->redirectToRoute('oc_advert_viewAction', array('id' => $advert->getId()));
            }
        }
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('advert_rappel_tp/add.html.twig', array(
            'formAdvert' => $formAdvert->createView()
        ));
    }
    ///

    /**
     * @Route("/editAction/{id}", name="oc_advert_editAction", requirements={"id"="\d+"})
     * @IsGranted("EDIT", subject="advert", message="ACCES REFUSE!!!")
     */
    public function editAction($id, Request $request, AdvertRappel $advert)
    {
        $em = $this->getDoctrine()->getManager();
        //
        //On récupère l'annonce $id! Nul Besoin de Déclarer $advert en tant qu'objet AdvertRappel
        $advert = $em->getRepository(AdvertRappel::class)->find($id);
        //
        //
        if (null == $advert) {
            throw new NotFoundHttpException("L'annonce d'id" . $id . "n'existe pas!");
        }
        ///Si on n'a pas défini l'annotation @IsGranted("EDIT", subject="advert") ci-dessus,
        /// alors, on peut ajouter le contrôle d'accès de cette manière:
        /// //Nous avons défini nous le rôle EDIT sur l'action "editAction" par ce qu'on appelle LE VOTEUR...
        /// A retrouver dans App\Security\Voter
        if (!$this->isGranted('EDIT', $advert)) {
            throw $this->createAccessDeniedException('ACCES REFUSE:::');
        }
        ///Sinon, On est dans le BOn pour effectuer La Modification
        ///
        //Le Formulaire sera Préempli avec les Données récupérées dans la Base de données selon l'id $id
        //Et on se base sur le Formulaire d'Edition et non Celui d'Ajout
        $formAdvert = $this->createForm(AdvertRappelEditType::class, $advert);
        //
        //Cette instruction met à jour les infos à l'aide des infos reçues de l'utilisateur
        $formAdvert->handleRequest($request); //On fait la correspondance entre les données du formulaire et notre Objet $advert
        ///Dans la méthode "handleRequest", le formulaire va faire lui-même
        /// appel à la classe "Validation" pour Vérifier NOS CONSTRAINTES et REGLES
        //Même mécanisme que pour l'ajout
        if ($formAdvert->isSubmitted() && $formAdvert->isValid()) {
            ///Si le formulaire est envoyé(ou a été soumis) et qu'il est Valide
            /// En réalité la méthode "isValid()" vient compter le nombre d'erreurs dans le formulaire envoyé
            /// Si nombre d'erreurs vaut 0 alors LE FORMULAIRE Est VALIDE, la méthode retourne "true"
            /// Si nombre d'erreurs est supérieur à 0 alors le FORMULAIRE n'est pas Valide, et la méthode retourne "false"

            ///
            // if ($formAdvert->isValid()) {
            //
            //On n'a pas besoin de faire un $em->persist($advert) car on a récupéré l'objet depus Doctrine
            $em->flush();
            //
            $this->addFlash('notice', 'Annonce bien modifiée...');
            //
            //Puis on redirige vers la page de visualisation de cette annonce
            return $this->redirectToRoute('oc_advert_viewAction', array('id' => $advert->getId()));
            //   }
        }
        // Si on n'est pas en POST, alors on affiche le formulaire d'édition
        return $this->render('advert_rappel_tp/edit.html.twig', array(
            'formAdvert' => $formAdvert->createView(),
            'advert' => $advert
        ));
    }
    //
    ///

    /**
     * @Route("/deleteAction/{id}", name="oc_advert_deleteAction",
     *     requirements={"id"="\d+"})
     * Ne peut supprimer une Annonce que son AUteur ou L'Admin
     * @IsGranted("EDIT", subject="advert", message="ACCES REFUSE!!!")
     */
    public function delete($id, Request $request, AdvertRappel $advert)
    {
        //
        $em = $this->getDoctrine()->getManager();
        //
        //On récupère l'id de l'annonce
        $idSupprime = $id;

        //Ici, on récupère l'annonce correspondant à $id
        $advert = $em->getRepository(AdvertRappel::class)->find($id);
        ///
        /// On récupère les Annonces Liées à l'annonce à Supprimer
        /// Cette Technique n'est pas cohérente (Elle est Donc à revoir)
        $listApplications = $em
            ->getRepository(Application::class)
            ->findBy(array('advert' => $advert));
        ///
        /// On recupère les Compétences Liées à l'annonce pour les supprimer
        $listAdvertSkills = $em
            ->getRepository(AdvertSkill::class)
            ->findBy(array('advert' => $advert));
        ///

        if (null == $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas!");
        }
        //
        // $advert= new AdvertRappel();
        //On boucle sur les catégories de l'annonce pour les supprimer
        //On récupère ainsi, toutes les catégories liées à
        //l'annonce $advert par le getter
        /*
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        */
        ///On supprime d'abord toutes les applications liées à l'annonce
        /// On boucle sur les Candidatures de l'annnonce pour les Supprimer


        ///
        //
        // Pour persister le changement dans la relation,
        // il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire,
        // donc inutile de la persister car on l'a récupérée
        // depuis Doctrine
        ///
        /// LA SUPPRESSION FINALE D'Un OBJET $sadvert
        ///Pour mieux supprimer l'Objet $advert,
        /// On doit créer un Formulaire vide, qui ne contiendra que le Champ CSRF(Cross Site Request Forgeries)
        /// cela permet de protéger la suppression d'annonce contre cette FAILLE CSRF
        ///Pour créer un formulaire VIDE, on doit PASSER PAR LE SERVICE Proprement dit
        $formAdvert = $this->get('form.factory')->create(); //Avec méthode, on peut créer un formulaire vide
        //$form=$this->createForm(); //Pas moyen de créer un formulaire vide ici...
        ///
        if ($request->isMethod('POST') && $formAdvert->handleRequest($request)->isValid()) {
            //
            //On supprime simplement en Symfony
            ///On supprime d'abord les Applications Associées
            foreach ($listApplications as $application) {
                $em->remove($application);
                ///
            }
            ///On Supprime les AdvertSkills associées (Méthode Non-Cohérente)
            foreach ($listAdvertSkills as $advertSkill) {
                $em->remove($advertSkill);
                ///
            }
            $em->remove($advert);
            // On déclenche la suppression
            $em->flush();
            $this->addFlash('noticeSuppression', "L'annonce " . $idSupprime . " a bien été supprimée...");
            //
            //On redirige vers l'accueil
            return $this->redirectToRoute('oc_advert_index', array(
                'idSupprime' => $idSupprime
            ));
        }
        //
        return $this->render('advert_rappel_tp/delete.html.twig', array(
            'advert' => $advert,
            'formAdvert' => $formAdvert->createView()
        ));
    }
    ///
    ///
    ///
    /**
     * @Route("/menu", name="oc_advert_menu")
     */
    //Ce Controller est inclus dans un Template Père:
    /*
     *Pour Inclure un controller dans un template, il faut créer une action comme menuAction()
     * Qui enverra les variables au Template (autre que celui qui l'inclut le controller) chargé de les traiter
     */
    public function menu()
    {
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
    ///
    ///
    ///
    /// LES METHODES en dessous, sont celles issues de l'Apprentissage
    /// Celles qui sont au-dessus sont issues du TP


//
///
///
///
    /**
     * @Route("/advert/view/{id}", name="oc_advert_view",
     *     requirements={"id"="\d+"})
     */
    public function view($id, Request $request)
    {


        // REPONSE et REDIRECTION
        //On fait une rédirection à la route 'oc_advert_index'
        // de sorte qu'à partir de la page View, on soit rédirigé vers la page d'accueil..
        return $this->redirectToRoute('oc_advert_viewAction');


        //On a accès à la requête HTTP via $request qu'on a injecté dans la méthode
        //On crée la réponse sans lui donner de contenu pour le moment

        //A. La Méthode La Plus Longue
        $response = new Response();
        //
        // On définit le contenu
        $response->setContent("Ceci est une page d'erreur 404");

        //On définir le code HTTP à "Not Found" (erreur 404)
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        //On retourne la réponse
        // return $response;
        //B. La Méthode La Plus Courte Pour Manipuler L'objet Réponse
        $tag = $request->query->get('tag');
        //On utilise le raccourci: il crée un objet Response
        //et lui donne comme contenu le contenu du Template
        return $this->render('advert_rappel/view.html.twig', ['id' => $id, 'tag' => $tag]);


        // $id vaut 5 si l'URL appelée est /advert/view/5
        $tag = $request->query->get('tag'); // On récupère l'élément qui vient après le "?"
        return new Response("Affichage de l'annonce d'id:" . $id . ", avec le tag:" . $tag);

    }
    //

    /**
     * @Route("/advert/view/{year}/{slug}.{format}", name="oc_advert_view_slug", requirements={
     *     "year"="\d{4}",
     *     "format"="html|xml"
     *     }, defaults={"format"="html"})
     */
    public function viewSlug($slug, $year, $format)
    {
        return new Response("On Pourrait afficher l'annonce correspondant au 
        slug '" . $slug . "', créée en " . $year . " et au format " . $format . ".");

    }
    //

    /**
     * @Route("/advert/add", name="oc_advert_add")
     */
    public function add(Request $request)
    {
        //
        //DOCTRINE et BD
        //On récupère l'EntityManager
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        //

        // I. Relation OneToOne entre "AdvertRappel" et "Image"
        //Création(instanciation) de l'entité
        $advert = new AdvertRappel();
        $advert->setTitle('Equity BANK CONGO est à la Recherche des mécaniciens');
        $advert->setAuthor('RECRUITEMENT EQUITY BANK');
        $advert->setContent("Pour Assurer la pérénisation de ses travaux avec ses partenaires, 
                           nous recrutons des mécaniciens avec ou sans expérience...");
        //On ne peut pas définir ni la date ni la publication,
        //car ces attributs sont définis automatiquement dans le constructeur
        //
        ///On enregistre une image qui est liée à AdvertRappel par
        /// La relation OneToOne
        //Création de l'entité Image
        $image = new Image();
        $image->setUrl('assets/img/people/pic19.jpg');
        $image->setAlt('Job Mecanicien');
        //
        //On lie l'image à l'annonce
        $advert->setImage($image);
        //
        //II.RELATION ManyToOne entre "Application" et "AdvertRappel"
        //a)Création d'une première Candidature(Application)
        $application1 = new Application();
        $application1->setAuthor('Victor ESAFE');
        $application1->setContent("La Technique me connait par le nom....");
        //
        //b) Création d'une deuxième Candidature(Application) par exemple
        $application2 = new Application();
        $application2->setAuthor('Guylain OFAFU');
        $application2->setContent("La mécanique m'aime plus qu'elle ne s'aime...");
        //
        //On lie les candidatures(Application) à l'annonce (AdvertRappel)
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);
        //
        //III. RELATION ManyToMany avec Attributs
        ///AdvertRappel, Skill et AdvertSkill(propriétaire)
        /// Pour lier une ANNONCE(Advert) à une COMPETENCE (Skill):
        /// a)Il faut d'abord créer(Instancier) cette entité de liaison (AdvertSkill dans notre cas)
        /// b) On la lie à l'annonce Advert, puis à la compétence Skill
        /// c) On définit tous ses attributs
        /// d) On Persiste le tout.
        ///
        ///
        ///Comme on a déjà créé un Advert ci-dessus, on Va à présent
        /// Récupérer toutes les compétences possibles (de la BD)
        $listSkills = $em->getRepository(Skill::class)->findAll();
        ///
        /// Pour Chaque Compétence (Skill):

        foreach ($listSkills as $skill) {
            ///On crée une nouvelle "relation" entre 1 Advert et 1 Skill
            ///Et cette relation c'est l'entité AdvertSkill
            $advertSkill = new AdvertSkill();
            //
            //On la lie à l'annonce (Advert), qui est ici toujours la même
            $advertSkill->setAdvert($advert);
            //
            //On la lie à la compétence(Skill),
            // qui change ici dans la Boucle foreach
            $advertSkill->setSkill($skill);
            //
            //Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Toute Tendance Confondue');
            //
            //Et bien sûr, on persiste cette entité de relation, propriétaire de deux autres
            $em->persist($advertSkill);
        }
        //On doit persister aussi l'objet Advert étant donné
        //que la relation AdvertSkill n'a pas été défini avec un "cascade persist"
        //La persistance de 'Advert' a été fait ci-dessous


        //Ou en une ligne
        //$em=$this->getDoctrine()->getManager();
        //Pour enregistrer une ligne dans une BD, on passe par deux étapes

        //1.Etape 1: On "Persiste" l'entité via l'EntityManager

        ///On persiste l'entité "AdvertRappel" qui est en Relation
        ///  avec l'entité "Image" : Où il y a Cascade lors de l'enregistrement
        $em->persist($advert);
        ///NOTA: Pour la relation entre "Application" et "AdvertRappel", il n'y a pas de cascade
        /// lorsqu'on persiste "AdvertRappel" car la relation est définie dans l'entité
        /// "Application" et non "AdvertRappel"

        ///On Persiste les 2 Objets "Application" créés ci-dessus
        $em->persist($application1);
        $em->persist($application2);

        //Brèche: On récupère  l'annonce d'id3
        //$advert2 = $em->find(AdvertRappel::class, 3);
        //$advert2= $em->getRepository(AdvertRappel::class)->find(15);

        //On modifie cette annonce, en changeant la date à la date actuelle
        //$advert2->setDate(new \DateTime());
        //A ce niveau, inutile de faire un persist() sur $advert2. En effet, comme on a
        ///récupéré cette annonce via Doctrine, il sait qu'il doit gérer cette entité
        /// Il sied de rappeler qu'un persist() ne sert qu'à donner la responsabilté de l'objet à Doctrine

        //2.Etape 2: On "flush" tout ce qui a été persisté avant (à l'étape1)
        $em->flush();
        //
        //
        //
        //Le Message Flash
        //On suppose qu'une annoncea été enregistrée et qu'on envoie un message Flash
        $this->addFlash('info', 'Annonce bien enregistrée');
        //Le "flashBag" est ce qui contient les messages flash dans la session
        //Il peut bien sûr contenir plusieurs messages:
        //Dans notre cas, le FlashBag c'est "info"
        // $this->addFlash('info', 'Annonce, bien Modifiée!!');
        //Puis on redirige vers la page de visualisation de cette annonce (ici, viewAction)
        return $this->redirectToRoute('oc_advert_viewAction', array('id' => $advert->getId()));
        return $this->redirectToRoute('oc_advert_viewAction', ['id' => 5]);
    }
    //
    ///

    //Utilisation Des Services
    //1) Le Service MessageGenerator
    /**
     * @Route("/service", name="oc_advert_service_rappel")
     */
    /*
    public function  new(MessageGenerator $messageGenerator)
    {
        $happymesage=$messageGenerator->getHappyMessage();
        $this->addFlash('success', $happymesage);
        return new Response($happymesage);
    }
    */
    //
    ///Cette Section, nous retrouvons différents exercices sur
    //LES REPOSITORIES
    /**
     * @Route("/advert/repository/{id}", name="advert_repository",
     *      requirements={"id"="\d+"})
     */

    //Cette méthode va nous permettre d'appréhender la notion de Repository
    public function learningRepository($id)
    {
        //Déclaration du repository
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(AdvertRappel::class);
        //
        //A...LES 4 METHODES NORMALES
        ///Faire un ctrl+clic sur la méthode pour voir sa Syntaxe

        //1) LA METHODE find($id)
        $advert1 = $repository->find($id);
        //
        if (null == $advert1) {
            throw  new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas");

        }
        //
        //2) LA METHODE findAll(): trouve toutes les entités contenues dans la base de données
        $listAdvert2 = $repository->findAll();
        //Cette méthode retourne un tableau PHP normal(un array) qu'on peut parcourir avec un foreach par exemple
        foreach ($listAdvert2 as $advert) {
            //$advert est une instance de advert
            // echo $advert->getContent();
        }
        //
        ///3) LA METHODE findBy(): elle retourne une liste d'entités comme findAll()
        ///  tout en effectuant un filtre avec un ou plusieurs critères
        /// Elle peut aussi trier les entités, et même n'en récupérer qu'un certain nombre(pour une pagination)
        $listAdvert3 = $repository->findBy(
            array('author' => "Francis N'TATA"), //Critère
            array('date' => 'desc'),  //Tri (ordered By)
            2, //Limite : On va sélectionner Deux entités seulement
            0  //à partir de 0 (le début)
        );
        ///NOTA: On peut mettre plusieurs entrées dans le tableau des critères,
        /// afin d'appliquer plusieurs filtres(qui seront associés avec un AND et non un OR)
        /// Retourne un tableau array()
        ///
        /// 4) LA METHODE findOneBy(): Elle fonctionne sur le même principe que la méthode findBy()
        /// sauf qu'elle ne retourne qu'une seule entité. Et
        /// les arguments "limit" et "offset" n'existent donc pas
        $advert4 = $repository->findOneBy(
            array('author' => 'Francis N\'TATA'),
            array('date' => 'desc')
        );
        ///NOTA: retourne "null" si aucune entité ne correspond au critère demandé.
        /// si plusieurs entités correspondent au critère demandé,
        ///  alors c'est la première occurence dans l'ordre qu'on a demandé(orderBy)
        ///  qui sera retournée
        /// NOTE FINALE: Ces méthodes permettent de couvrir pas mal de besoins

        //B...LES 2 METHODES MAGIGUES
        //
        //1) findByX($valeur)
        ///en Remplaçant "X" par le nom d'une propriété de notre entité.
        /// Dans notre cas de l'entité AdvertRappel, on a donc plusieurs méthodes :
        ///  findByTitle(), findByDate(), findByAuthor(), findByContent(), etc.
        /// Cette méthode fonctionne comme si on utilise findBy()
        ///  avec un seul critère, celui du nom de la propriété
        $listAdvert5 = $repository->findByAuthor('Francis N\'TATA');


        ///$listAdvert5 est un Array qui contient toutes les annonces écrites par "Francis N'TATA"
        /// on a donc findByAuthor('Francis N\'TATA') qui est strictement égal à
        /// findBy(array('author'=>'Francis N\'TATA'))
        /// //
        /// 2) findOneByX($valeur): mettre logique que findByX et findOneBy()
        $advert6 = $repository->findOneByTitle('Recherche développeur Java EE.');
        //

        //C... LES METHODES DE RECUPERATION PERSONNELLES
        ///ici, il nous faut distinguer 3 types d'objets qui vont nous servir et qu'il ne faut pas confondre:
        ///Le QueryBuilder, la Query et les résulats
        /// Et ces méthodes sont appelées dans une méthode de la classe repository d'une entité...
        /// Voir AdvertRappelRepository.php pour La suite des ces méthodes
        /// NOUS AVONS DEFINI UNE METHODE myFindAll() dans 'AdvertRappelRepository.php'
        /// Elle nous renvoie les résultats que nous récupérons comme suit:
        ///
        $listAdvert7 = $repository->myFindAll();
        ///
        /// D... DQL -Doctrine Query Language
        $listAdvert8 = $repository->MyFindAllDQL();
        $advert9 = $repository->myFindDQL($id);
        ///Pour utiliser une méthode de Jointure, comme on l'a créée
        /// la classe AdvertRappelRepository:
        $listAdvert10 = $repository->getAdvertWithApplications();

        ///
        ///
        return $this->render('advert_rappel/view.html.twig', array(
            'advert1' => $advert1,
            'listAdvert2' => $listAdvert2,
            'listAdvert3' => $listAdvert3,
            'advert4' => $advert4,
            'listAdvert5' => $listAdvert5,
            'advert6' => $advert6,
            'listAdvert7' => $listAdvert7,
            'listAdvert8' => $listAdvert8,
            'advert9' => $advert9,
            'listAdvert10' => $listAdvert10
        ));
    }
    ///
    /// Dans cette section, nous avons l'apprentissage sur:
    /// LES FORMULAIRES
    /**
     * @Route("/advert/forms", name="advert_forms")
     */
    public function learningForms(Request $request)
    {
        //
        ///On a dejà défini Notre Formulaire dans la Classe: "Form/AdvertRappelType
        /// Et Ici, on va juste le créer sur un Objet AdvertRappel
        /// Nous Instancions la Classe AdvertRappel
        $advert = new AdvertRappel();
        ///
        ///On crée maintenant notre FORMULAIRE dans les lignes qui suivent
        /// à partir de l'objet "$advert" dont les champs sont définis
        ///  dans la Classe "AdvertRappelType" du dossier "Form"
        ///
        $formAdvert = $this->createForm(AdvertRappelType::class, $advert);
        ///
        ///ON vérifie si la requêteest en POST
        if ($request->isMethod('POST')) {
            //On fait le lien Requête-Formulaire
            //c'est à partir de cet instant que la variable $advert contient
            //les valeurs entrées dans le formulaire par le visiteur
            $formAdvert->handleRequest($request);
            //On vérifie que les valeurs entrées sont correctes ou valides
            if ($formAdvert->isValid()) {
                ///On enregistre notre Objet $advert dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();
                ///
                ///Message Flash
                $this->addFlash('notice', 'Annonce bien enregistrée');
                ///
                /// On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('oc_advert_viewAction', array('id' => $advert->getId()));
            }
        }
        // En cas de non validation du Formulaire
        //
        return $this->render('advert_rappel/viewFom.html.twig', array(
            'formAdvert' => $formAdvert->createView() //On donne à notre vue de nous créer le rendu visuel du Formulaire
        ));
    }
    ///
    /// LES CONTRAINTES
    ///
    /**
     * @Route("/advert/constraint", name="advert_constraint")
     */
    public function learningConstraint()
    {
        $advert = new AdvertRappel();
        //
        $advert->setDate(new \Datetime());  // Champ « date » OK
        $advert->setTitle('abc');           // Champ « title » incorrect : moins de 10 caractères
        //$advert->setContent('blabla');    // Champ « content » incorrect : on ne le définit pas
        $advert->setAuthor('A');            // Champ « author » incorrect : moins de 2 caractères
        ///
        /// On récupère le service validator
        // $validator = $this->get('validator');
        //A partir de Symfony 4
        $validator = Validation::createValidator();

        //On déclenche la validation sur notre Objet
        $listErrors = $validator->validate($advert, array(
            new Length(['min' => 10]),
            new NotBlank(),
        ));

        //Si $listErrors n'est pas vide, on affiche les erreurs
        if (count($listErrors) > 0) {
            //$listErrors est un Objet, sa méthode __toString permet de lister joliement les erreurs
            foreach ($listErrors as $error) {
                echo $error->getMessage() . '<br>';
            }
            return new Response((string)$listErrors);
        } else { //Quand $listErrors" égal null alors Il n' y a pas d'erreurs
            return new Response("L'annonce est Valide!!");
        }
        ///
    }
    ///
    ///LES EVENEMENTS SYMFONY
    public function learnigEvent()
    {
        /*
         * 1) MANIPULER DIRECTEMENT LE GESTIONNAIRE d'EVENEMENTS: EventDispatcher

        ///On instancie notre Listener
        $betaListener = new BetaListener('2020-06-02');
        ///
        /// On récupère le gestionnaire d'évènements,
        ///  qui heureusement est un service:
        ///  Nous l'avons injecter dans notre méthode
       // $dispatcher = $this->get('event_dispatcher');
        ///On dit au gestionnaire d'exécuter la méthode 'onKernelResponse' de notre listener
        /// Lorsque l'évènement 'kernel.response' est déclenché
        $dispatcher->addListener(
            'kernel.response',
            array($betaListener, 'processBeta')
        );
         */
        /*
         * A partir de maintenant, dès que l'évènement 'kernel.response' est déclenché,
         * le gestionnaire d'évènements exécutera la méthode
         * $betaListener->processBeta()
         * *****************************
         * Bien évidemment, avec cette méthode, le moment où on exécute ce code est important!
         * En effet, si on prévient le gestionnaire d'évènements après que
         * l'évt qui nous intéresse se soit produit, le Listener ne sera pas exécuté..
         * c'est pourquoi, ce n'est que rarement que l'on fera comme ça...
         *
         */
        ///
        /// 2)PASSER PAR LE SERVICE du LISTERNER que nous avons défini


    }

}
