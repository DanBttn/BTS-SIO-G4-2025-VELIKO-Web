<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\veliko\GenerateToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegistrationController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws LoaderError
     * @throws RuntimeError
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Environment $twig
    ): Response {
        // Si l'utilisateur est déjà connecté, rediriger vers la page de connexion
        if ($security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe en clair
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Générer un token de confirmation unique
            $token = (new GenerateToken())->create();
            $user->setConfirmationToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            // Créer le lien de confirmation
            $confirmationLink = $this->generateUrl('app_confirm_email', ['token' => $token], 0);

            // Rendre le contenu de l'email depuis le template Twig
            $emailContent = $twig->render('registration/confirmation_email.html.twig', [
                'user' => $user,
                'confirmation_link' => $confirmationLink,
            ]);

            // Envoi de l'email de confirmation
            try {
                $email = (new Email())
                    ->from($_ENV['MAILER_FROM_ADDRESS'])
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre email')
                    ->html($emailContent);

                $mailer->send($email);
            } catch (TransportExceptionInterface $exception) {
                // Gérer l'erreur d'envoi
                dd($exception);
            }

            // Message d'information pour l'utilisateur
            $this->addFlash('info', 'Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/confirm_email/{token}', name: 'app_confirm_email')]
    public function confirmEmail(
        string $token,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        // Rechercher l'utilisateur par le token de confirmation
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if ($user) {
            // Confirmer l'email
            $user->setIsVerified(true);
            $entityManager->flush();

            // Connecter automatiquement l'utilisateur
            $security->login($user);

            // Rediriger vers la carte
            return $this->redirectToRoute('app_map');
        }

        return new Response('Token invalide ou expiré.', Response::HTTP_BAD_REQUEST);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/rgpd', name: 'app_rgpd')]
    public function index(): Response
    {
        return $this->render('registration/rgpd.html.twig', [
            'controller_name' => 'RGPDController',
        ]);
    }
}
