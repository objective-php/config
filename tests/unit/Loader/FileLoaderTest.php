<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:07
 */

namespace Tests\ObjectivePHP\Config\Loader;

use Codeception\Test\Unit;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Loader\FileLoader;
use ObjectivePHP\Config\Processor\JsonParamsProcessor;
use Tests\Helper\TestDirectives\ScalarDirective;

class FileLoaderTest extends Unit
{
    /**
     *
     *
     * @param $location
     *
     * @return ConfigInterface
     */
    public function testRegisteringProcessors()
    {
        $loader = new FileLoader();
        $loader->registerProcessor($jsonProcessor = new JsonParamsProcessor(), 'json');

        $this->assertAttributeContains($jsonProcessor, 'processors', $loader);
        $this->assertAttributeEquals(['json' => $jsonProcessor], 'processors', $loader);
    }

    public function testLoadingFile()
    {
        $loader = new FileLoader();
        $loader->registerProcessor($jsonProcessor = new JsonParamsProcessor(), 'json');

        $config = (new Config())->registerDirective(new ScalarDirective());

        $params = $loader->load(__DIR__ . '/params/params.json');

        $config->hydrate($params);

        $this->assertEquals('scalar value from json', $config->get('scalar'));
    }

    public function testLoadingFolder()
    {
        $loader = new FileLoader();
        $loader->registerProcessor($jsonProcessor = new JsonParamsProcessor(), 'json');

        $config = (new Config())->registerDirective(new ScalarDirective());

        $params = $loader->load(__DIR__ . '/params');

        $config->hydrate($params);

        $this->assertEquals('overridden local value', $config->get('scalar'));
    }

}