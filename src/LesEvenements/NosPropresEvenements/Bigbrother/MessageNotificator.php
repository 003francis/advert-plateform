<?php


namespace App\LesEvenements\NosPropresEvenements\Bigbrother;

/*
 * Cette classe, possède notre méthode qui sera exécute par Notre Listener "MessageListener"
 * Elle est donc le Service que notre Listener va appeler
 */

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;

//use \Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
    protected $mailer;

    //

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    ///
    /// Méthode pour notifier par e-mail un administrateur
    public function notifyByEmail($message, $user)
    {
        $message = new \Swift_Message();
        //
        $message
            ->setSubject("Nouveau message d'un Utilisateur surveillé!")
            ->setFrom('francistshimbombo@gmail.com')
            ->setTo('francistshimbombo@gmail.com')
            ->setBody("L'utilisateur surveillé \" " . $user . " \" a posté le message suivant : \" " . $message . " \" ");
        ///
        /// On envoie le mail
        $this->mailer->send($message);
       // $this->mailer->send($);
        ///


    }

}