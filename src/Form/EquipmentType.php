<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Equipment;
use App\Entity\EquipmentCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\EquipmentType as EntityEquipmentType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;

class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom (champ obligatoire)',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('ref', TextType::class, [
                'label' => 'Référence (champ obligatoire)',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('serialNumber', TextType::class, [
                'label' => 'Numéro de série',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('identificationCode', TextType::class, [
                'label' => 'Code d\'identification MENESRI',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'État (champ obligatoire)',
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Très bon état' => 'Très bon état',
                    'Bon état' => 'Bon état',
                    'Satisfaisant' => 'Satisfaisant',
                    'Défectueux' => 'Défectueux'
                ],
                'placeholder' => '',
            ])
            ->add('specifications', UrlType::class, [
                'label' => 'Spécifications (lien PDF)',
                'attr' => [
                    'placeholder' => 'Présentation du matériel'
                ]
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'URL de l\'image du matériel'
            ])
            ->add('equipmentType', EntityType::class,[
                'label' => 'Type (champ obligatoire)',
                'placeholder' => '-- Choix du type --',
                'class' => EntityEquipmentType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                        
                },
                'choice_label' => function(EntityEquipmentType $equipmentType){

                    return ($equipmentType->getName());
                }
            ])
            ->add('location', EntityType::class,[
                'label' => 'Lieu de stockage ou d\'installation',
                'class' => Location::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.name', 'ASC');
                        
                },
                'choice_label' => 'name'
            ])

            ->add('equipmentCategories', EntityType::class,[
                'label' => 'Catégorie',
                'placeholder' => '-- Choix des catégories --',
                'class' => EquipmentCategory::class,
                'choice_label' => function(EquipmentCategory $equipmentCategory){

                    return $equipmentCategory->getName();
                },
                'multiple' => true,
                'expanded' => true
                
            ])
            ->add('note', TextType::class, [
                'label' => 'Commentaires',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('missing', ChoiceType::class, [
                'label' => 'Matériel manquant',
                
                'choices'  => [
                    'non' => 0,
                    'oui' => 1
                    
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
            'required' => false
        ]);
    }
}
