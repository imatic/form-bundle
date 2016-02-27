<?php

namespace Imatic\Bundle\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Genemu\Bundle\FormBundle\DependencyInjection\Configuration as GenemuConfiguration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Genemu config pass
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class GenemuConfigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $genemuConfig = (new Processor())->processConfiguration(
            new GenemuConfiguration(),
            $container->getExtensionConfig('genemu_form')
        );

        $container->setParameter('imatic_form.genemu_config', $genemuConfig);
    }
}
