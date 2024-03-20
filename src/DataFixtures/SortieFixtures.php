<?php

namespace App\DataFixtures;


use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $apero = new Sortie();
        $apero->setNom('Boire un verre');
        $apero->setDateHeureDebut(new \DateTimeImmutable('2024-04-01 20:00:00'));
        $apero->setDuree(240);
        $apero->setDateLimiteInscription(new \DateTimeImmutable('2024-03-31 00:00:00'));
        $apero->setNbInscriptionsMax(40);
        $apero->setInfosSortie('Détente');
        $apero->setEtat($this->getReference('etat_cree'));
        $apero->setCampus($this->getReference('add_campus1'));
        $apero->addParticipant($this->getReference('participant_aude'));
        $apero->addParticipant($this->getReference('participant_brandon'));
        $apero->setOrganisateur($this->getReference('organisateur_clement'));
        $apero->setLieu($this->getReference('lieu_biere_promise'));
        $manager->persist($apero);
        $this->addReference('sortie_apero', $apero);

        $passeur = new Sortie();
        $passeur->setNom('Un endroit sympa');
        $passeur->setDateHeureDebut(new \DateTimeImmutable('2024-04-02 20:00:00'));
        $passeur->setDuree(120);
        $passeur->setDateLimiteInscription(new \DateTimeImmutable('2024-03-20 00:00:00'));
        $passeur->setNbInscriptionsMax(20);
        $passeur->setInfosSortie('Un bon moment');
        $passeur->setEtat($this->getReference('etat_ouvert'));
        $passeur->setCampus($this->getReference('add_campus2'));
        $passeur->addParticipant($this->getReference('participant_brandon'));
        $passeur->addParticipant($this->getReference('participant_clement'));
        $passeur->setOrganisateur($this->getReference('organisateur_aude'));
        $passeur->setLieu($this->getReference('lieu_maison_passeur'));
        $manager->persist($passeur);
        $this->addReference('sortie_passeur', $passeur);

        $tourisme = new Sortie();
        $tourisme->setNom('Visiter le musée de la mer');
        $tourisme->setDateHeureDebut(new \DateTimeImmutable('2024-04-22 14:00:00'));
        $tourisme->setDuree(120);
        $tourisme->setDateLimiteInscription(new \DateTimeImmutable('2024-03-21 00:00:00'));
        $tourisme->setNbInscriptionsMax(10);
        $tourisme->setInfosSortie('Un peu de tourisme');
        $tourisme->setEtat($this->getReference('etat_ouvert'));
        $tourisme->setCampus($this->getReference('add_campus3'));
        $tourisme->addParticipant($this->getReference('participant_aude'));
        $tourisme->addParticipant($this->getReference('participant_clement'));
        $tourisme->setOrganisateur($this->getReference('organisateur_brandon'));
        $tourisme->setLieu($this->getReference('lieu_cite_mer'));
        $manager->persist($tourisme);
        $this->addReference('sortie_tourisme', $tourisme);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EtatFixtures::class,
            CampusFixtures::class,
            UtilisateurFixtures::class,
            LieuFixtures::class
        ];
    }
}
