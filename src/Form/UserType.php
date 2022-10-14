<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname',TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('firstname',TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Mail',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe ne correspond pas.',
               
            
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
            ])
            ->add('phone',TextType::class, [
                'label' => 'Téléphone fixe',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('mobil',TextType::class, [
                'label' => 'téléphone portable',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('job',TextType::class, [
                'label' => 'Fonction',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('office',TextType::class, [
                'label' => 'Service / pôle',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('department',TextType::class, [
                'label' => 'Direction / département',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
     
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false
        ]);
    }
}
