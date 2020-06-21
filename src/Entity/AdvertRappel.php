<?php

namespace App\Entity;

use App\Repository\AdvertRappelRepository;
use App\Validator\Antiflood;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

//Ce use nous permet de définir les règles de Validation sur Les Champs de cette Entité
//Ces Règles, on les appelle LES CONTRAINTES
use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; //Pour la Contrainte UniqueEntity qui se trouve dans Le Pont (Bridge) entre "Doctrine et Symfony"
use Symfony\Component\Validator\Constraints as Assert; //Pour les contraintes Utilisant le composant ou plugin ou encore Bundle "Validator"
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=AdvertRappelRepository::class)
 *
 * On vérifie que qu'une valeur d'un attribut est UNIQUE parmi toutes les entités existantes en BD
 * Elle pratique pour vérifier qu'une adresse e-mail n'existe pas déjà dans la BD:
 * On définit cette contrainte comme suit:
 *
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre.")
 * Pour être logique, il faut aussi mettre la colonne "title" en Unique pour Doctrine
 *
 * @ORM\HasLifecycleCallbacks()
 * On définit les callbacks de cycle de vie avec l'annotation ci-dessus
 */
class AdvertRappel
{
    //
    //
    //Les Infos en Annotations sont les métadonnées(metadata) de l'entité(ou de l'objet)
    //Et Rajouter les metadonnées à un Objet(entité) s'appelle "Mapper l'Objet"
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * Règle sur La date : La date doit être une date valide, d'où on le définit par la règle ci-dessous:
     * @Assert\DateTime(message="Saisissez une date valide 'YYYY-MM-DD'")
     */
    private $date;

    /**
     * On définit la colonne "title" en unique vu la CONTRAINTE "UniqueEntity" définie ci-dessus
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * Le titre doit avoir au minimum 10 caractères
     * @Assert\Length(min=10, minMessage="Le Titre doit faire au moins {{ limit }} caractères.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * Le nom de l'auteur doit avoir au moins 2 caractères
     * @Assert\Length(min=2, minMessage="Le nom de l'auteur doit avoir au minimum {{ limit }} caractères.")
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * Le contenu ne doit pas être vide, on doit y mentionner quelque chose
     * @Assert\NotBlank(message="Le Contenu ne peut être vide.")
     *
     * On utilise notre Contrainte que nous avons créée
     * @Antiflood()
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published = true;
    //
    //Relation OneToOne entre AdvertRappel et Image
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     *
     * L'Image doit être valide:
     *
     * @Assert\Valid()
     *
     * La Contrainte "\Valid()" va déclencher la validation du sous-objet
     * 'ici' $image selon ses propres règles de validation
     * qui doivent être définies dans l'entité "Image"
     */
    /* on rend la relation non-facultative
    // @ORM\JoinColumn(nullable=false)
    */
    private $image;
    //
    //RELATION ManyToMany entre AdvertRappel et Category
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", cascade={"persist"})
     */
    //Si on veut nommer la Table de Jointure, voici le ligne
    // à mettre juste en dessous de l'annotation ci-dessus
    //@ORM\JoinTable(name="oc_advert_category")
    private $categories;
    ///
    /// Implémentation d'une RELATION BIDIRECTIONNELLE
    /// L'entité Advert(entité Inverse) est dans la relation ManyToOne
    ///  avec l'Entité "Application"(entité Propriétaire). Pour cela,
    /// On doit rajouter un Attribut (pour l'entité "Application") et Son Annotation
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Application", mappedBy="advert" )
     */
    ///L'inverse de ManyToOne est OneToMany (A ne pas oublier dans toute entité Inverse)
    /// Le "mappedBy" correspond à l'attribut de l'entité propriétaire(Application) qui
    /// pointe vers l'entité "inverse" (Advert)
    /// c'est Donc le "private $advert" de l'entité "Application"
    /// on renseigne ce "mappedBy" pour que l'entité" INVERSE soit
    ///  au courant des caractéristiques de la relation: celles-ci sont définies dans l'annotation de l'entité propriétaire
    /// Dans l'entité Propriétaire(Application), on va rajouter le paramètre "inversedBy" en mettant
    /// comme valeur, la propriété "applications" ci-dessous
    private $applications; // La présence de "s" traduit juste le fait qu'une advert est liée à plusieurs applications


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    ///
    ///
    ///Comme, on ne veut pas charger à chaque fois, les annonces et leurs applications,
    /// on définit un attribut "nbApplications" qui est incrémenté ou décrémenté
    /// quand un objet "Application" est Ajouté ou Supprimé
    /**
     * @ORM\Column(type="integer")
     */
    private $nbApplications = 0;

