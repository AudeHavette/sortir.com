<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UtilisateurFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher){

    }


    public function load(ObjectManager $manager): void
    {

        $aude=new Utilisateur();
        $aude->setNom('Havette');
        $aude->setPrenom('Aude');
        $aude->setTelephone('06112346270');
        $aude->setEmail('aude.ma@hotmail.fr');
        $aude->setMotPasse($this->passwordHasher->hashPassword($aude, '1234'));
        $aude->setRoles(['ROLE_ADMIN']);
        $aude->setActif(true);
        $aude->setPseudo('Aude');
        $aude->setCampus($this->getReference('add_campus1'));
        dump($this->getReference('add_campus1'));
        //$aude->addSorty($this->getReference('sortie_apero'));
        $manager->persist($aude);
        $this->addReference('participant_aude', $aude);
        $this->addReference('organisateur_aude', $aude);

        $brandon=new Utilisateur();
        $brandon->setNom('Chartier');
        $brandon->setPrenom('Brandon');
        $brandon->setTelephone('06112346270');
        $brandon->setEmail('brandon@hotmail.fr');
        $brandon->setMotPasse($this->passwordHasher->hashPassword($brandon, '1234'));
        $brandon->setRoles(['ROLE_USER']);
        $brandon->setActif(true);
        $brandon->setPseudo('Brandon');
        $aude->setCampus($this->getReference('add_campus2'));
      //  $aude->addSorty($this->getReference('sortie_passeur'));
        $manager->persist($brandon);
        $this->addReference('participant_brandon', $brandon);
        $this->addReference('organisateur_brandon', $brandon);

        $clement=new Utilisateur();
        $clement->setNom('Morel');
        $clement->setPrenom('Clement');
        $clement->setTelephone('06112346270');
        $clement->setEmail('clement@hotmail.fr');
        $clement->setMotPasse($this->passwordHasher->hashPassword($clement, '1234'));
        $clement->setRoles(['ROLE_USER']);
        $clement->setActif(true);
        $clement->setPseudo('Clement');
        $aude->setCampus($this->getReference('add_campus3'));
       // $aude->addSorty($this->getReference('sortie_tourisme'));
        $manager->persist($clement);
        $this->addReference('participant_clement', $clement);
        $this->addReference('organisateur_clement', $clement);

        $manager->flush();
    }

    public function getDependencies()
    {
        return[
            CampusFixtures::class
        ];
    }
}
