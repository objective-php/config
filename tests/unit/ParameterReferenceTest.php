<?php

namespace Test\ObjectivePHP\Config;

use Codeception\Test\Unit;
use ObjectivePHP\Config\ParameterReference;

class ParameterReferenceTest extends Unit
{
    public function testToStringImplementation()
    {
        $configRef = new ParameterReference('config-name');

        $this->assertEquals('config-name', (string)$configRef);
    }

    public function testGetId()
    {
        $configRef = new ParameterReference('config-name');

        $this->assertEquals('config-name', $configRef->getId());
    }
}
