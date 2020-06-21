<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoadSkill extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        //
        //Liste de noms de compétences à ajouter
        $names=array(
            'PHP',
            'Symfony',
            'C++',
            'Java',
            'PhotoShop',
            'Blender',
            'Bloc-notes'
        );
        //
        foreach ($names as $name){
            //On crée la compétence
            $skill= new Skill();
            $skill->setName($name);
            //
            //On la persiste
            $manager->persist($skill);
        }
        //On déclenche l'enregistrement de toutes les compétences
        $manager->flush();
    }
}
