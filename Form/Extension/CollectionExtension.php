<?php

namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'template' => function (Options $options, $default) {
                return $options['prototype']
                    ? 'ImaticFormBundle:Form:collection.html.twig'
                    : $default
                ;
            },
            'options' => function (Options $options, $default) {
                return $default + [
                    'label' => false,
                ];
            },
        ]);
    }

    public function getExtendedType()
    {
        return 'collection';
    }
}
