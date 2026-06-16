<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class Number extends Constraint
{
    public function __construct(
        public readonly ?int $precision = null,
        public readonly ?int $scale = null,
        public readonly string $precisionMessage = 'The number cannot have bigger precision than "%maxPrecision%"',
        public readonly string $scaleMessage = 'The number cannot have bigger scale than "%maxScale%"',
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);

        if ($this->precision === null && $this->scale === null) {
            throw new MissingOptionsException(\sprintf('Either option "precision" or "scale" must be given for constraint %s', __CLASS__));
        }
    }
}
