<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\EquipmentCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EquipmentCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Audio'
                    ]
            ])
            ->add('information', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Présentation de la catégorie'
                ]
            ])
            /* ->add('equipment', EntityType::class,[
                'label' => 'Matériel',
                'placeholder' => '-- Ajouter du matériel --',
                'class' => Equipment::class,
                'choice_label' => function(Equipment $equipment){

                    return $equipment->getName();
                },
                'multiple' => true,
                'expanded' => true
            ]) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquipmentCategory::class,
            'required' => false
        ]);
    }
}
