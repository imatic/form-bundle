<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Unit\Form\Type;

use Imatic\Bundle\FormBundle\Form\Type\RangeType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class RangeTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'start' => 5,
            'end' => 3,
        ];

        $form = $this->factory->create(RangeType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEquals($formData, $form->getData());
    }
}
