<?php

namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Choice extension.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class ChoiceExtension extends AbstractTypeExtension
{
    /** @var array */
    protected $select2Config;

    public function __construct($select2Config)
    {
        $this->select2Config = $select2Config;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['rich']) {
            $view->vars['select2_options'] = [
                'placeholder' => $options['placeholder'] ?: null,
                'multiple' => $options['multiple'],
                'allowClear' => $options['multiple'] ? false : !$options['required'],
            ] + $this->select2Config;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'rich' => function (Options $options) {
                return !$options['expanded']
                    ? true
                    : false
                ;
            },
            'template' => function (Options $options) {
                return $options['rich']
                    ? 'ImaticFormBundle:Form:choice.html.twig'
                    : null
                ;
            },
        ]);
    }

    public function getExtendedType()
    {
        return ChoiceType::class;
    }
}