    /**
     * @ORM\Column(type="string")
     */
    private $user;
    ///
    ///


    //
    //On définit les valeurs par défaut d'un Objet dans son constructeur
    //Par ex.: Par défaut, la date de l'annonce est la date d'aujourd'hui
    //On le définit donc dans le constructeur
    public function __construct()
    {
        //Par défaut, la date de l'annonce est la date d'aujourd'hui
        // Comme la propriété $categories doit être un ArrayCollection,
        // On doit la définir dans un constructeur :
        $this->date = new \DateTime();
        //Comme On a Pour Une "Advert" beaucoup de Catégories et Beaucoup de Applications,
        //Il nous faut déclarer, dans ce Constructeur, un "ArrayCollection" qui nous permettra d'Ajouter plusieurs instances,
        // mais une par une
        //On définit la liste de catégories dans le constructeur de AdvertRappel
        $this->categories = new ArrayCollection();
        //
        //On définit la liste de Application dans le Constructeur de AdvertRappel
        $this->applications = new ArrayCollection();
        //
        //On définit par défaut la valeur de $user à la valeur de l'utilisateur connecté
        //$this->user= $user->getUsername();

    }

    //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
    //
    //On force le type de l'argument pour le setter
    //Ceci permet de déclencher une erreur si on essaie de passer
    //un autre objet que Image à  la méthode
    ///Le "=null" est mis étant donné que la relation entre "image" et "AdvertRappel
    /// est Facultative, c'est-à-dire qu'on peut créer un objet "AdvertRappel" sans un objet "Image"
    public function setImage(Image $image = null)
    {
        $this->image = $image;
    }

    //Categories (notez la présence de "s") car on récupère plusieurs catégories
    public function getCategories()
    {
        return $this->categories;
    }

    //Pour La propriété $categories, on aura deux setters
    ///"addCategory" pour ajouter une seule catégorie à la fois.
    /// "removeCategory" pour supprimer une catégorie
    public function addCategory(Category $category)
    {
        //On ajoute les Catégories, une par une, dans l'Objet ArrayCollection categories
        // défini dans le constructeur ci-haut
        $this->categories[] = $category;
    }
    //Cette méthode va nous aider à supprimer un ELement dans l'ensemble de
    // toutes les instances liées à un Objet Advert
    public function removeCategory(Category $category)
    {
        //Ici, on utilise une méthode de l'ArrayCollection, pour supprimer
        //La catégorie en argument
        $this->categories->removeElement($category);
    }
    //
    //
    public function getApplications()
    {
        return $this->applications;
    }

    //Mêmes explications que Pour addCategory() et removeCategory()
    public function addApplication(Application $application)
    {
        $this->applications[] = $application;
        /// On se rappelera qu'il existe une relation BIDIRECTIONNELLE
        /// entre AdvertRappel et Application
        ///Pour appeler facilement les setters ou pour mieux faire:
        ///  $advert->addApplication($application) et
        /// $application->setAdvert($advert), il faut lier les deux entités
        ///  dans l'une ou l'autre des ces deux entités (pas les deux à la fois)
        /// et Nous avons choisi de les lier ici dans l'entité "AdvertRappel"

        //On lie l'annonce Advert à la candidature "Application"
        $application->setAdvert($this);
        ///
        /// NOTA TRES IMPORTANT:::::
        /// Après avoir lier les entités de cette manière, pour le Setter(i.e l'Ajout d'une Instance)
        /// dans le reste du code(contrôleur, service, etc),
        /// Il faudra exécuter "$advert->addApplication()"
        /// qui garde la cohérence entre les deux entités
        /// Car il va exécuter en interne "$application->setAdvert()"
        /// Cependant, Il ne faudra JAMAIS exécuter "$application->setAdvert()"
        /// car, lui ne garde pas la cohérence étant donné que les deux entités sont désormais liées.

        // return $this;
    }

