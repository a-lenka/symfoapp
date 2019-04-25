<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate User Entities
 *
 * Class UserType
 * @package App\Form
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'help'  => 'User email',
                'label' => 'User email',
            ])
            ->add('roles', ChoiceType::class, [
                'expanded' => false,
                'multiple' => true,
                'label'    => 'User roles',
                'choices'  => [
                    'Root'  => 'ROLE_ROOT',
                    'Admin' => 'ROLE_ADMIN',
                    'User'  => 'ROLE_USER'
                ],'choice_attr' => array(
                    'User' => [
                        'disabled' => true,
                    ],
                ),
            ])
            ->add('password', PasswordType::class, [
                'help'  => 'User password',
                'label' => 'User password',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
