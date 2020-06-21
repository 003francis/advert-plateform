<?php


namespace App\LesEvenements\NosPropresEvenements\Listeners;

///
/*
 * Ce Listener permet d'écouter l'evt advert.post_message
 * et commander l'exécution l'objet 'MessageNotificator' (qui consiste à envoyer un mail)
 * seulement quand l'auteur du message posté est dans une liste prédéfinie
 * que nous passons en argument du Constructeur
 */

use App\LesEvenements\NosPropresEvenements\Bigbrother\MessageNotificator;
use App\LesEvenements\NosPropresEvenements\Events\MessagePostEvent;

class MessageListener
{
    protected $notificator;
    protected $listUsers = array();

    ///
    public function __construct(MessageNotificator $notificator, $listUsers)
    {
        $this->notificator = $notificator;
        $this->listUsers = $listUsers;
    }

    ///
    public function processMessage(MessagePostEvent $event)
    {
        //On active la surveillance si l'auteur du message est dans la liste
        if (in_array($event->getUser(), $this->listUsers)) {
            //On envoie un e-mail à l'administrateur
            $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
        }
    }
}