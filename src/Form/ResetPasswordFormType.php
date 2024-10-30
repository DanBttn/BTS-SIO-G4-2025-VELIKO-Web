<?php
// src/Form/ResetPasswordRequestFormType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('email', EmailType::class, [
'label' => 'Votre email',
'attr' => [
'class' => 'form-control',
'placeholder' => 'Entrez votre adresse e-mail',
],
'constraints' => [
new Assert\NotBlank(['message' => 'Veuillez entrer votre adresse e-mail']),
new Assert\Email(['message' => 'Veuillez entrer une adresse e-mail valide']),
],
]);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([]);
}
}
