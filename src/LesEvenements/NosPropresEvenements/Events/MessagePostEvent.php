<?php


namespace App\LesEvenements\NosPropresEvenements\Events;

//use Symfony\Component\EventDispatcher\Event;
//use Symfony\Contracts\EventDispatcher\Event;
//use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserInterface;
//use \Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

///
/*
 * C'est la classe de l'évènement 'advert.post_message'
 * qui a été créé dans la classe finale 'AdvertEvent'
 * Comme l'évènement déclaré dans la classe 'AdvertEvents'
 * Porte à la fois sur le contenu du Message et son Auteur,
 * Alors on définit $message et $user pour dans notre classe ci-présente:
 */

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    ///
    public function __construct($message, $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    //Le Listener doit avoir accès au message,
    //d'où le getter
    public function getMessage()
    {
        return $this->message;
    }

    //Le Listener doit pouvoir modifier le message
    //D'où le setter
    public function setMessage($message)
    {
        $this->message = $message;
    }

    //Le Listener doit avoir accès à l'utilisateur
    public function getUser()
    {
        return $this->user;
    }

    //Pas de 'setUser', les listeners ne peuvent pas modifier l'auteur du message
    ///D'où PAS de SETTER Pour le USER
    /*
     *
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }
    */


}