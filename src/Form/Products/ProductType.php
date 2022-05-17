<?php

namespace App\Form\Products;

use App\Entity\Products;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * Función para crear el formulario de añadir producto
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nombre del Producto *',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'floating-input form-control'
                ],
                'empty_data' => ''
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'label' => 'Categoría *',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'floating-input form-control'
                ],
                'empty_data' => '',
                // Obtenemos las categorías de la tabla correspondiente
                'class' => Categories::class,
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Añadir producto',
                'attr' => [
                    'class' => 'btn btn-primary mr-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}