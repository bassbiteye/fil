<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $libelle=array('super','admin','user','caissier');
        for($i=0;$i<count($libelle);$i++){
            $profil =new Profile();
            $profil->setLibelle($libelle[$i]);
            $manager->persist($profil);
        }
         $manager->flush();

    }
}
