<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Location;
use App\Entity\Equipment;
use App\Entity\TaskCategory;
use App\Form\TaskEquipmentType;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;

class TaskType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('project',  EntityType::class,[
                'label' => 'Projet lié à cette tâche',
                'placeholder' => '-- Choix du projet --',
                'class' => Project::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')->where('p.status = :status1')->orWhere('p.status = :status2')
                        ->orderBy('p.name', 'ASC')
                        ->setParameters(['status1' => 'A faire',
                            'status2' => 'En cours']);
                },
                'choice_label' => 'name',
                'autocomplete' => true,
                'multiple' => false
            ])
            ->add('name', TextType::class, [
                'label' => 'INTITULÉ',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'DESCRIPTION',
                'attr' => [
                    'placeholder' => 'Description de la tâche'
                ]
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
               
                
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                
            ])
            ->add('startHour', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
            ])
            ->add('endHour', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
            ])
            
            ->add('status', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'A faire' => 'A faire',
                    'En cours' => 'En cours',
                    'Faite' => 'Faite',
                    'Annulée' => 'Annulée'
                ],
                'placeholder' => '',
            ])
            ->add('category', EntityType::class,[
                'label' => 'Catégorie',
                'placeholder' => '-- Choix de la catégorie --',
                'class' => TaskCategory::class,
                'choice_label' => function(TaskCategory $taskCategory){

                    return ($taskCategory->getName());
                }
            ])
            ->add('stream', CheckboxType::class,[
                'label' => 'Retransmission en direct (Youtube, Facebook, ...)'
            ])
            ->add('location',  EntityType::class,[
                'label' => 'Lieu',
                'placeholder' => '-- Choix du lieu --',
                'class' => Location::class,
                'choice_label' => function(Location $location){

                    return ($location->getName());
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.name', 'ASC');
                },
                'autocomplete' => true,
            ])
           ->add('owners', EntityType::class,[
            'label' => ' ',
            
            'class' => User::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')->andWhere('u.taskOwner = :status')->setParameter('status', 1)->orderBy('u.firstname', 'ASC');
            },
            'choice_label' => 'firstname',
            'multiple' => true,
            'expanded' => true
            
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $formEvent){
                $postData = $formEvent->getData();
                
                
               
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $formEvent){
                $preSubmit = $formEvent->getData();
              
            })
            
        ;
        
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'required' => false
        ]);
    }
}
