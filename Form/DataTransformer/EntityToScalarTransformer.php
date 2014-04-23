<?php

namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\Proxy;

/**
 * Transforms entity instance to a scalar value
 *
 * @author Pavel Batecko <pavel.batecko at imatic.cz>
 */
class EntityToScalarTransformer implements DataTransformerInterface
{
    /** @var EntityManager */
    protected $em;
    /** @var string */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    public function transform($entity)
    {
        // handle value
        if (!is_object($entity)) {
            if (null === $entity) {
                return '';
            } else {
                throw new UnexpectedTypeException($entity, 'object or null');
            }
        }

        // fetch class metadata
        $metadata = $this->em->getClassMetadata($this->class);

        // force loading for proxies
        if ($entity instanceof Proxy) {
            $entity->__load();
        }

        return (string) current($metadata->getIdentifierValues($entity));
    }

    public function reverseTransform($value)
    {
        if (null !== $value && '' !== $value) {
            return $this->em->find($this->class, $value);
        }
    }
}
