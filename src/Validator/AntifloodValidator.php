<?php

namespace App\Validator;

use App\Entity\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
    /*
        * Enfin, il faut adapter notre validateur pour que d'une part
        * il récupère les données qu'on lui injecte, grâce au constructeur,
        * et d'autre part qu'il s'en serve tout simplement :
        * */
    private $requestStack;
    private $em;
    ///
    // Les arguments déclarés dans la définition du service arrivent au constructeur
    // On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la méthode validate()
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        /// // Pour récupérer l'objet Request tel qu'on le connait,
        ///  il faut utiliser getCurrentRequest du service request_stack
        $request = $this->requestStack->getCurrentRequest();

        //On récupère l'IP de celui qui poste
        $ip = $request->getClientIp();

        //    // On vérifie si cette IP a déjà posté une candidature
        // il y a moins de 15 secondes
        /*
        $isFlood = $this->em
            ->getRepository(Application::class)
            ->isFlood($ip, 15); // Bien entendu, il faudrait écrire cette méthode isFlood, c'est pour l'exemple
        ///
        /// NOTICE POUR CREER LA METHODE isFlood($ip, sec)
        /// **********************************************
        /// Il faudrait ajouter un attribut ip dans les entités
        ///  Advert  et Application , puis écrire un service
        ///  qui irait chercher si oui ou non l'ip courante
        ///  a créé une annonce ou une application
        ///  dans les X dernières secondes
        ///
        ///
        if ($isFlood){
            // C'est cette ligne qui déclenche l'erreur pour le formulaire,
            // avec en argument le message de la contrainte
            $this->context->addViolation($constraint->message);
        }
    */

        ///
        // /* @var $constraint \App\Validator\Antiflood */
        //Pour l'instant, on considère comme "flood" tout message de moins de 3 caractères
        /* Ce qui était là avant isFlood
        if (strlen($value) < 3) {
            // C'est cette ligne qui déclenche l'erreur pour le formulaire,
            // avec en argument le message de la contrainte
            $this->context->addViolation($constraint->message);
        }

        */
        //NOTA: L'argument $value de notre méthode "validate" correspond à la valeur de l'attribut
        // sur laquelle on a défini l'annotation. Par exemple si l'on avait
        //défini l'annotation comme ceci:
        /*
         /**
         * @Antiflood()
         private $content;
         *
         *  alors c'est tout logiquement le contenu de l'attribut $contenu
         * au moment de la validation qui sera injecté en tant
         * qu'argument $value.
         * */

        /*
            if (null === $value || '' === $value) {
                return;
            }
    */
        // TODO: implement the validation here
        /*
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        */
    }
}
