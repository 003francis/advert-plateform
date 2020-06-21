<?php

namespace App\Controller;

use App\Entity\User;
use FOS\UserBundle\FOSUserBundle;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /*
     * @Route("/login", name="login")
     */
    public function login(AuthorizationCheckerInterface $authorizationChecker, AuthenticationUtils $authenticationUtils, TokenStorageInterface $tokenStorage)
    {
        ///Si le visiteur est déjà identifié, on le redirige vers la page d'accueil
        /*
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('oc_advert_index');
        }
        */
        ///1)Pour vérifier que l'utilisateur a une Authorisation d'accéder au site
        //En utilisant le service 'security.authorization_checker' via Sa classe injectée dans notre méthode, on a:
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // $currentUser = $this->getUser();
            return $this->redirectToRoute('oc_advert_index');

        }

        ///2)
        ///Le service 'authentication_utils' permet de récupérer le nom d'utilisateur
        /// et l'erreur dans le cas où le formulairea déjà été soumis, mais était invalide
        /// (mauvais mot de passe par exemple)
        //$authenticationUtils=$this->get('security.authentication_utils');
        //Le Dernier User Name à se connecter
        $last_username = $authenticationUtils->getLastUsername();
        //On récupère l'erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        ///

        ///3)
        /// Récupérer l'utilisateur courant. Le service 'security.token_storage'
        /// Ce service dispose d'une méthode 'getToken()'
        /// Pour récupérer la session de sécurité courante
        ///  (à ne pas confondre avec la session classique disponible via $request->getSession())
        ///NOTA: ce token vaut 'null' si on est hors d'un pare-feu
        /// et si on est derrière un pare-feu, alors on peut récupérer
        /// l'utilisateur courant grâce à '$token->getUser()

        //a)On récupère le token (On utilise l'Injection de Dépendances)
        $token = $tokenStorage->getToken();

        //Si la requête courante n'est pas derrière un pare-feu, $token est null
        //b)Sinon, on récupère l'utilisateur courant, i.e celui qui est connecté
        $currentUser = $token->getUser();
        ///

        //Si l'utilisateur courant est anonyme, $currentUser vaut 'anon.'
        //c)Si non, c'est une instance de notre entié=té User, on peut l'utiliser normalement
        // $currentUser->getUsername(); //On récupère le 'nom' de l'utilisateur courant maintenant
        //
        ///Ou TOUT SIMPLEMENT, au lieu de faire TOUTES LES VERIFICATIONS ci-dessus (de a-c),
        /// on passe par le raccourci proposé par le controller $this->getUser()
        $currentUser2 = $this->getUser();
        ///Cette méthode retourne:
        ///  1) 'null' si larquêt n'est pas derrière un pare-feu ou si l'utilisateur courant est anonyme.
        /// 2) Une Instance de 'User' le reste du temps (utilisateur authentifié derrière un pare-feu et non-anonyme)
        /*
         * if (null === $user) {
            // Ici, l'utilisateur est anonyme ou l'URL n'est pas derrière un pare-feu
             } else {
            // Ici, $user est une instance de notre classe User
            }
         *
         */
        ///NOTA: On peut récupérer l'utilisateur courant depuis Twig en faisant
        /// {{ app.user }}

        return $this->render('security/__formLogin.html.twig', array(
            'last_username' => $last_username,
            'error' => $error
        ));
        ///LA SUITE SUR LA SECURITE est à voir dans AdvertRappeController
    }
    ///
    ///
    /// MANIPULATION DES UTILISATEURS AVEC FOSUserBundle
    /// pour manipuler nos utilisateurs au quotidien
    /**
     * @Route("/manip", name="advert_manip")
     */
    ///On préfère passer par l'injection de dépendance
    public function manipulation(UserManagerInterface $userManager)
    {
        ///Pour récupérer le service UserManager du Bundle FOSUser
        /// Nous sommes passés par l'injection de dépendances
        ///  de l'Interface UserManagerInterface que implémente le service fos_user.user_manager
        ///
        /// Pour charger un utilisateur

        $user = $userManager->findUserBy(array(
            'username' => 'jean mutombo'));


        ///
        ///Pour modifier un utilisateur
        $user->setEmail('cetemail@nexiste.pas');
        $userManager->updateUser($user); //Pas besoin de faire un flush avec l'EntityManager, cette méthode le fait toute seule !
        //
        $user = $userManager->findUserByEmail('cetemail@nexiste.pas');
        if (null == $user) {
            throw new NotFoundHttpException('UTILISATEUR NON TROUVE!!!!');

        }
        ///
        ///Pour supprimer un utilisateur
        $userDeleted = $user;
        $userManager->deleteUser($user);

        //echo('UTILISATEUR SUPPRIME ' . $userDeleted);
        ///

        $user1= new User();
        //Poiur récupérer le rôle
        $user1=$this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->find(1);
        $roles= $user1->getRoles();
        foreach ($roles as $role ) {
            //dump('Les Rôles sont '.$role);
            return new Response('Les Rôles sont '.$role);
        }
        ///
        /// Pour récupérer la liste de tous les utilisateurs
        $users = $userManager->findUsers();
        ///
        /*
         * NOTA: Si on a besoin de plus de fonctions que celles proposées par
         * le Service fos_user.user_manager, alors
         * On peut parfaitement passer par notre repository de l'entité User
         * et faire les manipulations comme d'hab!
         */



    }
}
