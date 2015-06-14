<?php

namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Not null group validator
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class NotNullGroupValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $nullProperties = [];
        foreach ($constraint->properties as $property) {
            $reflProperty = new \ReflectionProperty($value, $property);
            $reflProperty->setAccessible(true);

            if (null === $reflProperty->getValue($value)) {
                $nullProperties[] = $property;
            }
        }

        $nullPropertyCount = sizeof($nullProperties);
        if ($nullPropertyCount > 0 && $nullPropertyCount !== sizeof($constraint->properties)) {
            for ($i = 0; $i < $nullPropertyCount; ++$i) {
                $this->context->addViolationAt($nullProperties[$i], $constraint->message);
            }
        }
    }
}
