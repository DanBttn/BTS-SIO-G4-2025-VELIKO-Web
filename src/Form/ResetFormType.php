<?php
// src/Form/ResetPasswordFormType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResetFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('plainPassword', PasswordType::class, [
'label' => 'Nouveau mot de passe',
'attr' => [
'class' => 'form-control',
'placeholder' => 'Entrez votre nouveau mot de passe',
],
'constraints' => [
new Assert\NotBlank(['message' => 'Le mot de passe est requis']),
new Assert\Length([
'min' => 12,
'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
]),
],
])
->add('confirmPassword', PasswordType::class, [
'label' => 'Confirmez le mot de passe',
'attr' => [
'class' => 'form-control',
'placeholder' => 'Confirmez votre nouveau mot de passe',
],
'constraints' => [
new Assert\NotBlank(['message' => 'Veuillez confirmer votre mot de passe']),
],
'mapped' => false, // Ne pas mapper ce champ à l'entité User
]);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([]);
}
}
