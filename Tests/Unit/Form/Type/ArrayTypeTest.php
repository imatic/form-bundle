<?php

namespace Imatic\Bundle\FormBundle\Tests\Unit\Form\Type;

use Imatic\Bundle\FormBundle\Form\Type\ArrayType;
use Symfony\Component\Form\Test\TypeTestCase;

class ArrayTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $form = $this->factory->create(new ArrayType());
        $form->submit('a, b,c ,  d   ');
        $this->assertEquals(['a', 'b', 'c', 'd'], $form->getData());
    }
}
