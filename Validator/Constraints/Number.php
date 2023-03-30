<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 *
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class Number extends Constraint
{
    public $precisionMessage = 'The number cannot have bigger precision than "%maxPrecision%"';
    public $scaleMessage = 'The number cannot have bigger scale than "%maxScale%"';
    public $precision;
    public $scale;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($this->precision === null && $this->scale === null) {
            throw new MissingOptionsException(\sprintf('Either option "precision" or "scale" must be given for constraint %s', __CLASS__));
        }
    }
}
