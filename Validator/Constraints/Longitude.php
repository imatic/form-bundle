<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Longitude extends Constraint
{
    public $message = 'Invalid value for longitude';
}
