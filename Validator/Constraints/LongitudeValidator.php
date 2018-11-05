<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LongitudeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!\is_numeric($value) || -180 > $value || 180 < $value) {
            $this->context->addViolation($constraint->message, ['%value%' => $value]);
        }
    }
}
