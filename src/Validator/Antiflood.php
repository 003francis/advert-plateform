<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
//////On crée notre propre CONSTRAINTE
/// Notre Contrainte a pour finalité d'Imposer un délai de 15 secondes
/// entre Chaque message Posté sur le site (que ce soit une annonce ou une candidature)
///
/// notre objectif pour cette contrainte d'anti-flood :
///  on veut empêcher quelqu'un de poster à moins de 15 secondes d'intervalle.
/// Il nous faut donc un accès à son IP pour le reconnaitre,
/// et à la base de données pour savoir quand était son dernier post.
/// Tout cela est impossible sans service.

/**
 * @Annotation
 */
/*
 *  cette annotation ci-dessus est important pour que cette nouvelle contrainte
 * soit disponible via les annotations dans les autres classes.
 * Il est à NOTER que toutes les classes ne sont pas des annotaions
 */
class Antiflood extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message="Vous avez déjà posté un message il y a 15 secondes, Merci d'attendre un peu.";
    ///NOTA: Les Options de l'annotation correspondent en réalité aux "attributs publics"
    /// de la classe d'annotation. Ici, on a l'attribut "message". Et en Appel, on pourra donc dire:
    /// @Antiflood(message="Mon message personnalisé)
    ///
    /// Après la définition de notre CONTRAINTE, passons maintenant à la création
    /// de Son VALIDATEUR.
    /// NOTA: C'est la CONTRAINTE qui décide par quel VALIDATEUR elle doit se faire Valider
    /// Par défaut, une CONTRAINTE Ccc demande à se faire valider par le Validateur CccValidator
/*
    public function validatedBy()
{
    return 'service_antiflood'; // Ici, on fait appel à l'alias du service

   // return parent::validatedBy(); // TODO: Change the autogenerated stub
}
*/
}