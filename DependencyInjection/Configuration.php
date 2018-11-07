<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('imatic_form');

        $rootNode
            ->children()
                ->variableNode('default_theme')->defaultValue('bootstrap_3_horizontal_layout.html.twig')->end()
                ->arrayNode('select2')
                    ->addDefaultsIfNotSet()
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->scalarNode('theme')->defaultValue('bootstrap')->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
