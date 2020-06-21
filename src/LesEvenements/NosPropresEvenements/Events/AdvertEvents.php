<?php


namespace App\LesEvenements\NosPropresEvenements\Events;

///
/// Dans cette classe, nous définissons les noms de tous nos évènements
/// On les définit comme étant les constantes
/// et Notre classe est de portée "final"
/*
 * On Considère que cet évènement porte à la fois sur le contenu du message et sur son auteur
 */
final class AdvertEvents
{
     const POST_MESSAGE = 'advert.post_message'; //Le nom de l'évènement ici est 'advert.post_message'
    //et POST_MESSAGE va servir juste pour déclencher l'évènement
    //Nos autres évènements
    const POST_UPDATE = 'advert.update_message';

}