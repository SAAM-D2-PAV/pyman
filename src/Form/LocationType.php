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
                    '...' => '...',
                    '97 GRENELLE' => '97 GRENELLE',
                    '99 GRENELLE' => '99 GRENELLE',
                    '103 GRENELLE' => '103 GRENELLE',
                    '107 GRENELLE' => '107 GRENELLE',
                    '110 GRENELLE' => '110 GRENELLE',
                    'AVENUE DE FRANCE' => 'AVENUE DE FRANCE',
                    'DESCARTES' => 'DESCARTES',
                    'DUTOT' => 'DUTOT',
                    'MIRABEAU' => 'MIRABEAU',
                    'REGNAULT' => 'REGNAULT',
                    '----' => '----',
                    'ÉCOLE / COLLÈGE / LYCÉE' => 'ÉCOLE / COLLÈGE / LYCÉE',
                    'AUTRE' => 'AUTRE'
                ],
            ])
            ->add('ministry', ChoiceType::class, [
                'label' => 'Ministère de rattachement',
                'choices'  => [
                    '...' => '...',
                    'MENJ' => 'MENJ',
                    'MSJOP' => 'MSJOP',
                    'MESR' => 'MESR',
                    '----' => '----',
                    'LIEU EXTERNE' => 'LIEU EXTERNE',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                
                'choices'  => [
                    '...' => 'NC',
                    'Amphithéâtre' => 'Amphithéâtre',
                    'Bibliothèque' => 'Bibliothèque',
                    'Bureau' => 'Bureau',   
                    'Cour ou jardin' => 'Cour ou jardin',
                    'Réception' => 'Réception',
                    'Réserve' => 'Réserve',
                    'Réunion - conférence' => 'Réunion - conférence',
                    'Studio' => 'Studio',
                    'Visioconférence' => 'Visioconférence',
                    '----' => '----',
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
