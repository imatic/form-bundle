<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class TimeRangeType extends AbstractType
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
        return 'imatic_type_time_range';
    }

    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'field_type' => 'time',
                'field_options' => []
            ]
        );
    }
}
