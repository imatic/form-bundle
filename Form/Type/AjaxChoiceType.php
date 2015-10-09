<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Genemu\Bundle\FormBundle\Form\JQuery\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Ajax choice type
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class AjaxChoiceType extends AbstractType
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getName()
    {
        return 'imatic_type_ajax_choice';
    }

    public function getParent()
    {
        return 'genemu_jqueryselect2_choice';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addViewTransformer(new ArrayToStringTransformer());
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['multiple'] && $options['allow_clear']) {
            throw new \RuntimeException('The "allow_clear" option has no effect in multiple choice mode');
        }

        $view->vars['configs'] = array_merge($view->vars['configs'], [
            'placeholder' => $options['placeholder'],
            'multiple' => $options['multiple'],
            'allow_clear' => $options['allow_clear'],
        ]);

        $view->vars['ajax_path'] = $this->urlGenerator->generate(
            $options['route'],
            $options['route_attrs']
        );

        $formValue = $form->getData();

        $view->vars['initial_value'] = null !== $formValue
            ? $this->getInitialValue($formValue, $options)
            : null
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'route',
        ]);
        $resolver->setDefaults([
            'text_provider' => null,
            'placeholder' => null,
            'multiple' => false,
            'allow_clear' => function (Options $options) {
                if (!$options['multiple']) {
                    return !$options['required'];
                }
            },
            'route_attrs' => [],
            'compound' => false,
            'template' => 'ImaticFormBundle:Form:ajax_choice.html.twig',
        ]);
    }

    /**
     * @param mixed $formValue
     * @param array $options
     */
    protected function getInitialValue($formValue, array $options)
    {
        if ($options['multiple']) {
            // array
            if (!is_array($formValue)) {
                throw new UnexpectedTypeException($formValue, 'array');
            }

            $initalValue = [];
            foreach ($formValue as $item) {
                $initalValue[] = [
                    'id' => $item,
                    'text' => $this->getText($item, $options),
                ];
            }
        } else {
            // single value
            if (!is_scalar($formValue)) {
                throw new UnexpectedTypeException($formValue, 'scalar');
            }

            $initalValue = [
                'id' => $formValue,
                'text' => $this->getText($formValue, $options),
            ];
        }

        return $initalValue;
    }

    /**
     * @param scalar $formValue
     * @param array  $options
     * @return string
     */
    protected function getText($formValue, array $options)
    {
        return isset($options['text_provider'])
            ? $options['text_provider']($formValue)
            : (string) $formValue
        ;
    }
}
