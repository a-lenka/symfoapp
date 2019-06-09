<?php

namespace App\Form\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the custom form field type
 * @see https://symfony.com/doc/current/cookbook/form/create_custom_field_type.html
 *
 * Class DateTimePickerType
 * @package App\Form\Fields
 */
class DateTimePickerType extends AbstractType
{
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options - From the parent DateTime type
     */
    final public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Only for old browsers
        $data = $form->getData();

        if($data) {
            $view->vars['value'] = $data->format('Y-m-d\TH:i:s');
        }
    }


    /**
     * @param OptionsResolver $resolver
     */
    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'html5'       => false,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ]);
    }


    /**
     * @return null|string
     */
    final public function getParent(): ?string
    {
        return DateTimeType::class;
    }
}
