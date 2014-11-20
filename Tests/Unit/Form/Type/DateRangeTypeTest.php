<?php

namespace Imatic\Bundle\FormBundle\Tests\Unit\Form\Type;

use Imatic\Bundle\FormBundle\Form\Type\DateRangeType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class DateRangeTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'start' => [
                'year' => 2014,
                'month' => 3,
                'day' => 5,
            ],
            'end' => [
                'year' => 2014,
                'month' => 05,
                'day' => 10,
            ],
        ];

        $type = new DateRangeType();
        $form = $this->factory->create($type);
        $form->submit($formData);

        $this->assertTrue($form->has('start'));
        $this->assertTrue($form->has('end'));
        $this->assertEquals('2014-03-05 00:00:00', $form->get('start')->getData()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-05-10 00:00:00', $form->get('end')->getData()->format('Y-m-d H:i:s'));
    }
}
