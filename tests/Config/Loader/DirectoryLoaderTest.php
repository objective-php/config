<?php

    namespace Tests\ObjectivePHP\Config\Loader;


    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Config\Exception;
    use ObjectivePHP\Config\Loader\DirectoryLoader;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Collection\Collection;

    class DirectoryLoaderTest extends TestCase
    {


        public function testLoadingConfigFromNonExistingLocationFailsWithAnException()
        {
            $this->expectsException(function() use(&$location)
            {
                $loader = new DirectoryLoader();
                $loader->load($location = uniqid(uniqid()));
            }, Exception::class, $location, Exception::INVALID_LOCATION);
        }

        public function testConfigTreeLoading()
        {
            $configLoader = new DirectoryLoader();

            $config = $configLoader->load(__DIR__ . '/config');

            $this->assertEquals($this->getExpectedConfig()->toArray(), $config->toArray());

        }

        protected function getExpectedConfig()
        {

            return new Config([

                'app.version' => '1.0',
                'app.env'     => 'prod',
                'package.token' => 'token',
                'packages.loaded' => new Collection(['pre', 'sub']),
                'package.pre.version' => '0.1b',
                'add.key' => 'added',
                'sub.key' => 'test'

            ]);

        }

    }