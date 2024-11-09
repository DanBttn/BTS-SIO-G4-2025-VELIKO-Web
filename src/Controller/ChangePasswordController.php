<?php

namespace App\Controller;

use App\Form\PasswordChangeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChangePasswordController extends AbstractController
{
    #[Route('/change/password', name: 'app_change_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $form = $this->createForm(PasswordChangeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $currentPassword = $data['current_password'];
            $newPassword = $data['new_password'];
            $confirmPassword = $data['confirm_password'];

            // Vérifier que le mot de passe actuel est correct
            if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
                // Vérifier si le nouveau mot de passe et la confirmation correspondent
                if ($newPassword === $confirmPassword) {
                    // Hacher le nouveau mot de passe
                    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

                    // Mettre à jour l'utilisateur avec le nouveau mot de passe
                    $user->setPassword($hashedPassword);

                    // Sauvegarder l'utilisateur dans la base de données
                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Ajouter un message de succès
                    $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
                } else {
                    $this->addFlash('error', 'Le nouveau mot de passe et sa confirmation ne correspondent pas.');
                }
            } else {
                $this->addFlash('error', "L'ancien mot de passe est incorrect .");
            }
        }

        return $this->render('change_password/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}