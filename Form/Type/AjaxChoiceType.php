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
    /** @var array */
    protected $genemuConfig;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    public function __construct(array $genemuConfig, UrlGeneratorInterface $urlGenerator)
    {
        $this->genemuConfig = $genemuConfig;
        $this->urlGenerator = $urlGenerator;
    }

    public function getName()
    {
        return 'imatic_type_ajax_choice';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addViewTransformer(new ArrayToStringTransformer(), true);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['multiple'] && $options['allow_clear']) {
            throw new \RuntimeException('The "allow_clear" option has no effect in multiple choice mode');
        }

        $formValue = $form->getData();

        $view->vars += [
            'multiple' => $options['multiple'],
            'request_type' => $options['request_type'],
            'ajax_path' => $this->urlGenerator->generate(
                $options['route'],
                $options['route_attrs']
            ),
            'initial_value' => null !== $formValue
                ? $this->getInitialValue($formValue, $options)
                : null,
            'configs' => [
                'placeholder' => $options['placeholder'],
                'multiple' => $options['multiple'],
                'allowClear' => $options['allow_clear'],
            ],
        ];

        if (isset($this->genemuConfig['select2']['configs'])) {
            $view->vars['configs'] += $this->genemuConfig['select2']['configs'];
        }
        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'route',
        ]);
        $resolver->setDefaults([
            'id_provider' => function ($item) {
                return (string) $item;
            },
            'text_provider' => function ($item) {
                return (string) $item;
            },
            'placeholder' => null,
            'multiple' => false,
            'allow_clear' => function (Options $options) {
                return $options['multiple'] ? false : !$options['required'];
            },
            'route_attrs' => [],
            'request_type' => 'filter',
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
                    'id' => $options['id_provider']($item),
                    'text' => $options['text_provider']($item),
                ];
            }
        } else {
            // single value
            if (!is_scalar($formValue)) {
                throw new UnexpectedTypeException($formValue, 'scalar');
            }

            $initalValue = [
                'id' => $options['id_provider']($formValue),
                'text' => $options['text_provider']($formValue),
            ];
        }

        return $initalValue;
    }
}
