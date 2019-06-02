<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Models\AccountPropertiesModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Defines the form used to change User properties
 *
 * Class AccountPropertiesType
 * @package App\Form
 */
class AccountPropertiesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $model   = $options['data'] ?? null;
        assert($model instanceof AccountPropertiesModel);
        $isEdit = $model && $model->getTheme();

        $builder
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new UserPassword(),
                ],
                'help'  => 'Your password',
                'label' => 'Your password',
            ])
            ->add('theme', ChoiceType::class, [
                'expanded'    => false,
                'constraints' => new NotNull([
                    'message' => 'Please choose the theme',
                ]),
                'choices' => User::THEMES,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
        ;

        $nicknameConstraints = [
            new NotNull([
                'message' => 'Please choose the nickname',
            ])
        ];


        if(!$isEdit || !$model->getNickname()) {
            $nicknameConstraints[] = new NotNull([
                'message' => 'Please upload an icon',
            ]);
        }

        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];


        if(!$isEdit || !$model->getTheme()) {
            $imageConstraints[] = new NotNull([
                'message' => 'Please upload an icon',
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
            ->add('nickname', TextType::class, [
                'constraints' => $nicknameConstraints,
                'help'  => 'Your nickname',
                'label' => 'Your nickname',
            ])
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccountPropertiesModel::class,
        ]);
    }
}
