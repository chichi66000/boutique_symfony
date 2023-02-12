<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
// use Symfony\Component\Validator\Constraints\IsTrue;
// use Symfony\Component\Validator\Constraints\Length;
// use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civile',
                'choices' => [
                    'Homme' => 1,'Femme' => 2,'Autre' => 3
                ],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'constraints' => [
                    new Assert\Length(max: 50)
                ],
            ])
            ->add('tel',TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new Assert\Length(max: 15),
                    new Assert\NotBlank()
                ],
            ] )   
            ->add('first_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\Length(max: 50),
                    new Assert\NotBlank()
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\Length(max: 50),
                    new Assert\NotBlank()
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\Email(),
                    new Assert\NotNull()
                    ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
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
                    new Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/")
                ],
                'label' => "Password"
            ])
            ->add('pc',TextType::class, [
                'label' => 'Code postale',
                'constraints' => [
                    new Assert\Length(max: 20),
                    new Assert\NotBlank()
                ],
            ] )
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\Length(max: 150),
                    new Assert\NotBlank()
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new Assert\Length(max: 50),
                    new Assert\NotBlank()
                ],
            ])
            // ->add('creation_date',  HiddenType::class, [
            //     'label' => 'Creation at',
            //     'data' => (new \DateTime())->format('Y-m-d H:i:s'),
            // ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Inscription',
                // 'attr' => ['id' => 'inscription']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
