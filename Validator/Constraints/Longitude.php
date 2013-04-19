<?php

namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Longitude extends Constraint
{
    public $message = 'Invalid value for longitude';
}
