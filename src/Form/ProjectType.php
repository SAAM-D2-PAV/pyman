<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Applicant;
use App\Entity\ProjectCategory;
use Doctrine\ORM\EntityRepository;

use App\Repository\ApplicantRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'attr' => [
                    'placeholder' => '...'
                    ]
            ])
            ->add('information', CKEditorType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description du projet'
                ]
            ])
            ->add('deliveryDate', DateType::class, [
                'label' => 'Date de livraison',
                'widget' => 'single_text',
               
                
            ])
            ->add('url',UrlType::class,[
                'label' => 'Lien vers la vidéo produite'
            ])
           
            ->add('category', EntityType::class,[
                'label' => 'Catégorie',
                'placeholder' => '-- Choix de la catégorie --',
                'class' => ProjectCategory::class,
                'choice_label' => function(ProjectCategory $projectCategory){

                    return $projectCategory->getName();
                }
            ])
            ->add('requestBy', EntityType::class,[
                'label' => 'demande faite par',
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
                },
                'autocomplete' => true,

            ])
            ->add('status', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'A faire' => 'A faire',
                    'En cours' => 'En cours',
                    'Fait' => 'Fait',
                    'Annulé' => 'Annulé',
                    'Refusé' => 'Refusé'
                ],
                
            ])
            ->add('pub_video',TextType::class,[
                'label' => 'Iframe publique (url)'
            ])
            ->add('pub_presentation', CKEditorType::class, [
                'label' => 'Description publique (médiathèque)',
                'attr' => [
                    'placeholder' => 'Description de la vidéo'
                ]
            ])
            ->add('pub_video_status', ChoiceType::class, [
                'label' => 'Statut de la vidéo',
                'choices' => [
                    'Privée' => 0,
                    'Public' => 1
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'required' => false
        ]);
    }
}
