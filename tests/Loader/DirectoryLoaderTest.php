<?php

    namespace Test\ObjectivePHP\Config\Loader;


    use ObjectivePHP\Config\Exception;
    use ObjectivePHP\Config\Loader\DirectoryLoader;
    use ObjectivePHP\Config\SingleValueDirectiveGroup;
    use ObjectivePHP\Config\SingleValueDirective;
    use ObjectivePHP\Config\StackedValuesDirective;
    use ObjectivePHP\PHPUnit\TestCase;

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
            $this->assertEquals($this->getExpectedConfig(), $config->toArray());

        }

        protected function getExpectedConfig()
        {

            return [
                TestSingleValueDirectiveGroup::class . '.version' => '1.0',
                TestSingleValueDirectiveGroup::class . '.env'     => 'test',
                TestStackedValuesDirective::class => ['packageX', 'packageY'],
                TestSingleValueDirective::class => 'local value'
            ];

        }

    }

    class TestSingleValueDirective extends SingleValueDirective
    {
    }

    class TestStackedValuesDirective extends StackedValuesDirective
    {
    }

    class TestSingleValueDirectiveGroup extends SingleValueDirectiveGroup
    {
    }
