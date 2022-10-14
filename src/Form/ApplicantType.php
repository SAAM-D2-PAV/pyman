<?php

namespace App\Form;

use App\Entity\Applicant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ApplicantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('lastname',TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Mail',
                'attr' => [
                    'placeholder' => '...',
                    'class' => 'errorMail'
                    ]
            ])
            ->add('phone',TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('job',TextType::class, [
                'label' => 'Fonction',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('office',TextType::class, [
                'label' => 'Service',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('department',TextType::class, [
                'label' => 'Département',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Applicant::class,
            'required' => false
        ]);
    }
}
