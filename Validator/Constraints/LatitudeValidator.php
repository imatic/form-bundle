<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LatitudeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        if (!\is_numeric($value) || -90 > $value || 90 < $value) {
            $this->context->addViolation($constraint->message, ['%value%' => $value]);
        }
    }
}
