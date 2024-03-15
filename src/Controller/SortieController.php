<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\FilterSortiesType;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\SearchData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SortieController extends AbstractController
{



    #[Route('/createSortie', name: 'app_createSortie')]
    #[IsGranted('ROLE_USER')]
    public function creationSortie (Request $request, EntityManagerInterface $entityManager): Response{
        $this->denyAccessUnlessGranted('ROLE_USER');
        $sortie= new Sortie();

        $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'creee']);
        if ($etatCree) {
            $sortie->setEtat($etatCree);
        } else {
            dump('L\'état "sortie créée" n\'a pas été trouvé.');
        }

      //  dump($etatCree);
      //  $sortie->setEtat($etatCree);
        $sortie->setOrganisateur($this->getUser());


        $SortieForm=$this->createForm(SortieType::class, $sortie);
        $SortieForm->handleRequest($request);

        if($SortieForm->isSubmitted()){
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie créée !');
            return $this->redirectToRoute('app_createSortie');
        }

        return $this->render('sortie/creation.html.twig',[
            'sortieForm'=> $SortieForm->createView()
        ]);
    }

    #[Route('/', name: 'app_home')]
    public function home(Request $request, SortieRepository $sortieRepository): Response
    {

        $data=new SearchData();
        $form=$this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);

        $sorties=$sortieRepository->findSearch($data);

        return $this->render('sortie/index.html.twig', [

            'sorties'=>$sorties,
            'form'=>$form->createView()
        ]) ;
    }


}


