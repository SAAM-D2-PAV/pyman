<?php

namespace App\Form;

use App\Entity\ProjectCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjectSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('q', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Par titre de projet'
                ]
        ])
        ->add('category', EntityType::class,[
            'label' => false,
            'placeholder' => '-- Aucune --',
            'class' => ProjectCategory::class,
            'choice_label' => function(ProjectCategory $projectCategory){

                return $projectCategory->getName();
            }
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'required' => false,
            'mapped' => false
        ]);
    }
}
