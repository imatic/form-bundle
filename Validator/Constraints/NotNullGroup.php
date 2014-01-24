<?php

namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Not null group constraint
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 *
 * @Annotation
 */
class NotNullGroup extends Constraint
{
    /** @var string */
    public $message = 'This value should not be null.';
    /** @var array */
    public $properties = array();

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('properties');
    }
}
