<?php

namespace App\Form;

use App\Entity\Task;
use App\Form\Fields\DateTimePickerType;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

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
        $task   = $options['data'] ?? null;
        assert($task instanceof Task);
        $isEdit = $task && $task->getId();

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

        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];


        if (!$isEdit || !$task->getIcon()) {
            $imageConstraints[] = new NotNull([
                'message' => 'Please upload an icon',
            ]);
        }


        $builder
            ->add('icon', FileType::class, [
                'help'  => 'Task icon',
                'label' => 'Task icon',
                'mapped'   => false,
                'required' => false,
                'constraints' => $imageConstraints
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
