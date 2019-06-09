<?php

namespace App\Form;

use App\Form\Models\RegistrationModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Form is used to register the new Users
 *
 * Class RegistrationType
 * @package App\Form
 */
class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'help' => 'This email you will use to log in and recover your password',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'help'  => 'Enter your password, please',
                ],
                'second_options' => [
                    'label' => 'Repeat password',
                    'help'  => 'Repeat your password, please',
                ],
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'mapped'      => false,
                'constraints' => new IsTrue(),
                'label'       => 'Terms accepted',
            ])
            ->add('avatar', FileType::class, [
                'help'  => 'User avatar',
                'label' => 'User avatar',
                'mapped'   => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M'
                    ]),
                    new NotNull([
                        'message' => 'Please upload an image',
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register'
            ])
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => RegistrationModel::class,
        ));
    }
}
