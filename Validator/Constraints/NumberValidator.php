<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class NumberValidator extends ConstraintValidator
{
    /**
     * @param float  $value
     * @param Number $constraint
     *
     * @throws \Exception
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        $chunks = \explode('.', (string) $value);
        if (\count($chunks) > 2) {
            throw new \Exception('Number can have only 1 "." character.');
        }

        $precision = \strlen(\implode('', $chunks));
        $scale = isset($chunks[1]) ? \strlen($chunks[1]) : 0;

        if ($constraint->precision !== null && $precision > $constraint->precision) {
            $this->context->addViolation($constraint->precisionMessage, [
                '%maxPrecision%' => $constraint->precision,
            ]);
        }

        if ($constraint->scale !== null && $scale > $constraint->scale) {
            $this->context->addViolation($constraint->scaleMessage, [
                '%maxScale%' => $constraint->scale,
            ]);
        }
    }
}
