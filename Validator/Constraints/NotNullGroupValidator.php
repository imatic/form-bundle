<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Not null group validator.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class NotNullGroupValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        $nullProperties = [];
        foreach ($constraint->properties as $property) {
            $reflProperty = new \ReflectionProperty($value, $property);
            if (null === $reflProperty->getValue($value)) {
                $nullProperties[] = $property;
            }
        }

        $nullPropertyCount = \count($nullProperties);
        if ($nullPropertyCount > 0 && $nullPropertyCount !== \count($constraint->properties)) {
            for ($i = 0; $i < $nullPropertyCount; ++$i) {
                $this->context->addViolationAt($nullProperties[$i], $constraint->message);
            }
        }
    }
}
