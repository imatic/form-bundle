<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'start',
            $options['field_type'],
            array_merge(['required' => false], $options['field_options'])
        );

        $builder->add(
            'end',
            $options['field_type'],
            array_merge(['required' => false], $options['field_options'])
        );
    }

    
    public function getName()
    {
        return 'imatic_type_date_range';
    }

    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'field_type' => 'date',
                'field_options' => []
            ]
        );
    }
}
