<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Unit\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Imatic\Bundle\FormBundle\Form\Extension\EntityExtension;
use Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject\ImaticFormBundle\Log\ArrayLogger;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

class EntityTypeTest extends TypeTestCase
{
    private $registry;

    protected function setUp()
    {
        $qb = $this->createMock(QueryBuilder::class);

        $cm = new ClassMetadataInfo('Class');
        $cm->identifier = ['id'];

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository
            ->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $em = $this->createMock(EntityManagerInterface::class);
        $em
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($entityRepository));
        $em
            ->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($cm));

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($em);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionShouldBeThrownWhenChoiceValueIsNotSpecified()
    {
        $logger = new ArrayLogger();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions(\array_merge(
                $this->getTypeExtensions(),
                [new EntityExtension($logger, true)]
            ))
            ->addTypes($this->getTypes())
            ->addTypeGuessers($this->getTypeGuessers())
            ->getFormFactory();

        $this->factory->create(EntityType::class, null, [
            'class' => 'Class',
            'choices' => [],
        ]);
    }

    public function testErrorShouldBeLoggedWhenChoiceValueIsNotSpecified()
    {
        $logger = new ArrayLogger();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions(\array_merge(
                $this->getTypeExtensions(),
                [new EntityExtension($logger, false)]
            ))
            ->addTypes($this->getTypes())
            ->addTypeGuessers($this->getTypeGuessers())
            ->getFormFactory();

        $this->factory->create(EntityType::class, null, [
            'class' => 'Class',
            'choices' => [],
        ]);

        $this->assertEquals(
            [
                [
                    'level' => 'error',
                    'message' => 'Form type `Symfony\Bridge\Doctrine\Form\Type\EntityType` does not specify `choice_value` option which can lead to unmanaged entity exception in case `UnitOfWork` state is cleared before constructing the form.',
                    'context' => [],
                ],
            ],
            $logger->getBuffer()
        );
    }

    public function testNoErrorWhenChoiceValueIsSpecified()
    {
        $logger = new ArrayLogger();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions(\array_merge(
                $this->getTypeExtensions(),
                [new EntityExtension($logger, true)]
            ))
            ->addTypes($this->getTypes())
            ->addTypeGuessers($this->getTypeGuessers())
            ->getFormFactory();

        $this->factory->create(EntityType::class, null, [
            'class' => 'Class',
            'choices' => [],
            'choice_value' => 'id',
        ]);

        $this->assertEquals(
            [],
            $logger->getBuffer()
        );
    }

    protected function getExtensions()
    {
        return \array_merge(
            parent::getExtensions(),
            [
                new DoctrineOrmExtension($this->registry),
            ]
        );
    }
}
