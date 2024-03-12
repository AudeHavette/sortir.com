<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $laBierePromise = new Lieu();
        $laBierePromise->setNom('la Bière promise');
        $laBierePromise->setRue('6 Avenue André Citroën');
        $laBierePromise->setVille($this->getReference('saint_herblain'));
        $manager->persist($laBierePromise);

        $laMaisonDuPasseur = new Lieu();
        $laMaisonDuPasseur->setNom('la Maison du Passeur');
        $laMaisonDuPasseur->setRue('1, Quai du Génie');
        $laMaisonDuPasseur->setVille($this->getReference('herblay'));
        $manager->persist($laMaisonDuPasseur);

        $laCiteMere = new Lieu();
        $laCiteMere->setNom('La Cité de la Mer');
        $laCiteMere->setRue('Allée du Président Menut');
        $laCiteMere->setVille($this->getReference('cherbourg'));
        $manager->persist($laCiteMere);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            VilleFixtures::class
        ];
    }
}
