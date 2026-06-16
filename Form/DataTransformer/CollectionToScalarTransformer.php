<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms collection to a scalar value.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class CollectionToScalarTransformer implements DataTransformerInterface
{
    /** @var QueryBuilder */
    protected $qb;
    /** @var callable */
    protected $idProvider;

    public function __construct(QueryBuilder $qb, callable $idProvider)
    {
        $this->qb = $qb;
        $this->idProvider = $idProvider;
    }

    public function transform(mixed $value): mixed
    {
        // handle value
        if (null === $value) {
            return '';
        }
        if (!$value instanceof \Traversable && !\is_array($value)) {
            throw new UnexpectedTypeException($value, 'Traversable, array or null');
        }

        // fetch collection members
        $output = null;
        foreach ($value as $entity) {
            if (null === $output) {
                $output = '';
            } else {
                $output .= ',';
            }

            // add id
            $output .= \call_user_func($this->idProvider, $entity);
        }

        // return
        return $output;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (null === $value || '' === $value) {
            return new ArrayCollection();
        }
        if (!\is_array($value)) {
            $value = \explode(',', $value);
        }

        $qb = clone $this->qb;
        $qb
            ->andWhere(\current($qb->getRootAliases()) . ' IN(:CollectionToScalarTransformer_Ids)')
            ->setParameter('CollectionToScalarTransformer_Ids', $value);

        return new ArrayCollection($qb->getQuery()->execute());
    }
}
