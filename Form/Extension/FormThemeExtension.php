<?php
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Imatic\Bundle\FormBundle\Twig\Extension\FormExtension;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form theme extension.
 *
 * Allows configuring form theme through options.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class FormThemeExtension extends AbstractTypeExtension
{
    /** @var \Twig_Environment */
    private $twig;
    /** @var string|string[]|null */
    private $defaultTheme;

    public function __construct(\Twig_Environment $twig, $defaultTheme = null)
    {
        $this->twig = $twig;
        $this->defaultTheme = $defaultTheme;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $theme = $options['template'];

        // set default theme if no template was specified
        if (!$theme && $this->defaultTheme && !$view->parent) {
            $theme = $this->defaultTheme;
        }

        if ($theme) {
            $this->twig->getExtension(FormExtension::class)->getRenderer()->setTheme(
                $view,
                (array) $theme
            );
        }

        $view->vars += $options['template_parameters'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => null,
            'template_parameters' => [],
        ]);
        $resolver->setAllowedTypes('template', ['null', 'string', 'array']);
        $resolver->setAllowedTypes('template_parameters', 'array');
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}
