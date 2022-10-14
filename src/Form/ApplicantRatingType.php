<?php

namespace App\Form;

use App\Entity\ProjectRateByApplicant;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicantRatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('note', ChoiceType::class, [
                'label' => 'La prestation réalisée correspond-elle aux résultats attendus ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                    'En partie' => 'En partie'
                ],
                'placeholder' => '',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaires',
                'attr' => [
                    'placeholder' => '...',
                    'rows' => '5'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectRateByApplicant::class,
            'required' => false
        ]);
    }
}
