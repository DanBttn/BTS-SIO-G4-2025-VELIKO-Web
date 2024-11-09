<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is already logged in
        $user = $this->getUser();

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get the last entered username
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login form
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('/reset/password', name: 'app_reset_password_request')]
    public function request(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
    {


        $form = $this->createForm(ResetPasswordFormType::class);  // Utilisation du nouveau formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setConfirmationToken($token);

                // Persister les modifications
                $entityManager->persist($user);
                $entityManager->flush();

                // Envoi de l'e-mail
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], true);

                $emailMessage = (new Email())
                    ->from($_ENV['MAILER_FROM_ADDRESS'])
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de mot de passe')
                    ->html("<p>Pour réinitialiser votre mot de passe, cliquez sur ce lien : <a href='" . $_ENV['APP_URL'] . $resetUrl . "'>" . 'rénitialiser mon mot de passe' . "</a></p>");

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Un lien de réinitialisation de mot de passe a été envoyé à votre adresse e-mail.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'Cet email n\'existe pas dans notre base de données.');
        }

        return $this->render('reset_password/index.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset/password/{token}', name: 'app_reset_password')]
    public function reset(string $token, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('app_reset_password_request');
        }

        $form = $this->createForm(ResetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Vérifier si les mots de passe correspondent
            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            // Encode et met à jour le mot de passe
            $encodedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            $user->setPassword($encodedPassword);



            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');;

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }

}