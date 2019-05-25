<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
class AccountPropertiesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user   = $options['data'] ?? null;
        assert($user instanceof User);
        $isEdit = $user && $user->getId();

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
        ;

        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];

        $themeConstraints = [];


        if (!$isEdit || !$user->getAvatar()) {
            $themeConstraints[] = new NotNull([
                'message' => 'Please upload an image',
            ]);
        }


        if (!$isEdit || !$user->getTheme()) {
            $imageConstraints[] = new NotNull();
        }


        $builder
            ->add('avatar', FileType::class, [
                'help'  => 'User avatar',
                'label' => 'User avatar',
                'mapped'   => false,
                'required' => false,
                'constraints' => $imageConstraints
            ])
            ->add('theme', ChoiceType::class, [
                'expanded'    => false,
                'constraints' => $themeConstraints,
                'choices'  => [
                    'Default' => 'red lighten-2',
                    'Purple'  => 'purple lighten-2',
                    'Indigo'  => 'indigo lighten-2',
                    'Black'   => 'black',
                ],
            ])
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
