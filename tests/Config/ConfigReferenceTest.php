<?php
namespace Test\ObjectivePHP\Config;

use ObjectivePHP\Config\ConfigReference;
use ObjectivePHP\PHPUnit\TestCase;

class ConfigReferenceTest extends TestCase
{
    public function testToStringImplementation()
    {
        $configRef = new ConfigReference('config-name');

        $this->assertEquals('config-name', (string)$configRef);
    }

    public function testgetId()
    {
        $configRef = new ConfigReference('config-name');

        $this->assertEquals('config-name', $configRef->getId());
    }
}
