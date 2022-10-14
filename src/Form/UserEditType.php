<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;

class UserEditType extends AbstractType
{
    private $currentUserRoles;

    public function __construct(Security $security) {

        $this->currentUserRoles = $security->getUser()->getRoles();
       
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname',TextType::class, [
            'label' => 'Nom',
            'attr' => [
                'placeholder' => ''
                ]
            ])
            ->add('firstname',TextType::class, [
            'label' => 'Prénom',
            'attr' => [
                'placeholder' => ''
                ]
            ])
             ->add('email', EmailType::class,[
            'label' => 'Mail',
            'attr' => [
                'placeholder' => ''
                ]
            ])
            ->add('phone',TextType::class, [
                'label' => 'Téléphone fixe',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('mobil',TextType::class, [
                'label' => 'téléphone portable',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('job',TextType::class, [
                'label' => 'Fonction',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('office',TextType::class, [
                'label' => 'Service / pôle',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
            ->add('department',TextType::class, [
                'label' => 'Direction / département',
                'attr' => [
                    'placeholder' => ''
                    ]
            ])
           
           
        ;
        //SEULMENT SI ROLE ADMIN
        foreach ($this->currentUserRoles as $role)

        if ($role == "ROLE_ADMIN") {
            $builder 
                ->add('roles', ChoiceType::class, [
                    'choices'  => [
                    'Non autorisé' => 'ROLE_NOTALLOW',
                    'Utilisateur' => 'ROLE_USER',
                    'Observateur' => 'ROLE_VIEWER',
                    'Propriétaire de tâches' => 'ROLE_OWNER',
                    'Editeur' => 'ROLE_EDITOR',
                    'Administrateur' => 'ROLE_ADMIN'
                    ],
                    'multiple' => true,
                    'expanded' => true
            ]);
        }
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false
        ]);
    }
}
