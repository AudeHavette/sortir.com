<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/create", name="event_create")
     */
    public function createAction(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été créée avec succès!');
            return $this->redirectToRoute('sortie_list');

        }

        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //...

    /**
     * @Route("/show/{id}", name="event_show")
     */
    public function showAction(int $id): Response
    {
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie demandée n\'existe pas.');
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

//...

    /**
     * @Route("/register/{id}", name="event_register")
     */
    public function registerAction(int $id): Response
    {
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie demandée n\'existe pas.');
        }

        $user = $this->getUser();
        if ($user) {
            $sortie->addParticipant($user); // méthode addParticipant() dans l'entité Sortie
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes inscrit à la sortie.');
        } else {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire à une sortie.');
        }

        return $this->redirectToRoute('event_show', ['id' => $id]);
    }

    //...

    /**
     * @Route("/unregister/{id}", name="event_unregister")
     */
    public function unregisterAction(int $id): Response
    {
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie demandée n\'existe pas.');
        }

        $user = $this->getUser();
        if ($user && $sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($user); // méthode removeParticipant() dans l'entité Sortie
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes désinscrit de la sortie.');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas vous désinscrire de cette sortie ou pas inscrit.');
        }

        return $this->redirectToRoute('event_show', ['id' => $id]);
    }
    //...

    /**
     * @Route("/edit/{id}", name="event_edit")
     */
    public function editAction(Request $request, int $id): Response
    {
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie à modifier n\'existe pas.');
        }

        // Vérif si l'utilisateur actuel est l'organisateur de la sortie
        $this->denyAccessUnlessGranted('edit', $sortie);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été modifiée avec succès.');
            return $this->redirectToRoute('event_show', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }
    //...

    /**
     * @Route("/cancel/{id}", name="event_cancel")
     */
    public function cancelAction(Request $request, int $id): Response
    {
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie à annuler n\'existe pas.');
        }

        // Vérif si l'utilisateur actuel est l'organisateur ou un admin
        $this->denyAccessUnlessGranted('cancel', $sortie);

        // Annuler la sortie (mettre à jour l'état de la sortie, etc.)
        // $sortie->setState($cancelledState); // état annulé défini quelque part

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'La sortie a été annulée avec succès.');
        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/close/{id}", name="event_close_registration")
     */
    public function closeRegistrationAction(int $id): Response
    {
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie à clôturer n\'existe pas.');
        }

        // Vérif si l'utilisateur actuel est l'organisateur ou un admin
        $this->denyAccessUnlessGranted('close', $sortie);

        // Clôturer les inscriptions
        // $sortie->setIsRegistrationClosed(true); //  méthode pour fermer les inscriptions

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Les inscriptions sont maintenant fermées pour cette sortie.');
        return $this->redirectToRoute('event_show', ['id' => $id]);
    }
}



