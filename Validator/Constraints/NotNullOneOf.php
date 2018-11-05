<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Not null one of constraint.
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
    public $properties = [];

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getRequiredOptions()
    {
        return ['properties'];
    }
}
