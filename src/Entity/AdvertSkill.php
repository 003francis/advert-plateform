<?php

namespace App\Entity;

use App\Repository\AdvertSkillRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdvertSkillRepository::class)
 */
//  Cette classe est issue de AdvertRappel et Skill
// C'est au fait une relation porteusse ayant pour attribut "level"
//
class AdvertSkill
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
    private $level;
    //
    //Nous défnissons à dans la suite les relations qu'elle a avec *
    //Ses Classes mères
    //a) Avec AdvertRappel: la Relation ManyToOne
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdvertRappel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    //b) Avec Skill: la Relation ManyToOne
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill;
    //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel(string $level)
    {
        $this->level = $level;

        return $this;
    }

    public function getAdvert()
    {
        return $this->advert;
    }

    public function setAdvert(AdvertRappel $advert)
    {
        $this->advert = $advert;
    }

    public function getSkill()
    {
        return $this->skill;
    }

    public function setSkill(Skill $skill)
    {
        $this->skill = $skill;
    }


}
