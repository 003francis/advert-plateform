<?php

namespace App\Form;

use App\Entity\AdvertRappel;
use App\Entity\Category;
use App\Entity\Image;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

///
/// Cette classe a été générée par la commande CLI:
/// php bin/console make:form AdvertRappel
/// Elle est donc LE CONSTRUCTEUR DE FORMULAIRE
/// Cette clasee sert donc de REUTILISABILITE
class AdvertRappelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ///Arbitrairement, on récupère toutes les catégories qui commencent par "D"
        $pattern = 'O%'; //Dont la première lettre est 'O'
        ///La méthode qui se charge d'afficher de traiter ce cas est décrité dans
        /// la classe CategoryRepository
        ///
        ///Ici, on construit les champs du Formulaire
        ///  sur Base des Attributs de l'Objet AdvertRappel
        /// Et Pour Commencer à le CONSTUIRE ICI, Il faut d'abord
        /// Préciser dans la Méthode "configureOptions()" ci-dessous,
        ///  La CLASSE sur Laquelle est CONSTRUIT LE FORMULAIRE
        $builder
            ->add('date', DateType::class) //Le Champ Date
            ->add('title', TextType::class) //Le Champ title
            ->add('content', TextareaType::class)
            ->add('author', TextType::class)
            ///On ajoute le 3è argument qui définit les "Options du Champ"
            /// Ces Options se présentent sous la forme d'un simple tableau :
            /// ///NOTA: Tout Champ de Formulaire est requis par défaut,
            /// et si on veut le rendre Facultatif, il faut  préciser l'option "required" à 'false' à la fin
            ///
            ///LES EVENEMENTS DU FORMULAIRE:
            /// Nous supprimons ici le champ "published" pour le rajouter via les EVENEMENTS DU FORMULAIRE
            //->add('published', CheckboxType::class, array('required' => false))
            ///On imbrique le Formulaire "Image" dans le Formulaire Advert
            ->add('image', ImageType::class, array('required' => false))
            /*
             * -1er Argument: Nom du Champ, ici "categories", car c'est le nom de l'attribut
             * -2e Argument: Type du Champ, ici "CollectionType", qui est une liste de quelque chose
             * car Nous avons beaucoup de categories suivant notre définition de l'entité "AdvertRappel"
             * -3e Argument: Tableau d'options du Champ "Collection"
            */
            ///LE TYPE DE CHAMP Ici est "CollectionType"
            /// CollectionType ne tient pas compte des données existantes(en BD) dans l'entité "Category"
            /// Avec ça, on se contente plutôt de créer de nouvelles entités
            /*
            ->add('categories', CollectionType::class, array(
                //On Ajoute 3 Options du Champ "CollectionType"
                'entry_type'=>CategoryType::class, //On précise de quelle collection s'agit-il.Ici, il s'agit de la Collection issue de CategoryType
                'allow_add'=>true, // Permet d'ajouter des entrées en plusdans la collection
                'allow_delete'=>true  //Permet de Supprimer les lignes dans la collection
            ))
            */

            ->add('categories', EntityType::class, array(
                'required'=>false,
                'class' => Category::class, //On sélectionne une Entité gérée par DOCTRINE et qui nous concerne dans notre cas
                'choice_label' => 'name', // On Choisit selon quel label seront affichées les données
                'multiple' => true,  //Pour sélectionner un ou plusieurs items: (Il est très important de mentionner cette Option
                //Si 'multiple'= false, on a un select box; sinon on a une LISTE
                //Si 'multiple'= true,  on a une LISTE
                'expanded' => false, //On peut mentionner aussi cette Option et changer Sa valeur et adminer le résultat
                //Par défaut les Options "multiple et expanded" sont à "false"
                //
                //L'Option "query_builder" permet d'effectuer un filtre
                'query_builder' => function (CategoryRepository $repository) use ($pattern) {
                    return $repository->getLikeQueryBuilder($pattern);
                }
            ))
            ///On peut ajouter le Bouton d'envoi, ici dans le CONSTRUCTEUR du FOrmulaire ou
            ///  directement dans la vue(*.html.twig) qui rend visible notre formulaire, dans ce cas, il faut enléver sa défintion à ce niveau
            /// C'est le cas avec Le Bouton "supprimer" de l'action "Supprimer". IL FAUT QUE SON TYPE soit "SUBMIT"
            ->add('enregistrer', SubmitType::class); ///Le Bouton d'envoi
        ///
        /// On ajoute une fonction qui va écouter un EVENEMENT DU FORMULAIRE
        /// Elle va permettre de supprimer ou d'ajouter le champ "pusblished"
        ///  selon que ce champ se trouve(avec valeur 1) ou non (avec valeur 0) dans la BD
        /// Si l'annonce est déjà publiée, i.e "published=true", ce champ ne s'affichera plus dans le formulaire
        ///Sinon, il s'affichera...

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, //1er argument: L'évènement qui nous intéresse: ici, PRE_SET_DATA
            function (FormEvent $event) {  //2e argument: la fonction à exécuter lorsque l'évènement est déclenché
                //On récupère notre Objet Advert sous-jacent
                $advert = $event->getData();
                // $advert= new AdvertRappel();
                ///
                /// NOTA: A la première création du formulaire,
                ///  celui-ci exécute sa méthode setData() avec null en argument
                /// d'où:
                ///Quand $advert est null, on ne fait rien. Cette Fonction est importante
                if (null == $advert) {
                    return;
                }
                ///
                /// Si l'annonce n'est pas publiée,
                /// ou si elle n'existe pas encore en Base(id est null)
                if (!$advert->getPublished() || null == $advert->getId()) {
                    //Alors on ajoute le Champ "published"
                    $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                } else{
                    //Sinon, on le supprime
                    $event->getForm()->remove('published');
                }
            }
        );
        ///
        ///
        ///Après cette DEFINITION du FORMULAIRE à ce Niveau, ON se rend dans le
        /// Controller pour Sa Création à partir d'une INstance de AdvertRappel
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        ///Ici, on définit la classe (l'Objet) sur lequel est CONSTRUIT CE FORMULAIRE
        /// L'option de cette méthode est "data_class"
        /// IL FAUT COMMENCER PAR MODIFIER CETTE METHODE AVANT la Méthode "buildForm"
        $resolver->setDefaults(['data_class' => AdvertRappel::class]);
    }
}
