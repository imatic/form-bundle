<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Integration\Twig\Extension;

use Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject\WebTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class FormExtensionTest extends WebTestCase
{
    public function setUp(): void
    {
        static::createClient();
    }

    public function testImaticFormJavascriptWithJavascript()
    {
        $testingForm = $this->getFormFactory()->createBuilder(
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            null,
            ['csrf_protection' => false]
        )
            ->add('testing_input')
            ->getForm();

        $content = $this->getTwig()->render(
            '@AppImaticForm/Form/testing.html.twig',
            ['form' => $testingForm->createView()]
        );

        $this->assertEquals(
            <<<'EOF'

<form name="form" method="post"><div id="form"><input name="testing"></div></form>
<script>console.log('testing input js');</script>

EOF
            ,
            $content
        );
    }

    /**
     * @return \Twig_Environment
     */
    private function getTwig()
    {
        return self::$container->get('twig');
    }

    /**
     * @return FormFactoryInterface
     */
    private function getFormFactory()
    {
        return self::$container->get('form.factory');
    }
}
