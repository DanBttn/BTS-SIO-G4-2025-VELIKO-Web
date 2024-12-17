<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordChangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'Ancien mot de passe',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit contenir au moins  {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex(
                        '/[a-z]/',
                        'Votre mot de passe doit contenir au moins une minuscule.'
                    ),
                    new Regex(
                        '/[A-Z]/',
                        'Votre mot de passe doit contenir au moins une majuscule'),
                    new Regex(
                        '/[0-9]/',
                        'Votre mot de passe doit contenir au moins un chiffre'
                    ),
                    new Regex(
                        '/[^a-zA-Z0-9]/',
                        'Votre mot de passe doit contenir au moins un caractère spécial'
                    ),

                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmation nouveau mot de passe',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
