<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Application
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;
    //
    ///Relation ManyToOne entre les entités Application et AdvertRappel
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdvertRappel", inversedBy="applications")
     *
     * On interdit la création d'une "Application" sans son "Advert"(Annonce)
     * @ORM\JoinColumn(nullable=false)
     */
    ///Le paramètre "inversedBy" correspond au symétrique "mappedBy"
    /// i.e à l'attribut de l'entité INVERSE(advert) qui pointe vers l'entité propriétaire(Application)
    /// c'est donc l'attribut "applications" dans la classe "AdvertRappel)
    private $advert;

    //
    //On définit une date par défaut en le précisant dans le constructeur
    public function __construct()
    {
        $this->date= new \DateTime();
    }

    //

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
    //
    //
    ///NOTA: Pour qu'un $application->getAdvert()
    /// retourne effectivement un objet "annonce"
    /// il faut d'abord le définir en appelant
    /// $application->setAdvert($advert)

    public function getAdvert()
    {
        return $this->advert;
    }
    //
    ///On la relation entre "Application" et "AdvertRappel" est Obligatoire
    ///par l'attribut JoinColumn(nullable=false)
    /// Alors dans la méthode setAdvert, il n'y a pas le "=null" à l'initialisation
    /// de "AdvertRappel"
    public function setAdvert(AdvertRappel $advert)
    {
        $this->advert = $advert;
        //
        return $this;
        //
    }
    ///
    /// Nous définissions ci-dessous deux CallBacks pour mettre à jour
    /// l'attribut "nbApplications" de l'entité "AdvertRappel"
    /// Il s'agit des événements PrePersist et PreRemove
    /**
     * @ORM\PrePersist()
     */
    public function increase()
    {
    $this->getAdvert()->increaseApplication();
    }
    /// Pour la décrémentation du compteur

    /**
     * @ORM\PreRemove()
     */
   public function decrease()
   {
       $this->getAdvert()->decreaseApplication();
   }

}
