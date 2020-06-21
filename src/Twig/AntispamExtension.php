<?php

namespace App\Twig;

use App\Services\Antispam\Antispam;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

///
/*
 * Cette Classe a été générée par la commande 'php bin/console make:twig-extension
 * Et Cette Classe est Définie ici comme une EXTENSION TWIG
 * Cette extesion Twig sera utilisée dans une VUE TWIG comme  toute autre FONCTION TWIG
 *
 * LE PRINCIPE de TOUT SERVICE est:
 * *******************************
 *    1 SERVICE = 1 MISSION
 * *******************************
 * Et la mission de notre petit service(micro service)
 * a comme "mission" d'être une "EXTENSION TWIG" concernant le SPAM
 * et c'est pourquoi, lui ne DOIT pas VERIFIER qu'un Texte est SPAM vu qu'il a deja UNE MISSION, celle d'être UNE EXTENSION TWIG
 * D'où IL FERA Juste appel à d'autres services de SPAM comme  'Antispam' qui se trouve dans 'App\Services'
 */

class AntispamExtension extends AbstractExtension
{
    ///On peut définir ou appliquer un Tag à ce service afin
    /// que ce dernier soit utilisé dans une vue
    /*
     * Chaque service qui récupère les services d'un certain "tag",
     * va exécuter telle ou telle méthode des services tagués.
     * En l'occurrence, TWIG va exécuter les méthodes suivantes:
     * getFilters() : qui retourne un tableau contenant les 'filtres' (par exemple "|upper" est un filtre) que le service ajoute à TWIG
     * getTests() : qui retourne les tests
     * getFunctions(): qui retourne les fonctions
     * getOperators(): qui retourne les opérateurs
     */
    ///
    /*
     * @var Antispam
     */
    private $antispam;

    ///Ici, ce service utilise le service 'Antispam' qui détecte
    ///si un Message donné est un SPAM ou NON
    /// D'où l'injection de dépendance dans le CONSTRUCTEUR ci-dessous:
    ///
    public function __construct(Antispam $antispam)
    {
        $this->antispam = $antispam;
    }
    ///
    /// Ici, on définit la fonction qui sera utilisée dans un gabarit TWIG
    /// comme vraiment une fonction TWIG.
    ///
    public function checkIfArgumentIsSpam($text)
    {
        return $this->antispam->isSpam($text); //on appelle ici, la méthode isSpam de la Classe App\Services\Antispam
    }

    ///TWIG va exécuter cette méthode pour savoir
    /// la(les) fonction(s) qu'ajoute notre Service(notre Extension) à TWIG
    public function getFunctions()
    {
        return array(
            new TwigFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam'))
        );
        /*Le premier argument de la fonction "TwigFunction" , est le nom de la fonction qui sera disponible dans nos VUES TWIG
         *Le deuxième Argument est Callable PHP (Un Callable PHP, est une fonction qui est souvent précédée par DEUX UNDESCORES(__)
         *Au final, {{ checkIfSpam(var) }} du côté TWIG
         * exécutera $this->isSpam($var) du côté classe "AntispamExtension"
         *
         */
    }

    ///
    ///
    /*
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }
    */
}
