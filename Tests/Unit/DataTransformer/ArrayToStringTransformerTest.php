<?php

namespace Imatic\Bundle\DataBundle\Tests\Unit\DataTransformer;

use Imatic\Bundle\FormBundle\Form\DataTransformer\ArrayToStringTransformer;
use PHPUnit_Framework_TestCase;

class ArrayToStringTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider reverseTransformDataProvider
     */
    public function testReverseTransform($value, $expectedValue)
    {
        $transformer = new ArrayToStringTransformer();
        $this->assertEquals($expectedValue, $transformer->reverseTransform($value));
    }

    public function reverseTransformDataProvider()
    {
        return [
            [
                null,
                [],
            ],
            [
                '',
                [],
            ],
            [
                '0',
                [0],
            ],
            [
                0,
                [0],
            ],
            [
                '1,2,some text',
                [1, '2', 'some text'],
            ],
        ];
    }

    /**
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, $expectedValue)
    {
        $transformer = new ArrayToStringTransformer();
        $this->assertEquals($expectedValue,$transformer->transform($value));
    }

    public function transformDataProvider()
    {
        return [
            [
                null,
                '',
            ],
            [
                [],
                '',
            ],
            [
                [1, ' 2 ', ' some text '],
                '1,2,some text',
            ],
        ];
    }
}
