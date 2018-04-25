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
use ObjectivePHP\Config\Loader\FileLoader\FileLoader;
use ObjectivePHP\Config\Loader\FileLoader\JsonFileLoaderAdapter;
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
        $loader->registerAdapter($jsonProcessor = new JsonFileLoaderAdapter());

        $this->assertAttributeContains($jsonProcessor, 'adapters', $loader);
    }

    public function testLoadingFile()
    {
        $loader = new FileLoader();
        $loader->registerAdapter($jsonProcessor = new JsonFileLoaderAdapter());

        $config = (new Config())->registerDirective(new ScalarDirective())->registerDirective(new ScalarDirective(null, 'x'))->registerDirective(new ScalarDirective(null, 'y'));

        $params = $loader->load(__DIR__ . '/params/params.json');

        $config->hydrate($params);

        $this->assertEquals('scalar value from json', $config->get('scalar'));

    }

    public function testLoadingFolder()
    {
        $loader = new FileLoader();
        $config = (new Config())->registerDirective(new ScalarDirective())->registerDirective(new ScalarDirective(null,
            'x'))->registerDirective(new ScalarDirective(null, 'y'))
        ;


        $params = $loader->load(__DIR__ . '/params');

        $config->hydrate($params);

        $this->assertEquals('overridden local value', $config->get('scalar'));
        $this->assertEquals('a', $config->get('y'));
        $config->set('x', 'b');
        $this->assertEquals('b', $config->get('y'));
    }

}
