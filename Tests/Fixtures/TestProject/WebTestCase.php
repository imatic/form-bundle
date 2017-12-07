<?php
namespace Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject;

use Imatic\Testing\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class WebTestCase extends BaseWebTestCase
{
    protected static function initData(KernelInterface $kernel)
    {
    }
}
