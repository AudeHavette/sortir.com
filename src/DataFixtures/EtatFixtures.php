<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etat1 = new Etat();
        $etat1->setLibelle(' sortie créée');
        $manager->persist($etat1);
        $this->addReference('etat_cree',$etat1);

        $etat2 = new Etat();
        $etat2->setLibelle(' sortie ouverte');
        $manager->persist($etat2);
        $this->addReference('etat_ouvert',$etat2);

        $etat3 = new Etat();
        $etat3->setLibelle(' sortie clôturée');
        $manager->persist($etat3);
        $this->addReference('etat_cloture',$etat3);

        $etat4 = new Etat();
        $etat4->setLibelle(' Activité en cours');
        $manager->persist($etat4);
        $this->addReference('etat_encours',$etat4);

        $etat5 = new Etat();
        $etat5->setLibelle(' Activité passée');
        $manager->persist($etat5);
        $this->addReference('etat_passe',$etat5);

        $etat6 = new Etat();
        $etat6->setLibelle(' Activité annulée');
        $manager->persist($etat6);
        $this->addReference('etat_annule',$etat6);

        $manager->flush();
    }
}
