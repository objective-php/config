<?php

namespace Test\ObjectivePHP\Config;

use Codeception\Test\Unit;
use ObjectivePHP\Config\ConfigReference;

class ConfigReferenceTest extends Unit
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
