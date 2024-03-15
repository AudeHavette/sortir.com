<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function SearchByFilters($nomSortie, $campusId, $dateDebut, $dateFin, $organisateurId)
    {
        $qb = $this->createQueryBuilder('s');

        // Filtrage par nom de sortie
        if ($nomSortie !== null) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nomSortie . '%');
        }

        // Filtrage par campus
        if ($campusId !== null) {
            $qb->andWhere('s.campus = :campus_id')
                ->setParameter('campus_id', $campusId);
        }

        // Filtrage par date de début et date de fin
        if ($dateDebut !== null && $dateFin !== null) {
            $qb->andWhere('s.dateHeureDebut BETWEEN :date_heure_Debut AND :date_limite_inscription')
                ->setParameter('date_heure_Debut', $dateDebut)
                ->setParameter('date_limite_inscription', $dateFin);
        }

        // Filtrage par participant
        if ($organisateurId !== null) {
            $qb->leftJoin('s.organisateur', 'p')
                ->andWhere('p.id = :organisateur_id')
                ->setParameter('organisateur_id', $organisateurId);
        }

        // Filtrage par date de fin dépassée de plus d'un mois
        $dateLimite = new \DateTime();
        $dateLimite->modify('-1 month');
        $qb->andWhere('s.dateLimiteInscription < :date_limite_inscription')
            ->setParameter('date_limite_inscription', $dateLimite);

        return $qb->getQuery()->getResult();
    }






    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /*
     * récupère sorties en lien avec une recherche par filtres
     * @return Sortie[]
     */
    public function findSearch(SearchData $filters): array
    {
        $queryBuilder=$this
            ->createQueryBuilder('s');
        if ($filters->campus) {
            $queryBuilder
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $filters->campus);
        }

        if ($filters->nom) {
            $queryBuilder
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$filters->nom.'%');
        }

        if ($filters->date) {
            $queryBuilder
                ->andWhere('s.dateHeureDebut >= :date')
                ->setParameter('date', $filters->date);
        }

        if ($filters->fin) {
            $queryBuilder
                ->andWhere('s.dateHeureDebut <= :fin')
                ->setParameter('fin', $filters->fin);
        }

        if ($filters->organisateur) {
            $queryBuilder
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $filters->organisateur);
        }

        if ($filters->participants) {
            $queryBuilder
                ->leftJoin('s.participants', 'p')
                ->andWhere('p = :participants')
                ->setParameter('participants', $filters->participants);
        }


        return $queryBuilder->getQuery()->getResult();
    }
}
