<?php

namespace Test\ObjectivePHP\Config\Loader;


use ObjectivePHP\Config\Exception\ConfigLoadingException;
use ObjectivePHP\Config\Loader\DirectoryLoader;
use ObjectivePHP\PHPUnit\TestCase;
use Tests\Helper\TestDirectives\SampleSingleValueDirective;
use Tests\Helper\TestDirectives\TestStackedValueDirective;

class DirectoryLoaderTest extends TestCase
{


    public function testLoadingConfigFromNonExistingLocationFailsWithAnException()
    {
        $this->expectsException(function () use (&$location) {
            $loader = new DirectoryLoader();
            $loader->load($location = uniqid(uniqid()));
        }, ConfigLoadingException::class, $location, ConfigLoadingException::INVALID_LOCATION);
    }

    public function testLoadingExtraConfigFromNonExistingLocationDoesNotFailWithAnException()
    {
        $loader = new DirectoryLoader();
        $config = $loader->loadExtra($location = uniqid(uniqid()));

        $this->assertEmpty($config->toArray());
    }

    public function testConfigTreeLoading()
    {
        $configLoader = new DirectoryLoader();

        $config = $configLoader->load(__DIR__ . '/config');
        $this->assertEquals($this->getExpectedConfig(), $config->toArray());

    }

    protected function getExpectedConfig()
    {

        return [
            TestStackedValueDirective::class => ['packageX', 'packageY'],
            SampleSingleValueDirective::class => 'local value'
        ];

    }

}
    

