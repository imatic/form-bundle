<?php

namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Collection extension
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class CollectionExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($view->vars['prototype'])) {
            $view->vars['prototype_name'] = $options['prototype_name'];
            $view->vars['collection_button_style'] = $options['collection_button_style'];
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => function (Options $options, $default) {
                return $options['prototype']
                    ? 'ImaticFormBundle:Form:collection.html.twig'
                    : $default
                ;
            },
            'collection_button_style' => 'bootstrap-horizontal',
            'entry_options' => function (Options $options, $default) {
                return ($default ?: []) + [
                    'label' => false,
                ];
            },
        ]);
    }

    public function getExtendedType()
    {
        return CollectionType::class;
    }
}
