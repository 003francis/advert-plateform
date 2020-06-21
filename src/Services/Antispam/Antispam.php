<?php


namespace App\Services\Antispam;


class Antispam
{
    //
    private $minLength;
    //
    ///Nous définissons dans le Constructeur du Service,
    /// les Arguments tels qu'ils ont été déclarés dans le fichier services.yaml

    public function __construct()
    {
        $this->minLength = 50; //On définit une Valeur pour notre minLength
        //Cette valeur sera utilisée partout où est appelé notre service 'Antispam'
    }
    //


    /**
     * Vérifie si le texte est un spam ou non
     * si le texte est inférieur à 50 caractères alors il est UN SPAM
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
       // return strlen($text) < 50;
        return strlen($text) < $this->minLength;

    }

}