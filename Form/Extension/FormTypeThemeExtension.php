<?php

namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type theme extension
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class FormTypeThemeExtension extends AbstractTypeExtension
{
    /** @var \Twig_Environment */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['template'])) {
            $this->twig->getExtension('form')->renderer->setTheme(
                $view,
                (array) $options['template']
            );
        }

        $view->vars += $options['template_parameters'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'template' => null,
            'template_parameters' => [],
        ]);
        $resolver->setAllowedTypes([
            'template' => ['null', 'string', 'array'],
            'template_parameters' => 'array',
        ]);
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
