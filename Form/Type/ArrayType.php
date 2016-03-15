<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Imatic\Bundle\FormBundle\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ArrayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ArrayToStringTransformer());
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'imatic_type_array';
    }
}
