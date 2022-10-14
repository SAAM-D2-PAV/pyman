<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\EquipmentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TypeEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Caméra'
                    ]
            ])
            ->add('information', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Présentation du type'
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquipmentType::class,
            'required' => false
        ]);
    }
}
