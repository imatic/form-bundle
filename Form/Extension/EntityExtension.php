<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityExtension extends AbstractTypeExtension
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // specify `choice_value` option prevent unmanaged entity exception in case `UnitOfWork` state which is cleared before constructing the form
            'choice_value' => function ($entity) {
                if (\is_object($entity)) {
                    $metadata = $this->manager->getClassMetadata(\get_class($entity));

                    if (1 === \count($metadata->getIdentifierFieldNames())) {
                        return (string) \current($metadata->getIdentifierValues($entity));
                    }
                }

                return '';
            },
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [EntityType::class];
    }
}
