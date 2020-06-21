<?php

namespace App\Security\Voter;

use App\Entity\AdvertRappel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

////
/* Cette classe est ce qu'on appelle un VOTEUR!
 * Un Voteur est une classe dont la fonction est de voter si une action sur un Objet est autorisée
 *
 * Le Voteur que nous avons créé ici, Va Permettre à un USER d'éditer une annonce
 * dont il est le créateur...
 * Nous avons généré cette classe par la commande "php bin/console make:voter"
*/

///
class AnnonceEditionVoter extends Voter
{
    ///
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    ///
    protected function supports($attribute, $subject)
    {
        //

        //Par défaut:
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        /*
        return in_array($attribute, ['POST_EDIT', 'POST_VIEW'])
            && $subject instanceof \App\Entity\BlogPost;
        */
        ///Notre propre code
        return $attribute === 'EDIT'
            && $subject instanceof \App\Entity\AdvertRappel;


    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //ON retrouve le user (on peut aussi ré-utiliser $this->security)
        $user = $token->getUser();
        //$user=$this->security->getUser();

        // if the user is anonymous, do not grant access
        //1) Si le user n'est pas authentifié, c'est no-access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* CODE par défaut...
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'POST_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }
        */
        // $subject=new AdvertRappel();
        //
        //2) Si le user est l'auteur de l'Article
        ///On met le user et le nom de l'auteur dans la même casse
        /// étant donné dans la table "fos_user", on utilise aussi les minuscules
        if (strtolower($user) === strtolower($subject->getAuthor())) {
            return true;
        }
        //
        //Si l'utilisateur(le user) est un Administrateur
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        //
        return false;
    }
    /*
     * Après avoir configuré ce VOTEUR, nous allons l'utiliser
     * dans l'annotation sur l'action editAction de notre contrôleur AdvertRappelController
     * et Aussi dans notre gabarit TWIG
     * D'où, pour voir la suite du Programme,
     *  se rendre dans le contrôleur et dans notre gabarit TWIG
     */
}
