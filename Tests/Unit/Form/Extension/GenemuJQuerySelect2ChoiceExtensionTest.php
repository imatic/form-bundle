<?php

namespace Imatic\Bundle\FormBundle\Tests\Unit\Extension;

use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2Type;
use Imatic\Bundle\FormBundle\Form\Extension\GenemuJQuerySelect2ChoiceExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class GenemuJQuerySelect2ChoiceExtensionTest extends TypeTestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Genemu doesn\'t play well with symfony 3.');
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypes([
                new Select2Type('choice'),
            ])
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new GenemuJQuerySelect2ChoiceExtension())
            ->getFormFactory();
    }

    public function testAllowClearShouldNotBeTrueIfRequiredIsTrue()
    {
        $form = $this->factory->create('genemu_jqueryselect2_choice', null, [
            'required' => true,
        ]);

        $configs = $form->getConfig()->getOption('configs');
        $this->assertNotContains('allowClear', $configs);
    }

    public function testAllowClearShouldBeTrueIfRequiredIsFalse()
    {
        $form = $this->factory->create('genemu_jqueryselect2_choice', null, [
            'required' => false,
        ]);

        $configs = $form->getConfig()->getOption('configs');
        $this->assertContains('allowClear', $configs);
        $this->assertTrue($configs['allowClear']);
    }
}
