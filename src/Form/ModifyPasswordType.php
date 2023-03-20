<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifyPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('oldPassword', PasswordType::class, [
            'label'=> 'Password',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please enter a password',
                ])
            ]
        ])
        ->add('newPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            // 'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Assert\Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least 8 characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 20,
                    'maxMessage' => 'Your password should be no more than 20 characters',
                ]),
                new Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", "Password doit comprend 1 majuscule, 1 minuscule, 1 chiffre")
            ],
            'label' => "Nouveau Password"
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Modifier Password',
            // 'attr' => ['id' => 'inscription']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
