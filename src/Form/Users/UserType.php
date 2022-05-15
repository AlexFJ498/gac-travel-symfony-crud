<?php

namespace App\Form\Users;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * Función para crear el formulario de añadir usuario
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Nombre de Usuario *',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'floating-input form-control'
                ],
                'empty_data' => ''
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Contraseña *',
                'attr' => [
                    'placeholder' => '',
                    'class' => 'floating-input form-control'
                ],
                'empty_data' => ''
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Añadir usuario',
                'attr' => [
                    'class' => 'btn btn-primary mr-2'
                ]
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