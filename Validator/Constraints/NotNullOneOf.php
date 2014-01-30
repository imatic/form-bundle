<?php

namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Not null one of constraint
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 *
 * @Annotation
 */
class NotNullOneOf extends Constraint
{
    /** @var string */
    public $message = 'At least one item must be defined.';
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
