<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject;

use Imatic\Testing\Test\TestKernel as BaseTestKernel;

class TestKernel extends BaseTestKernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        $parentBundles = parent::registerBundles();

        $bundles = [
            new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),

            new \Imatic\Bundle\FormBundle\ImaticFormBundle(),
            new \Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject\ImaticFormBundle\AppImaticFormBundle(),
        ];

        return \array_merge($parentBundles, $bundles);
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
