<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRendererInterface;
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
    /** @var FormRendererInterface */
    private $renderer;
    /** @var string|string[]|null */
    private $defaultTheme;

    public function __construct(FormRendererInterface $renderer, $defaultTheme = null)
    {
        $this->renderer = $renderer;
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
            $this->renderer->setTheme($view, $theme);
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

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
