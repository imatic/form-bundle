<?php

namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Empty entity to null transformer
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class EmptyEntityToNullTransformer implements DataTransformerInterface
{
    /** @var array */
    private $properties;
    /** @var bool */
    private $strict;

    /**
     * Constructor
     *
     * @param array $properties array of property names
     * @param bool  $strict     consider only NULLs empty (not empty strings)
     */
    public function __construct(array $properties, $strict = false)
    {
        $this->properties = $properties;
        $this->strict = $strict;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (is_object($value)) {
            $hasNonEmptyValue = false;
            foreach ($this->properties as $property) {
                $reflProperty = new \ReflectionProperty($value, $property);
                $reflProperty->setAccessible(true);
                $reflPropertyValue = $reflProperty->getValue($value);

                if (
                    null !== $reflPropertyValue
                    && ($this->strict || '' !== $reflPropertyValue)
                ) {
                    $hasNonEmptyValue = true;
                    break;
                }
            }

            if ($hasNonEmptyValue) {
                return $value;
            } else {
                return null;
            }
        } else {
            return $value;
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        return $value;
    }
}
