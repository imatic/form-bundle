<?php

namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Transforms collection to a scalar value
 *
 * @author Pavel Batecko <pavel.batecko at imatic.cz>
 */
class CollectionToScalarTransformer implements DataTransformerInterface
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

    public function transform($collection)
    {
        // handle value
        if (null === $collection) {
            return '';
        }
        if (!($collection instanceof Collection)) {
            throw new UnexpectedTypeException($collection, 'Doctrine\Common\Collections\Collection or null');
        }

        // fetch class metadata
        $metadata = $this->em->getClassMetadata($this->class);

        // fetch collection members
        $output = null;
        foreach ($collection as $entity) {

            if (null === $output) {
                $output = '';
            } else {
                $output .= ',';
            }

            // add id
            $output .= current($metadata->getIdentifierValues($entity));

        }

        // return
        return $output;
    }

    public function reverseTransform($value)
    {
        $collection = new ArrayCollection;
        if (null === $value || '' === $value) {
            return $collection;
        }
        $value = explode(',', $value);
        for ($i = 0; isset($value[$i]); ++$i) {
            $collection[] = $this->em->getReference($this->class, $value[$i]);
        }

        return $collection;
    }
}
