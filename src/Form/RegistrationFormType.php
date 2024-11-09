<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Regex;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('agreeTerms', CheckboxType::class, [
        'label' => 'Accepter les termes et <a href="/rgpd" target="_blank">conditions</a>',
        'label_html' => true,  // Permet le HTML dans le label
        'mapped' => false,
        'constraints' => [
            new IsTrue([
                'message' => 'Vous devez accepter les termes.',
            ]),
        ],
    ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
                        'Votre mot de passe doit contenir au moins une minuscule'
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
            ->add('nom', TextareaType::class, [
                'label' => 'Nom',
                'attr' => [
                    'rows' => 1
                ],])
            -> add('date_naiss', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text', // Option pour un champ de saisie unique (calendrier)
                'format' => 'yyyy-MM-dd', // Format de la date

            ])
            -> add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'rows' => 1
                ],])
            -> add('code_postal', TextareaType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'rows' => 1
                ],])
            ->add('ville', TextareaType::class, [
                'label' => 'Ville de résidence',
                'attr' => [
                    'rows' => 1
                ],])
            ->add('prenom', TextareaType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'rows' => 1
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
