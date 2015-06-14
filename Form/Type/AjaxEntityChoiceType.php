<?php

namespace Imatic\Bundle\FormBundle\Form\Type;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Collection;
use Imatic\Bundle\FormBundle\Form\DataTransformer\CollectionToScalarTransformer;
use Imatic\Bundle\FormBundle\Form\DataTransformer\EntityToScalarTransformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Ajax entity choice type
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class AjaxEntityChoiceType extends AjaxChoiceType
{
    /** @var Registry */
    protected $registry;

    public function __construct(UrlGeneratorInterface $urlGenerator, Registry $registry)
    {
        parent::__construct($urlGenerator);

        $this->registry = $registry;
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired([
            'class',
        ]);
    }

    protected function getInitialValue($formValue, array $options)
    {
        $em = $this->registry->getManager(isset($options['entity_manager']) ? $options['entity_manager'] : null);
        $metadata = $em->getClassMetadata($options['class']);

        if ($options['multiple']) {
            // collection
            if (!($formValue instanceof Collection)) {
                throw new UnexpectedTypeException($formValue, 'Doctrine\Common\Collections\Collection or null');
            }

            $initalValue = [];
            foreach ($formValue as $entity) {
                $initalValue[] = [
                    'id' => current($metadata->getIdentifierValues($entity)),
                    'text' => $this->getText($entity, $options),
                ];
            }
        } else {
            // entity
            if (!is_object($formValue)) {
                throw new UnexpectedTypeException($formValue, 'object or null');
            }

            $initalValue = [
                'id' => current($metadata->getIdentifierValues($formValue)),
                'text' => $this->getText($formValue, $options),
            ];
        }

        return $initalValue;
    }
}
