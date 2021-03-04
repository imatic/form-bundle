<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Unit\Validator\Constraints;

use Imatic\Bundle\FormBundle\Validator\Constraints\Number;
use Imatic\Bundle\FormBundle\Validator\Constraints\NumberValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class NumberValidatorTest extends TestCase
{
    /**
     * @var MockObject|ExecutionContextInterface
     */
    private $executionContext;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
    }

    /**
     * @dataProvider precision3Scale1ValidValues
     */
    public function testValidatorShouldNotAddViolationWhenValuesAreValid($validValue)
    {
        $this->executionContext
            ->expects($this->never())
            ->method('addViolation');

        $validator = new NumberValidator();
        $validator->initialize($this->executionContext);

        $validator->validate($validValue, new Number([
            'precision' => 3,
            'scale' => 1,
        ]));
    }

    public function precision3Scale1ValidValues()
    {
        return [
            [32.5],
            [0],
            [1.2],
            [0.3],
        ];
    }

    public function testValidatorShouldAddViolationWithMessageAboutInvalidPrecisionIfPrecisionIsInvalid()
    {
        $this->executionContext
            ->expects($this->once())
            ->method('addViolation')
            ->with('The number cannot have bigger precision than "%maxPrecision%"', [
                '%maxPrecision%' => 3,
            ]);

        $validator = new NumberValidator();
        $validator->initialize($this->executionContext);

        $validator->validate(325.5, new Number([
            'precision' => 3,
            'scale' => 1,
        ]));
    }

    public function testValidatorShouldAddViolationWithMessageAboutInvalidScaleIfScaleIsInvalid()
    {
        $this->executionContext
            ->expects($this->once())
            ->method('addViolation')
            ->with('The number cannot have bigger scale than "%maxScale%"', [
                '%maxScale%' => 1,
            ]);

        $validator = new NumberValidator();
        $validator->initialize($this->executionContext);

        $validator->validate(3.25, new Number([
            'precision' => 3,
            'scale' => 1,
        ]));
    }

    public function testValidatorShouldAddViolationWithMessageAboutInvalidScaleIfScaleIsInvalid2()
    {
        $this->executionContext
            ->expects($this->exactly(2))
            ->method('addViolation');
        $this->executionContext
            ->method('addViolation')
            ->withConsecutive(
                ['The number cannot have bigger precision than "%maxPrecision%"', ['%maxPrecision%' => 3]],
                ['The number cannot have bigger scale than "%maxScale%"', ['%maxScale%' => 1]]
            );

        $validator = new NumberValidator();
        $validator->initialize($this->executionContext);

        $validator->validate(33.25, new Number([
            'precision' => 3,
            'scale' => 1,
        ]));
    }
}
