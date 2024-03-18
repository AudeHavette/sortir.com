<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\SearchData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SortieController extends AbstractController
{


    #[Route('/createSortie', name: 'app_createSortie')]
    #[IsGranted('ROLE_USER')]
    public function creationSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $sortie = new Sortie();

        $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'creee']);
        if ($etatCree) {
            $sortie->setEtat($etatCree);
        } else {
            dump('L\'état "sortie créée" n\'a pas été trouvé.');
        }

        //  dump($etatCree);
        //  $sortie->setEtat($etatCree);
        $sortie->setOrganisateur($this->getUser());


        $SortieForm = $this->createForm(SortieType::class, $sortie);
        $SortieForm->handleRequest($request);

        if ($SortieForm->isSubmitted()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie créée !');
            return $this->redirectToRoute('app_createSortie');
        }

        return $this->render('sortie/creation.html.twig', [
            'sortieForm' => $SortieForm->createView()
        ]);
    }





    #[Route('/editSortie/{id}', name: 'app_editSortie')]
    #[IsGranted('ROLE_USER')]
    public function modificationSortie(Request $request, EntityManagerInterface $entityManager, Sortie $sortie): Response
    {

        $user = $this->getUser();
        if ($user !== $sortie->getOrganisateur()) {
            throw $this->createAccessDeniedException("Seul l'organisateur peut modifier cette sortie");
        }

        if ($sortie->getDateHeureDebut() <= new \DateTime()) {
            throw new \LogicException("Trop tard pour modifier");
        }


        $SortieForm = $this->createForm(SortieType::class, $sortie);
        $SortieForm->handleRequest($request);


        if ($SortieForm->isSubmitted()) {

            $nbParticipants = count($sortie->getParticipants());

            if ($nbParticipants >= $sortie->getNbInscriptionsMax()) {
                throw new \LogicException("Vous ne pouvez pas réduire le nombre de places");
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie modifiée !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sortie/modification.html.twig', [
            'sortie'=>$sortie,
            'sortieForm' => $SortieForm->createView()
        ]);
    }







    #[Route(path: ('/annulersortie/{id}'), name: 'app_annuler_sortie')]
    public function annulerSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        if ($user !== $sortie->getOrganisateur()) {
            throw $this->createAccessDeniedException("Seul l'organisateur peut annuler cette sortie");
        }

        if ($sortie->getDateHeureDebut() <= new \DateTime()) {
            throw new \LogicException("Trop tard pour annuler");
        }

        $form = $this->createFormBuilder()
            ->add('motifAnnulation', TextareaType::class, [
                'label' => 'Motif d\'annulation',
                'required' => true
            ])
            ->add('annuler', SubmitType::class, ['label' => 'Annuler la sortie'])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $data = $form->getData();
            $motifAnnulation = $data['motifAnnulation'];

            $etatAnnule = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'annulee']);
            $sortie->setEtat($etatAnnule);

            $sortie->setMotifAnnulation($motifAnnulation);


            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie est annulée.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('sortie/drop.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie
        ]);
    }
    #[Route(path: ('/annulersortie/{id}'), name:'app_annuler_sortie')]
    public function annulerSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();


            $this->addFlash('success', 'La sortie a bien été annulée');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('sortie/drop.html.twig', [
            'sortie'=> $sortie
        ]);

    }

    #[Route('/', name: 'app_home')]
    public function home(Request $request, SortieRepository $sortieRepository): Response
    {

        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);



        $sorties = $sortieRepository->findSearch($data);


        return $this->render('sortie/index.html.twig', [

            'sorties' => $sorties,
            'form' => $form->createView()
        ]);
    }

    #[Route('/detail/{id}', name: 'app_detail')]
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie=$sortieRepository->find($id);

        return $this->render('sortie/detail.html.twig', [
            "sortie"=>$sortie
        ]);
    }


    #[Route('/inscriptionSortie/{id}', name: 'app_inscriptionSortie')]
    public function inscriptionSortie($id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository)
    {

        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Vous devez être connecté pour vous inscrire à une sortie.'], 401);
        }

        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            return new JsonResponse(['success' => false, 'message' => 'Sortie non trouvée.'], 404);
        }

        if ($sortie->getEtat()->getLibelle() !== 'ouverte') {
            return new JsonResponse(['success' => false, 'message' => 'La sortie n\'est pas ouverte aux inscriptions.'], 400);
        }

        if ($sortie->getParticipants()->contains($user)) {
            return new JsonResponse(['success' => false, 'message' => 'Vous êtes déjà inscrit à cette sortie.'], 400);
        }

        if ($sortie->getDateLimiteInscription() < new \DateTime()) {
            return new JsonResponse(['success' => false, 'message' => 'La date limite d\'inscription est déjà atteinte pour cette sortie.'], 400);
        }

        if ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) {
            return new JsonResponse(['success' => false, 'message' => 'Le nombre maximum de participants est atteint pour cette sortie.'], 400);
        }

        $sortie->addParticipant($user);
        $entityManager->persist($sortie);
        $entityManager->flush();


        return new JsonResponse(['success' => true, 'message' => 'Inscription réussie à la sortie.']);
    }


    #[Route('/desistementSortie/{id}', name: 'app_desistementSortie')]
    #[IsGranted('ROLE_USER')]
    public function desistementSortieAction($id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
        $user= $this->getUser();
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        $currentDateTime = new \DateTime();
        if ($user && $sortie->getParticipants()->contains($user) && $sortie->getDateHeureDebut() > $currentDateTime) {
            $sortie->removeParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }


        return new JsonResponse(['success' => true, 'message' => 'Désistement réussi']);

    }



    #[Route('/publicationSortie/{id}', name: 'app_publicationSortie')]
    #[IsGranted('ROLE_USER')]
    public function publicationSortieAction($id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
        $user= $this->getUser();
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        if ($user !== $sortie->getOrganisateur()) {
            throw new AccessDeniedException("Seul l'organisateur de la sortie est en droit de la publier");
        }

        $etatOuvert = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'ouverte']);
        if (!$etatOuvert) {
            throw new \RuntimeException("L'état 'ouvert' n'a pas été trouvé.");
        }


        $sortie->setEtat($etatOuvert);
        $entityManager->persist($sortie);
        $entityManager->flush();


        return new JsonResponse(['success' => true, 'message' => 'Sortie publiée']);
    }



}


