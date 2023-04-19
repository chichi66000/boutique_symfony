<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdateProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('reference')
            // ->add('category', TextType::class)
            ->add('title')
            ->add('description')
            ->add('photo1')
            ->add('photo2')
            ->add('photo3')
            ->add('photo4')
            ->add('price')
            ->add('stock')
            ->add('color')
            ->add('size')
            ->add('audience')
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier Produit',
            ])
            // ->add('creation_date')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
