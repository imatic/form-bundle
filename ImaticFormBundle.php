<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle;

use Imatic\Bundle\FormBundle\DependencyInjection\Compiler\FormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ImaticFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormPass());
    }
}
