<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Type;

use Imatic\Bundle\FormBundle\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Ajax choice type.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class AjaxChoiceType extends AbstractType
{
    /** @var array */
    protected $select2Config;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    public function __construct(array $select2Config, UrlGeneratorInterface $urlGenerator)
    {
        $this->select2Config = $select2Config;
        $this->urlGenerator = $urlGenerator;
    }

    public function getBlockPrefix()
    {
        return 'imatic_ajax_choice';
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
            'select2_options' => [
                'placeholder' => $options['placeholder'],
                'multiple' => $options['multiple'],
                'allowClear' => $options['allow_clear'],
            ] + $this->select2Config,
        ];

        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
        }
    }

    public function configureOptions(OptionsResolver $resolver)
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
            'template' => '@ImaticForm/Form/ajax_choice.html.twig',
        ]);
    }

    /**
     * @param mixed $formValue
     * @param array $options
     *
     * @return array
     */
    protected function getInitialValue($formValue, array $options)
    {
        if ($options['multiple']) {
            // array
            if (!\is_array($formValue)) {
                throw new UnexpectedTypeException($formValue, 'array');
            }

            $initialValue = [];
            foreach ($formValue as $item) {
                $initialValue[] = [
                    'id' => $options['id_provider']($item),
                    'text' => $options['text_provider']($item),
                ];
            }
        } else {
            // single value
            if (!\is_scalar($formValue)) {
                throw new UnexpectedTypeException($formValue, 'scalar');
            }

            $initialValue = [
                'id' => $options['id_provider']($formValue),
                'text' => $options['text_provider']($formValue),
            ];
        }

        return $initialValue;
    }
}