    //
    public function removeApplication(Application $application)
    {
        $this->applications->removeElement($application);
        //
        // Et si notre relation était facultative (nullable=true, ce qui n'est pas notre cas ici attention) :
        // $application->setAdvert(null);
    }

    //
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        // return $this;
    }
    //
    ///La méthode ci-dessous va permettre la mise à jour automatique
    /// de la date d'une annonce lors  de son édition
    /// Elle sert en effet de "Callback" défini ci-haut
    ///  par l'annotation "HasLifecycleCallbacks"
    /// Nous allons définir un évènement PreUpdate sur cette méthode
    /// i.e que la méthode va être exécutée juste avant que
    ///  l'entité ne soit modifiée en Base de Données.
    /**
     * @ORM\PreUpdate()
     */

    public function updateDate()
    {
        $this->setUpdatedAt(new \DateTime());
        ///On définit l'attribut updatedAt à la date actuelle
        /// de sorte que soit mise à jour automatiquement la date d'édition d'une annonce
    }

    public function getNbApplications(): ?int
    {
        return $this->nbApplications;
    }

    public function setNbApplications(int $nbApplications): self
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }
    ///
    /// Nous allons créer des méthodes qui vont nous permettre
    /// d'incrémenter et de décrémenter le compteur de nombre de candidatures(applications)
    ///à une annonce donnée
    public function increaseApplication()
    {
        $this->nbApplications++;
    }

    //
    public function decreaseApplication()
    {
        $this->nbApplications--;
    }
    ///NOTA: Après avoir créé ces méthodes dans l'entité AdvertRappel,
    /// et comme la mise à jour du compteur dans l'entité "AdvertRappel"
    ///  concerne l'entité "Applications",
    /// On doi définir 2 CallBacks dans l'entité "Applications"
    ///
    ///
    /// LES CONTRAINTES SUR LES METHODES
    ///
    /// Contraintes sur le Getter :Un Getter peut commencer par "get" ou par "is"
    /**
     * @Assert\IsTrue(message="L'entité doit être Vraie")
     */
    public function isAdvertValid()
    {
        //return false;

        ///Avec un Tel exemple, on considère l'annonce comme toujours non valide
        /// Car La Contrainte attend que le Getter renvoie "true"
        ///  alors que celui-ci envoie "false"
    }
    ///

    /**
     * Ici on définit une Contrainte sur UN ATTRIBUT: Il suffit de faire précéder un attribut de "is" suivi de son nom...
     * Par Exemple "isTitle": le Getter sur lequel on définit la Contrainte
     * @Assert\IsTrue(message="Le titre doit être vrai...")
     */
    public function isTitle()
    {
        // return false;

    }
    ///
    ///La Contrainte "Callback" permet une personnalisation
    ///  à souhait des contraintes
    ///Par exemple, elle peut nous aider à vérifier que le Contenu d'un Champ
    /// contient certains mots non-désirés ou non permis
    /**
     * @Assert\Callback
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        //On définit un ensemble de mots non-permis
        $forbiddenWords = array('démotivation', 'abandon', 'sexe');

        //On vérifie que le champ "contenu" ne contient pas l'un des mots définis dans "$forbiddenWords"
        ///On utilise les REGEX, ici on utilise le Langage PCRE(Perl Compatible Regular Expressions)
        if (preg_match('#' . implode('|', $forbiddenWords) . '#', $this->getContent())) {
            ///Alors la règle est Violée, on définit l'erreur
            $context
                ->buildViolation('Contenu invalide car il contient un mot interdit')//Message à afficher à l'internaute
                ->atPath('content') //atribut de l'objet qui est violé
                ->addViolation(); //Ceci déclenche l'erreur... A ne Jamais Oublier
        }
        ///NOTA: 1)Avec cette Contrainte "Callback" on peut même arriver
        ///  à comparer des attributs entre eux
        /// par exemple, pour interdire le pseudo dans un mot depasse.
        /// 2)L'avantage du Callback par rapport à une simplz contrainte sur un getter,
        /// c'est de pouvoir ajouter plusieurs erreurs à la fois,
        /// en définissant sur quel attribut chacune se trouve grâce
        /// à la méthode "atPath"(en mettant "content" ou "title", etc)
    }


    public function getUser()
    {
        return $this->user;
    }
    public function setUser($user)
    {
        $this->user = $user;
    }
    ///
    ///

}
