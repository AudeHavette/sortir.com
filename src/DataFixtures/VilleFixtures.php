<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $saintHerblain = new Ville();
        $saintHerblain->setNom('Saint-Herblain');
        $saintHerblain->setCodePostal('44800');
        $manager->persist($saintHerblain);
        $this->addReference('saint_herblain', $saintHerblain);




        $herblay = new Ville();
        $herblay->setNom('Herblay');
        $herblay->setCodePostal('95220');
        $manager->persist($herblay);
        $this->addReference('herblay', $herblay);


        $cherbourg = new Ville();
        $cherbourg->setNom('Cherbourg');
        $cherbourg->setCodePostal('50100');
        $manager->persist($cherbourg);
        $this->addReference('cherbourg', $cherbourg);


        $manager->flush();
    }
}
