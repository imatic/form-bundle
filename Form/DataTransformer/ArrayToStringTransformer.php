<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        if (null === $array || !\is_array($array)) {
            return '';
        }

        return \implode(',', $array);
    }

    public function reverseTransform($string)
    {
        if ('' === $string) {
            return null;
        }
        if (\is_array($string)) {
            return $string;
        }

        return \explode(',', $string);
    }
}
