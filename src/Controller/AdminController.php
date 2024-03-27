<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\AdminModifType;
use App\Form\AdminType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_inscription')]
    public function inscription(Request $request, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $utilisateur=new Utilisateur();
        $utilisateur->setActif(true);
        $utilisateur->setRoles(['ROLE_USER']);
        $adminForm=$this->createForm(AdminType::class, $utilisateur);
        $adminForm->handleRequest($request);

        if($adminForm->isSubmitted() && $adminForm->isValid()){
            // encode the plain password
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $adminForm->get('motPasse')->getData()
                )
            );

            $entityManager->persist($utilisateur);
            $entityManager->flush();


            $this->addFlash('success', 'Utilisateur ajouté !');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('admin/inscription.html.twig', [
            'adminForm' => $adminForm->createView()
        ]);
    }


    #[Route('/admin/liste', name: 'admin_liste')]
    public function listeUtilisateurs (UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs=$utilisateurRepository->findAll();

        return $this->render('admin/liste.html.twig', [
            "utilisateurs"=>$utilisateurs
        ]);
    }

    #[Route('/admin/liste/{id}', name: 'admin_detail')]
    public function detailUtilisateur (int $id, UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateur=$utilisateurRepository->find($id);

        return $this->render('admin/detail.html.twig', [
            "utilisateur"=>$utilisateur
        ]);
    }
    #[Route('/admin/edit/{id}', name: 'admin_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Utilisateur $utilisateur): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        $form = $this->createForm(AdminModifType::class, $utilisateur, ['isAdmin' => $isAdmin]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($isAdmin) {

                $formData = $form->getData();

                $originalValues = [
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                    'telephone' => $utilisateur->getTelephone(),
                    'email' => $utilisateur->getEmail(),
                    'motPasse'=>$utilisateur->getMotPasse(),
                    'pseudo'=>$utilisateur->getPseudo()
                ];


                $formData->setNom($originalValues['nom']);
                $formData->setPrenom($originalValues['prenom']);
                $formData->setTelephone($originalValues['telephone']);
                $formData->setEmail($originalValues['email']);
                $formData->setMotPasse($originalValues['motPasse']);
                $formData->setPseudo($originalValues['pseudo']);


                $utilisateur->setNom($formData->getNom());
                $utilisateur->setPrenom($formData->getPrenom());
                $utilisateur->setTelephone($formData->getTelephone());
                $utilisateur->setEmail($formData->getEmail());
                $utilisateur->setMotPasse($formData->getMotPasse());
                $utilisateur->setPseudo($formData->getPseudo());

            }


            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', ' Profil utilisateur modifié !');
            return $this->redirectToRoute('admin_liste');
        }

        return $this->render('admin/modification.html.twig', [
            'adminModifForm' => $form->createView(),
        ]);
    }



   
    #[Route('/admin/delete/{id}', name: 'admin_delete')]
    public function delete(EntityManagerInterface $entityManager, Utilisateur $user, Sortie $sortie): Response
    {
        $sortiesOrganisateur = $entityManager->getRepository(Sortie::class)->findBy(['organisateur' => $user]);

        foreach ($sortiesOrganisateur as $sortie) {
            $entityManager->remove($sortie);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        //dd($user);
        $this->addFlash('success', 'Utilisateur supprimé ainsi que ses sorties !');
        return $this->redirectToRoute('admin_liste');
    }

}
