<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Unit\Form\Type;

use Imatic\Bundle\FormBundle\Form\Type\DateTimeRangeType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class DateTimeRangeTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'start' => [
                'date' => [
                    'year' => 2014,
                    'month' => 3,
                    'day' => 5,
                ],
                'time' => [
                    'hour' => 13,
                    'minute' => 15,
                ],
            ],
            'end' => [
                'date' => [
                    'year' => 2014,
                    'month' => 05,
                    'day' => 10,
                ],
                'time' => [
                    'hour' => 5,
                    'minute' => 59,
                ],
            ],
        ];

        $form = $this->factory->create(DateTimeRangeType::class, null, ['field_options' => ['widget' => 'text']]);
        $form->submit($formData);

        $this->assertTrue($form->isValid(), (string) $form->getErrors(true));
        $this->assertTrue($form->has('start'));
        $this->assertTrue($form->has('end'));
        $this->assertEquals('2014-03-05 13:15:00', $form->get('start')->getData()->format('Y-m-d H:i:s'));
        $this->assertEquals('2014-05-10 05:59:00', $form->get('end')->getData()->format('Y-m-d H:i:s'));
    }
}
