<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $campus1 = new Campus();
        $campus1->setNom('Campus de Saint-Herblain');
        $manager->persist($campus1);
        $manager->flush();
        dump($campus1->getId());
        $this->addReference('add_campus1', $campus1);

        $campus2 = new Campus();
        $campus2->setNom('Campus de Chartres-de-Bretagne');
        $manager->persist($campus2);
        $manager->flush();
        $this->addReference('add_campus2', $campus2);

        $campus3 = new Campus();
        $campus3->setNom('Campus de La-Roche-Sur-Yon');
        $manager->persist($campus3);
        $manager->flush();
        $this->addReference('add_campus3', $campus3);

    }
}
