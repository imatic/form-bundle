<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Collection;
use Imatic\Bundle\FormBundle\Form\DataTransformer\EntityToScalarTransformer;
use Imatic\Bundle\FormBundle\Form\DataTransformer\CollectionToScalarTransformer;

/**
 * Ajax entity choice type
 * 
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class AjaxEntityChoiceType extends AbstractType
{
    /** @var Registry */
    protected $registry;
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    public function __construct(Registry $registry, UrlGeneratorInterface $urlGenerator)
    {
        $this->registry = $registry;
        $this->urlGenerator = $urlGenerator;
    }

    public function getName()
    {
        return 'imatic_type_ajax_entity_choice';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->registry->getManager(isset($options['entity_manager']) ? $options['entity_manager'] : null);

        if ($options['multiple']) {
            $builder->addViewTransformer(
                new CollectionToScalarTransformer(
                    $em,
                    $options['class']
                )
            );
        } else {
            $builder->addViewTransformer(
                new EntityToScalarTransformer(
                    $em,
                    $options['class']
                )
            );
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // check options
        if ($options['multiple'] && $options['allow_clear']) {
            throw new \RuntimeException('The "allow_clear" option has no effect in multiple choice mode');
        }

        // set vars
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['allow_clear'] = $options['allow_clear'];
        $view->vars['ajax_path'] = $this->urlGenerator->generate(
            $options['route'],
            $options['route_attrs']
        );

        // set initial value
        $formValue = $form->getData();
        if (null !== $formValue) {
            $em = $this->registry->getManager(isset($options['entity_manager']) ? $options['entity_manager'] : null);
            $metadata = $em->getClassMetadata($options['class']);

            if ($options['multiple']) {
                // collection
                if (!($formValue instanceof Collection)) {
                    throw new UnexpectedTypeException($formValue, 'Doctrine\Common\Collections\Collection or null');
                }

                $view->vars['initial_value'] = array();
                foreach ($formValue as $entity) {
                    $view->vars['initial_value'][] = array(
                        'id' => current($metadata->getIdentifierValues($entity)),
                        'text' => isset($options['property'])
                            ? $metadata->getFieldValue($entity, $options['property'])
                            : (string) $entity,
                    );
                }
            } else {
                // entity
                if (!is_object($formValue)) {
                    throw new UnexpectedTypeException($formValue, 'object or null');
                }

                $view->vars['initial_value'] = array(
                    'id' => current($metadata->getIdentifierValues($formValue)),
                    'text' => isset($options['property'])
                        ? $metadata->getFieldValue($formValue, $options['property'])
                        : (string) $formValue,
                );
            }
        } else {
            $view->vars['initial_value'] = null;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => null,
            'property' => null,
            'placeholder' => null,
            'multiple' => false,
            'allow_clear' => function (Options $options) {
                return !$options->get('required');
            },
            'route' => null,
            'route_attrs' => array(),
            'compound' => false,
            'template' => 'ImaticFormBundle:Form:ajax_entity_choice.html.twig',
        ));
    }

}
