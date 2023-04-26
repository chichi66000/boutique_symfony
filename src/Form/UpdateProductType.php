<?php

namespace App\Form;

use App\Entity\Product;
// use App\Repository\ColorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UpdateProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        

        $builder
            // ->add('reference')
            // ->add('category', TextType::class)
            ->add('title',TextType::class, [
                'label'=> 'Nom',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'attr' => ['class' => 'input_insert']
            ] )
            ->add('description', TextareaType::class, [
                'label'=> 'Description',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'attr' => [
                    'class' => 'input_insert',
                    'rows' => 4,
            ],
            ])
            
            ->add('price', NumberType::class, [
                'label'=> 'Prix',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'attr' => ['class' => 'input_insert']
            ])
            ->add('stock', NumberType::class, [
                'label'=> 'Stock',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'attr' => ['class' => 'input_insert']
            ])
            ->add('color', null, [
                'label'=> 'Color',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'attr' => ['class' => 'input_insert']
            ]
             )
            ->add('size', null, [
                'label'=> 'Size',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'attr' => ['class' => 'input_insert']
            ]
            )
            ->add('audience', null, [
                // 'choices' => [
                //     '1' => 'Homme',
                //     '2' => 'Femme',
                //     '3' => 'Autre',
                // ],
                // 'expanded' => true,
                // 'multiple' => false,
                'required' => true,
                'label'=> 'Civil',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
            ])
            ->add('photo1', FileType::class, [
                'label'=> 'Photo1',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'required' => false,
                'attr' => ['class' => 'input_insert'],
                'data_class' => null,
            ])
            ->add('photo2', FileType::class, [
                'label'=> 'Photo2',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'required' => false,
                'attr' => ['class' => 'input_insert'],
                'data_class' => null,
            ])
            ->add('photo3', FileType::class, [
                'label'=> 'Photo3',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'required' => false,
                'attr' => ['class' => 'input_insert'],
                'data_class' => null,
            ])
            ->add('photo4', FileType::class, [
                'label'=> 'Photo4',
                'label_attr' => [
                    'class' => 'label_insert'
                ],
                'required' => false,
                'attr' => ['class' => 'input_insert'],
                'data_class' => null,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier Produit',
                'attr' => ['id' => 'submit_insert'],
            ])
            // ->add('creation_date')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'color_choices' => [], // option personnalisée pour les couleurs
        ]);
    }
}
