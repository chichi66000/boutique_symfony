<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ProfilUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('civilite',  ChoiceType::class, [
            'label' => 'Civile',
            'choices' => [
                'Homme' => 1,'Femme' => 2,'Autre' => 3
            ],
        ])
        ->add('pseudo',TextType::class, [
            'label' => 'Pseudo',
            // 'constraints' => [
            //     new Assert\Length(max: 50)
            // ],
            'required' => false]
        )
        ->add('tel', TextType::class, [
            'label' => 'Téléphone',
            // 'constraints' => [
            //     new Assert\Length(max: 15),
            //     new Assert\NotBlank(),
            //     new Assert\Regex("/^[0-9]+$/", "Ne contient que les chiffres")
            // ],
        ])
        ->add('first_name', TextType::class, [
            'label' => 'Nom',
            // 'constraints' => [
            //     new Assert\Length(max: 50),
            //     new Assert\NotBlank(),
            //     new Assert\Type("string"),
            //     new Assert\Regex("/^[a-zA-Z]+$/", "Ne contient que les lettres")
            // ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Prénom',
                // 'constraints' => [
                //     new Assert\Length(max: 50),
                //     new Assert\NotBlank(),
                //     new Assert\Type("string"),
                //     new Assert\Regex("/^[a-zA-Z]+$/", "Ne contient que les lettres")
                // ],
            ])
            ->add('email',  EmailType::class, [
                // 'constraints' => [
                //     new Assert\Email(),
                //     new Assert\NotNull()
                //     ]
            ])
            
            ->add('pc', TextType::class, [
                'label' => 'Code postale',
                // 'constraints' => [
                //     new Assert\Length(max: 20),
                //     new Assert\NotBlank()
                // ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                // 'constraints' => [
                //     new Assert\Length(max: 150),
                //     new Assert\NotBlank()
                // ],
            ])
            
            ->add('city', TextType::class, [
                'label' => 'Ville',
                // 'constraints' => [
                //     new Assert\Length(max: 50),
                //     new Assert\NotBlank()
                // ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier Profil',
                
                'attr' => ['id' => 'inscription']
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
