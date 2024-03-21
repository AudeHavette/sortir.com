<?php

namespace App\EventListener;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SortieRepository;

class SortieEtatEventListener implements EventSubscriber
{
    public function SortieEtatEventListener  (Sortie $sorties, EntityManagerInterface $entityManager) : void
    {

/*
        foreach ($sorties as $sortie) {
            $dateDebut = $sortie->getDateHeureDebut();
            $duree = $sortie->getDuree();
            $dateFin = (clone $dateDebut)->modify("+{$duree} minutes");
            $dateActuelle = new \DateTime();

            $etatEncours = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'enCours']);
            $etatPasse = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'passee']);
            $etatCloture = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'cloturee']);

            if ($dateActuelle >= $dateDebut && $dateActuelle <= $dateFin) {
                $sortie->setEtat($etatEncours);
            } elseif ($dateActuelle > $dateFin && $dateActuelle <= $dateFin->modify('+1 month')) {
                $sortie->setEtat($etatPasse);
            } elseif ($dateActuelle > $dateFin->modify('+1 month')) {
                $sortie->setEtat($etatCloture);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();
        }
*/
    }

    public function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
    }
}