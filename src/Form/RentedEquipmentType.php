<?php

namespace App\Form;

use App\Entity\Applicant;
use App\Entity\RentedEquipment;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RentedEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',

            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',

            ])
            ->add('applicant', EntityType::class,[
                'label' => 'Demande faite par',
                'placeholder' => '',
                'multiple' => false,
                'class' => Applicant::class,

                //     'query_builder' => function (ApplicantRepository $er) {
                //         return $er->createQueryBuilder('a');
                //    },
                'choice_label' => function(Applicant $applicant){

                    return $applicant->getFirstname().' '.$applicant->getLastname().' | '.$applicant->getDepartment();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.firstname', 'ASC');
                }

            ])
            ->add('comment', CKEditorType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'placeholder' => '...'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RentedEquipment::class,
        ]);
    }
}
