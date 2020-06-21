<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertRappelEditType extends AbstractType
{
    ///
    /// Ce Formulaire hérite du Formulaire AdvertRappelType
    /// Ce Formulaire permet de Modifier une Annonce
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ///Lors de la modification d'une Annonce, nous avons choisi de supprimer le Champ "date" du Formulaire
        /// c'est-à-dire, le champ "date" qui est présent dans le formulaire "parent"
        /// se verra supprimé dans le formulaire "fils"
        $builder
            ->remove('date');
    }
    //
    //Cette méthode permet de retourner la Classe du Formualire Parent
    ///Ainsi, lors de la construction du Formulaire d'édition d'une Annonce,
    /// Le composant "form" exécutera d'abord la méthode "buildForm" du Formulaire parent
    /// avant d'exécuter celle qui vient supprimer le champ date.
    public function getParent()
    {
        return AdvertRappelType::class; //
    }
}
