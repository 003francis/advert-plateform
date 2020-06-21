<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;
    //
    ///Cet Attribut nous permet de récupérer le Fichier Chargé
    /// Cet attribut n'a pas d'annotations car il ne sera pas persisté en BD
    /// Cet attribut sert plutôt au Formulaire
    private $file;
    //
    ///On ajoute un Champ pour stocker le nom du Fichier,
    ///  avant sa suppression effective dans la Base de données
    private $tempFilename;

    ///

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    ///
    ///
    public function getFile()
    {
        return $this->file;
    }

    ///
    /// On modifie le setter de "file", pour prendre en compte l'upload d'un fichier
    /// lorsqu'il en existe déjà un autre
    ///
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        //On vérifie si on avait déjà un fichier pour cette entité
        if (null !== $this->url) { //Si on a déjà un fichier
            //On sauvegarde l'extension du fichier pour le supprimer plus tard
            $this->tempFilename = $this->url;

            //On réinitialise les valeurs des attributs "url" et "alt"
            $this->url = null;
            $this->alt = null;
        }
    }
    ///
    ///// Les Deux Evts permettent de Remplir, avant l'enregistrement effectif en BD, les Attributs $url et $alt
    //    /// avec les bonnes valeurs suivant le fichier envoyé
    //    /// Et On doit IMPERATIVEMENT le faire avant l'enregistrement en BD
    //    ///  pour qu'ils puissent être enregistrés eux-mêmes en BD, Sinon, Ils seront Null en Bd et par Conséquent Echec D'enregistrement

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    ///Cette méthode permet la génération des attributs $url et $alt
    public function preUpload()
    {
        //Si jamais, il n'y a pas de fichier(vu que le champ est facultatif), on ne fait rien
        if (null == $this->file) { //Pas de fichier chargé
            return;
        }
        ///
        ///Si Fichier CHargé,
        /// On change le nom du fichier Chargé... et désormais
        ///Le nom du fichier est son id, on doit juste stocker également son extension
        /// Pour faire propre, on devrait renommer cet attribut en "extension", plutôt que "url"
        ///
        $this->url = $this->file->guessExtension(); //On récupère l'extension du Fichier chargé par le client
        //
        ///Et on génère l'attribut "alt" de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName(); //On récupère le Nom du Fichier original Chargé par le Client

    }
    ///
    //Ces Deux Evts permettent, juste après l'enregistement dans la BD,
    //De Déplacer effectivement le fichier envoyé dans le répertoire /public/ de notre Application
    //On Ne doit pas le faire avant l'enregistrement en BD car
    // on risque de se retrouver avec un Fichier Orphelin en cads d'echec d'enregistrement dans la BD
    //
    ///
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    /// La méthode ci-dessous, va permettre le déplacement effectif du Fichier
    /// elle s'occupe concrètement de notre fichier
    public function upload()
    {
        ///Si jamais il n'y a pas de fichier(champ facultatif), on ne fait rien
        if (null == $this->file) {
            return;
        }

        ///Si on avait un ancien fichier, on le supprime
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile); //Permet de Supprimer l'ancien fichier
            }
        }
        /// Sinon,
        /// On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
            $this->getUploadRootDir(), //Le répertoire de Destination
            $this->id . '.' . $this->url   //Le nom du fichier à créer, ici "id.extension"
        );
        /*
                ///On récupère le nom original du fichier de l'internaute
                $name = $this->file->getClientOriginalName();
                ///
                /// On déplace le fichier envoyé dans le répertoire de notre choix
                $this->file->move($this->getUploadRootDir(), $name);
                ///
                ///On Sauvegarde le nom de Fichier dans notre attribut $url
                $this->url = $name;

                ///On crée également le futur attribut "alt" de notre balise <img>
                $this->alt = $name;
                */
    }
    ///
    /////Cet Evt permet, juste avant la Suppression de l'image,
    //    // de sauvegarder le nom du fichier dans un attribut non persisté $tempFilename dans notre cas
    //    //
    /**
     * @ORM\PreRemove()
     */
    //Cette Méthode Permet de Sauvegarder le Nom du Fichier
    // qui dépend de l'Id de l'entité, dans un attribut temporaire
    public function preRemoveUpload()
    {
        //On Sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFilename = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->url;
    }
    ///
    /// //
    //    //Cet Evt permet, juste après la Suppression,
    // de supprimer le Fichier qui était associé à l'entité
    //    //
    /**
     * @ORM\PostRemove()
     */
    //Cette méthode supprime effectivement le fichier Image grâce au nom enregistré
    //
    public function removeUpload()
    {
        //En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé dans "tempFilename"
        if (file_exists($this->tempFilename)) {
            //On supprime le fichier
            unlink($this->tempFilename); //On supprime le fichier temporaire
        }
    }
    ///

    ///
    public function getUploadDir()
    {
        //On retourne le chemin relatif vers l'image
        // pour un navigateur(relatif au répertoire /public)
        //Le répertoire dans lequel sont stockées nos images
        return 'assets/toutesLesImagesChargees';
        ///'assets/toutesLesImagesChargees' Définit le répertoire dans lequel on va stocker toutes nos images chargées
        /// Ce répertoire est relatif au répertoire web, ici '/public'
        /// On retourne ce chemin relatif qui sera utilisé dans nos vues car
        /// les navigateurs sont relatifs au répertoire /public.
        ///
    }

    ///
    protected function getUploadRootDir()
    {
        //On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__ . '/../../public/' . $this->getUploadDir();
        ///
        /// Cette méthode retourne le chemin EN ABSOLU vers
        ///  le fichier chargé dans notre application
        /// __DIR__: représente le répertoire absolu du fichier courant, ici, il s'agit de notre entité "Image"

    }
    ///NOTA: Avec cette façon ci-haut de traiter l'image,
    /// on peut créer des Annonces avec des images jointes
    /// et on verra automatiquement les fichiers téléchargés apparaître
    /// dans /public/assets/toutesLesImagesChargees.
    /// en Supprimant une annonce, l'image jointe sera automatiquement supprimée du répertoire
    ///
    ///
    /// On récupère l'emplacement du Fichier image à afficher
    ///
    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->getId() . '.' . $this->getUrl();
    }
    ///Et enfin, on va modifier la vue view.html.twig


    ///
}
