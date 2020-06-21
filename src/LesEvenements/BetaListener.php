<?php


namespace App\LesEvenements;

/*
 * Ce Service permet d'écouter l'évenement qui permet d'ajouter
 * Une Bannière "Beta" à notre Application web
 * L'Ajout de cette Bannière "Beta" sera fait par le service 'BetaHTMLAdder'
 ******************************************
 * Le "LISTENER"  est:
 * Un objet capable de décider s'il faut ou non appeler un autre Objet
 * qui remplira une certaine fonction
 * La FONCTION du LISTENER n'est que de DECIDER quand appeler l'autre Objet
 *
 * Dans notre cas, la décision ou non d'appeler le "BetaHTMLAdder"
 * repose sur la date courante.
 * Si elle est antérieure (i.e inférieure) à la date définie dans le constructeur
 * alors, on exécute "BetaHTMLAdder"; sinon, on ne fait rien.
 */

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Contracts\EventDispatcher\Event;

class BetaListener
{
    //Notre processeur
    protected $betaHTML;

    ///La date de fin de la version bêta:
    /// - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
    /// - Après cette date, on n'affichera plus le "bêta"
    protected $endDate;
    //
    ///On fait de l'injection de Dépendances
    /// On Injecte l'objet "BetaHTMLAdder"
    public function __construct(BetaHTMLAdder $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate = new \DateTime($endDate);
    }
    ///
    /// L'argument de la méthode est un FilterResponseEvent (ResponseEvent)
    public function processBeta(ResponseEvent $event)
    {
        /* ******************************
         * ECOUTER UN EVENEMENT
         * *****************************
         * Pour que notre classe, écoute qlq chose, il faut la présenter au gestionnaire d'évènement.
         * Il existe 2 manières de les faire:
         * 1) Manipuler directement le gestionnaire d'evts (EventDispatcher)
         * 2) Passer par les services
         * **************************************************************
         * Vu que nous avons besoin de modifier la réponse retournée par les contrôleurs,
         * nous allons écouter l'évènement "kernel.response"
         */
        ///1) MANIPULER DIRECTEMENT le GESTIONNAIRE D'EVENEMENTS
        ///ON se rend dans un CONTROLLEUR, prenons ici, "AdvertRappelController"
        ///2) MANIPULER LE SERVICE BetaListener que nous avons défini
        /*
         * Notre méthode processBeta()
         */
        //On teste si la requête est bien la requête principale(et non une sous-requête)
        if (!$event->isMasterRequest()) {// Si ce n'est pas la requête principale, On ne fait rien
            return;
        }
        /*
         * Le premier if teste si la requête courante est bien la requête principale.
         * En effet, souvenez-vous, on peut effectuer des sous-requêtes
         * via la fonction{{ render }}de Twig ou alors la méthode $this->forward() d'un contrôleur.
         * Cette condition permet de ne pas réexécuter le code lors d'une sous-requête
         * (on ne va pas mettre des mentions « bêta » sur chaque sous-requête !).
         * Bien entendu, si vous souhaitez que votre comportement s'applique même aux sous-requêtes, ne mettez pas cette condition.
         */
        ///
        /*
        /// Dans le cas d'une requête Principale
        /// On récupère la réponse que le gestionnaire a insérée dans l'évènement
        $response = $event->getResponse();
        //
        //Ici, on peut modifier comme on veut la Réponse
        //Puis on Insère la Réponse Modifiée dans l'Evènement
        $event->setResponse($response);
        ///
       */
        ///
        ///Pour avoir par exemple 11(jours) en prenant la différence(diff)
        ///  de la date D'ajourd'hui avec la Date Définie dans le Constructeur
        /// ici, stockée dans la variable $endDate
        $remainingDays = $this->endDate->diff(new \DateTime())->days;
        ///On traduit cette différence en Jours. D'où la propriété "days" à la fin de l'instruction ci-dessus
        //
        if ($remainingDays <= 0) {
            //Si la date est dépassée, on ne fait rien
            return;
        }
        //
        //Sinon, On utilise notre BetaHTMl
        $response = $this->betaHTML->addBeta(
            $event->getResponse(), $remainingDays);
        ///
        ///On met à jour la réponse avec la nouvelle valeur...
        /// On Ajoute cela dans l'Evenement kernel.response
        $event->setResponse($response);
        ///
        /// On Stoppe la propagation de l'évènement en cours(ici, kernel.response)
       // $event->stopPropagation(); //A revoir
    }
}