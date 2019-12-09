<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject\ImaticFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder('app_imatic_form');
    }
}
