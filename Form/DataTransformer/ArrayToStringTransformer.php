<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (null === $value || !\is_array($value)) {
            return '';
        }

        return \implode(',', $value);
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ('' === $value) {
            return null;
        }
        if (\is_array($value)) {
            return $value;
        }

        return \explode(',', $value);
    }
}
