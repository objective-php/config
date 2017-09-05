<?php

    namespace Test\ObjectivePHP\Config\Loader;


    use ObjectivePHP\Config\Exception;
    use ObjectivePHP\Config\Loader\DirectoryLoader;
    use ObjectivePHP\PHPUnit\TestCase;
    use Test\ObjectivePHP\Config\TestStackedValuesDirective;
    use TestDirectives\TestDirective;
    use Tests\Helper\TestDirectives\SampleSingleValueDirective;
    use Tests\Helper\TestDirectives\TestSingleValueDirectiveGroup;

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
                TestSingleValueDirectiveGroup::class . '.version' => '1.0',
                TestSingleValueDirectiveGroup::class . '.env'     => 'local',
                TestStackedValuesDirective::class => ['packageX', 'packageY'],
                SampleSingleValueDirective::class => 'local value'
            ];

        }

        public function testLoadingConfigFilesReferencingNonExistentDirectives()
        {
    
            $configLoader = new DirectoryLoader();
            $config = $configLoader->load(__DIR__ . '/unknown');
            $this->assertEquals('test', $config->get(TestDirective::class));
        }
    }
    

