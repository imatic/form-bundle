<?php

namespace Imatic\Bundle\FormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Imatic\Bundle\FormBundle\DependencyInjection\Compiler\FormPass;

class ImaticFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormPass());
    }
}
