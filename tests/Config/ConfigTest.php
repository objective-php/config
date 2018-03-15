<?php

namespace Test\ObjectivePHP\Config;

use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Directive\AbstractDirective;
use ObjectivePHP\PHPUnit\TestCase;

class ConfigTest extends TestCase
{

    public function testDirectiveRegistration()
    {
        $config = new Config();

        $directive = $this->getMockForAbstractClass(AbstractDirective::class);
        $directive->setKey('x');

        $config->registerDirective($directive);

        $this->assertSame($directive, $config->get('x'));


    }

    public function testConfigArrayExport()
    {
        $config = new Config();

        $directiveX = $this->getMockForAbstractClass(AbstractDirective::class, ['a']);
        $directiveX->setKey('x');
        $directiveY = $this->getMockForAbstractClass(AbstractDirective::class, ['b']);
        $directiveY->setKey('y');

        $config->registerDirective($directiveX, $directiveY);

        $this->assertEquals(['x' => 'a', 'y' => 'b'], $config->toArray());

    }

}
