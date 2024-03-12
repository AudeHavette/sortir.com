<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Assurez-vous d'importer les classes et annotations nécessaires.

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile(): Response
    {
        // Récupération de l'utilisateur actuel, typiquement via le TokenStorage ou directement via le service de sécurité
        $user = $this->getUser();

        // Assurez-vous de vérifier que l'utilisateur est connecté
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Générer la vue en passant l'utilisateur au template
        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function edit(): Response
    {
        // Implémentez la logique pour éditer l'utilisateur.
        // Cela inclura probablement la création d'un formulaire et son traitement.

        // Similaire à la méthode profile, récupérez l'utilisateur et passez-le à un formulaire.
        // Puis sauvegardez les changements dans la base de données.

        return $this->render('user/edit.html.twig');
    }

    // Ajoutez d'autres méthodes selon les besoins, comme pour la suppression d'un compte, la mise à jour du mot de passe, etc.
}
