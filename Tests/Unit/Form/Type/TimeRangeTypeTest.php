<?php

namespace Imatic\Bundle\FormBundle\Tests\Unit\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Imatic\Bundle\FormBundle\Form\Type\TimeRangeType;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class TimeRangeTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'start' => [
                'hour' => 13,
                'minute' => 15,
            ],
            'end' => [
                'hour' => 5,
                'minute' => 59,
            ],
        ];

        $type = new TimeRangeType();
        $form = $this->factory->create($type);
        $form->submit($formData);

        $this->assertTrue($form->has('start'));
        $this->assertTrue($form->has('end'));
        $this->assertEquals('1970-01-01 13:15:00', $form->get('start')->getData()->format('Y-m-d H:i:s'));
        $this->assertEquals('1970-01-01 05:59:00', $form->get('end')->getData()->format('Y-m-d H:i:s'));
    }
}
