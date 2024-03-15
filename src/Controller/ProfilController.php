<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/monprofil/{id}', name: 'app_mon_profil')]
    public function modifProfil(Request $request, Utilisateur $user, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        //dd($form);

            if ($form->isSubmitted() && $form->isValid())
            {
                // cf doc Symfony https://symfony.com/doc/current/security/passwords.html#hashing-the-password
                $plainPassword = $form->get('motPasse')->getData();
                $newHaschedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setMotPasse($newHaschedPassword);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Votre profil a bien été modifié');

                return $this->redirectToRoute('app_home');
            } else {
                return $this->render('profil/index.html.twig', [
                    'formProfil' => $form
                ]);
            }

    }

    #[Route('/profil/{id}', name: 'app_profil')]
    function afficherProfil(Request $request, Utilisateur $user): Response
    {
        return $this->render('profil/show.html.twig', [
            'user' => $user
        ]);

    }
}