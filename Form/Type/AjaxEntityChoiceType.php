<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Imatic\Bundle\FormBundle\Form\DataTransformer\CollectionToScalarTransformer;
use Imatic\Bundle\FormBundle\Form\DataTransformer\EntityToScalarTransformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Ajax entity choice type.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class AjaxEntityChoiceType extends AjaxChoiceType
{
    /** @var ManagerRegistry */
    protected $registry;

    public function __construct(array $select2Config, UrlGeneratorInterface $urlGenerator, ManagerRegistry $registry)
    {
        parent::__construct($select2Config, $urlGenerator);

        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->registry->getManager(isset($options['entity_manager']) ? $options['entity_manager'] : null);

        $qb = $options['query_builder'];
        if ($qb instanceof \Closure) {
            $qb = $qb($em, $options['class']);
        }

        $builder->addViewTransformer(
            $options['multiple']
                ? new CollectionToScalarTransformer($qb, $options['id_provider'])
                : new EntityToScalarTransformer($qb, $options['id_provider']),
            true
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'class',
        ]);
        $resolver->setDefaults([
            'entity_manager' => null,
            'query_builder' => function (EntityManager $em, $class) {
                return $em
                    ->createQueryBuilder()
                    ->select('e')
                    ->from($class, 'e');
            },
            'id_provider' => function (Options $options) {
                $metadata = $this
                    ->registry
                    ->getManager($options['entity_manager'])
                    ->getClassMetadata($options['class']);

                return function ($entity) use ($metadata) {
                    $ids = $metadata->getIdentifierValues($entity);

                    return \current($ids);
                };
            },
        ]);
    }

    protected function getInitialValue($formValue, array $options)
    {
        if ($options['multiple']) {
            // collection
            if (!$formValue instanceof \Traversable && !\is_array($formValue)) {
                throw new UnexpectedTypeException($formValue, 'Traversable, array or null');
            }

            $initalValue = [];
            foreach ($formValue as $entity) {
                $initalValue[] = [
                    'id' => $options['id_provider']($entity),
                    'text' => $options['text_provider']($entity),
                ];
            }
        } else {
            // entity
            if (!\is_object($formValue)) {
                throw new UnexpectedTypeException($formValue, 'object or null');
            }

            $initalValue = [
                'id' => $options['id_provider']($formValue),
                'text' => $options['text_provider']($formValue),
            ];
        }

        return $initalValue;
    }
}
