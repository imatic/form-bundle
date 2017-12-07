<?php
namespace Imatic\Bundle\FormBundle\Tests\Integration\Twig\Extension;

use Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FormExtensionTest extends WebTestCase
{
    /** @var ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = static::createClient()->getContainer();
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
            'AppImaticFormBundle:Form:testing.html.twig',
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
        return $this->container->get('twig');
    }

    /**
     * @return FormFactoryInterface
     */
    private function getFormFactory()
    {
        return $this->container->get('form.factory');
    }
}
