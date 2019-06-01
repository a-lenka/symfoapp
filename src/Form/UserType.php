<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Models\UserTypeModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

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
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user   = $options['data'] ?? null;
        assert($user instanceof User);
        $isEdit = $user && $user->getEmail();

        $builder
            ->add('email', EmailType::class, [
                'help'  => 'User email',
                'label' => 'User email',
            ])
            ->add('roles', ChoiceType::class, [
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'label'    => 'User roles',
                'data'     => ['ROLE_USER'],
                'choices'  => [
                    'Root'  => 'ROLE_ROOT',
                    'Admin' => 'ROLE_ADMIN',
                    'User'  => 'ROLE_USER'
                ],'choice_attr' => [
                    'User' => [
                        'disabled' => true,
                    ],
                ],
            ])
            ->add('password', PasswordType::class, [
                'help'  => 'User password',
                'label' => 'User password',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
        ;

        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];


        if(!$isEdit || !$user->getAvatar()) {
            $imageConstraints[] = new NotNull([
                'message' => 'Please upload an image',
            ]);
        }


        $builder
            ->add('avatar', FileType::class, [
                'help'  => 'User avatar',
                'label' => 'User avatar',
                'mapped'   => false,
                'required' => false,
                'constraints' => $imageConstraints
            ])
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserTypeModel::class,
        ]);
    }
}
