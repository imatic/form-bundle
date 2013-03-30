<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeType extends AbstractType
{
    protected $translator;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'start',
            $options['field_type'],
            array_merge(array('required' => false), $options['field_options'])
        );

        $builder->add(
            'end',
            $options['field_type'],
            array_merge(array('required' => false), $options['field_options'])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'imatic_type_date_range';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'field_type' => 'date',
                'field_options' => array()
            )
        );
    }
}
