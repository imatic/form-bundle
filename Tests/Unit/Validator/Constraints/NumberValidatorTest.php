<?php

namespace Imatic\Bundle\FormBundle\Tests\Unit\Validator\Constraints;

use Imatic\Bundle\FormBundle\Validator\Constraints\NumberValidator;
use Imatic\Bundle\FormBundle\Validator\Constraints\Number;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class NumberValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $executionContext;

    protected function setUp()
    {
        $this->executionContext = $this->getMock('Symfony\Component\Validator\ExecutionContextInterface');
    }

    /**
     * @dataProvider precision3Scale1ValidValues
     */
    public function testValidatorShouldNotAddViolationWhenValuesAreValid($validValue)
    {
        $this->executionContext
            ->expects($this->never())
            ->method('addViolation')
        ;

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
            ])
        ;

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
            ])
        ;

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
            ->method('addViolation')
        ;
        $this->executionContext
            ->expects($this->at(0))
            ->method('addViolation')
            ->with('The number cannot have bigger precision than "%maxPrecision%"', [
                '%maxPrecision%' => 3,
            ])
        ;
        $this->executionContext
            ->expects($this->at(1))
            ->method('addViolation')
            ->with('The number cannot have bigger scale than "%maxScale%"', [
                '%maxScale%' => 1,
            ])
        ;

        $validator = new NumberValidator();
        $validator->initialize($this->executionContext);

        $validator->validate(33.25, new Number([
            'precision' => 3,
            'scale' => 1,
        ]));
    }
}
