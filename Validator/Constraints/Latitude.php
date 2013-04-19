<?php

namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Latitude extends Constraint
{
    public $message = 'Invalid value for latitude';
}
