<?php

namespace App\Form;

use App\Entity\Task;
use App\Form\Fields\DateTimePickerType;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate Task Entities
 *
 * Class TaskType
 * @package App\Form
 */
class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Task name',
                'help'  => 'What to do',
            ])
            ->add('dateDeadline', DateTimePickerType::class, [
                'label'  => 'Deadline date',
                'help'   => 'When to do',
                'date_label' => 'Date',
                'time_label' => 'Time',
                'data' => (new DateTime('now'))->modify('+1 day'),
            ])
            ->add('state', ChoiceType::class, [
                'expanded' => false,
                'label'    => 'State',
                'choices'  => [
                    'In progress' => 'In progress',
                    'Done'        => 'Done',
                ],
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
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
