<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Not null one of validator.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class NotNullOneOfValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $hasNotNullValue = false;
        foreach ($constraint->properties as $property) {
            $reflProperty = new \ReflectionProperty($value, $property);
            $reflProperty->setAccessible(true);

            if (null !== $reflProperty->getValue($value)) {
                $hasNotNullValue = true;
                break;
            }
        }

        if (!$hasNotNullValue) {
            $this->context->addViolation($constraint->message);
        }
    }
}
