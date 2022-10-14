<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('located', ChoiceType::class, [
                'label' => 'Site',
                
                'choices'  => [
                    '...' => 'NC',
                    'MENJS - GRENELLE' => 'MENJS - GRENELLE',
                    'MENJS - DUTOT' => 'MENJS - DUTOT',
                    'MENJS - REGNAULT' => 'MENJS - REGNAULT',
                    'MENJS - AVENUE DE FRANCE' => 'MENJS - AVENUE DE FRANCE',
                    'MESRI - DESCARTES' => 'MESRI - DESCARTES',
                    'MESRI - MIRABEAU' => 'MESRI - MIRABEAU',
                    'SORBONNE' => 'SORBONNE',
                    'COLLEGE DE FRANCE' => 'COLLEGE DE FRANCE',
                    'ÉCOLE / COLLÈGE / LYCÉE' => 'ÉCOLE / COLLÈGE / LYCÉE',
                    'AUTRE' => 'AUTRE'
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                
                'choices'  => [
                    '...' => 'NC',
                    'Bureau' => 'Bureau',
                    'Réunion - conférence' => 'Réunion - conférence',
                    'Réception' => 'Réception',
                    'Amphithéâtre' => 'Amphithéâtre',
                    'Studio' => 'Studio',
                    'Visioconférence' => 'Visioconférence',
                    'Réserve' => 'Réserve',
                    'Bibliothèque' => 'Bibliothèque',
                    'Cour - jardin' => 'Cour - jardin',
                    'Autre' => 'Autre'

                ],
            ])
            ->add('number', TextType::class, [
                'label' => 'Numéro'
            ])
            ->add('street', TextType::class,[
                'label' => 'Rue',
                'attr' => [
                    'placeholder' => '...'
                ]
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => '...'
                ]
            ])
            ->add('information',CKEditorType::class, [
                'label' => 'DESCRIPTION',
                'attr' => [
                    'placeholder' => 'Description de la tâche'
                ]
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'required' => false
        ]);
    }
}
