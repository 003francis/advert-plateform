<?php


namespace App\LesEvenements;

/*
* Ce service permet d'ajouter une bannière "BETA" au Site
 * plus précisement à une réponse contenant du HTML...
 *
 * **************************
 * 1 SERVICE = 1 MISSION(1 FONCTION)
 * *********************
 * Et La MISSION de ce service est d'ajouter UNE BANNIERE "BETA"
 * à une réponse contenant du HTML
 */

use Symfony\Component\HttpFoundation\Response;

class BetaHTMLAdder
{
    ///Méthode pour ajouter le « bêta » à une réponse
    public function addBeta(Response $response, $remainingDays)
    {
        //On récupère la réponse envoyée au client...
        //Cette réponse contient du HTML
        //
        $content = $response->getContent(); //
        //
        //Code à rajouter.
        //On va rajouter ce code à la Variable $content qu'on a récuperé ci-haut
        $html = '<div style="position: absolute; top: 0; background: orange; width: 100%; text-align: center; padding: 0.5em;">Beta J-' . (int)$remainingDays . ' </div>';
        //
        //Insertion du code dans la page, au début du <body>
        $content = str_replace(
            '<body>', // On recherche la balise <body>
            '<body>'.$html, // On remplace la balise <body> par la balise <body> à laquelle on a ajouté la variable $html
            $content // et ce remplacement va s'effectuer dans la variable $content
        );
        ///
        /// Après avoir ajouté notre variable $html,
        ///  on modifie le contenu dans la réponse
        $response->setContent($content);
        //
        return $response;
    }

}