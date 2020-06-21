<?php

namespace App\DataFixtures;

use App\Entity\AdvertRappel;
use App\Entity\AdvertUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoadUser extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        //Les noms d'utilisateurs à créer
        $listNames=array('Francis', 'Zélia', 'André');

        foreach ($listNames as $name){
            //On crée l'utilisateur
            $userAdvert = new AdvertUser();

            //Le nom d'utilisateur et le mot de passe sont identiques pour l'instant
            $userAdvert->setUsername($name);
            $userAdvert->setPassword($name);

            //On ne se sert pas du sel pour l'instant
            //$userAdvert->setSalt('');

            //On définit uniquement le rôle ROLE_USER qui est le rôle de base
            $userAdvert->setRoles(array('ROLE_USER'));
            //
            //Puis, on persiste l'objet
            $manager->persist($userAdvert);
        }

        $manager->flush();
    }
}
