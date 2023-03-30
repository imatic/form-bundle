<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\DataTransformer;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms entity instance to a scalar value.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class EntityToScalarTransformer implements DataTransformerInterface
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

    public function transform($entity)
    {
        if (!\is_object($entity)) {
            if (null === $entity) {
                return '';
            }
            throw new UnexpectedTypeException($entity, 'object or null');
        }

        return (string) \call_user_func($this->idProvider, $entity);
    }

    public function reverseTransform($value)
    {
        if (null !== $value && '' !== $value) {
            $qb = clone $this->qb;
            $qb
                ->andWhere(\current($qb->getRootAliases()) . ' = :EntityToScalarTransformer_Id')
                ->setParameter('EntityToScalarTransformer_Id', $value);

            return $qb->getQuery()->getOneOrNullResult();
        }
    }
}
