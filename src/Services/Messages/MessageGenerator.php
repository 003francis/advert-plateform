<?php


namespace App\Services\Messages;

//use Psr\Log\LoggerInterface;
//Ce service a pour fonction d'envoyer un Message à un Utilisateur
class MessageGenerator
{
    //
    //
    public function getHappyMessage()
    {
        //
        $messages=[
            'Vous l\'avez fait!!! Vous avez mis à jour le Système! Félicitations!!!',
            'ç\'a été une bonne mise à jour jamais vue depuis qu\'il est Jour!',
            'Bon Boulot! Continuez à bosser!',
        ];
        //
        //On sélectionne un message aléatoirement dans le tableau $message
        $index=array_rand($messages);
        //
        return $messages[$index];
        //
    }

}