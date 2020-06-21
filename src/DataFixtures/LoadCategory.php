<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoadCategory extends Fixture
{
    //Dans l'argument de la méthode load,
    // l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        //
        //Liste de noms de Catégories à Ajouter
        $names=array(
            'Développement Web',
            'Développement Mobile',
            'Graphisme',
            'Intégration',
            'Réseau'
        );
        //
        foreach ($names as $name){
            //On crée la Catégorie
            $category= new Category();
            $category->setName($name);
            //
            //On la persiste
            $manager->persist($category);
        }
        //
        //On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }
}
