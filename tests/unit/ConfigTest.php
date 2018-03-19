<?php

namespace Tests\ObjectivePHP\Config;

use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Directive\AbstractScalarDirective;
use ObjectivePHP\PHPUnit\TestCase;

class ConfigTest extends TestCase
{

    public function testDirectiveRegistration()
    {
        $config = new Config();

        $directive = $this->getMockForAbstractClass(AbstractScalarDirective::class);
        $directive->setKey('x');

        $config->registerDirective($directive);

        $this->assertSame($directive, $config->get('x'));


    }

    public function testConfigArrayExport()
    {
        $config = new Config();

        $directiveX = $this->getMockForAbstractClass(AbstractScalarDirective::class, ['a']);
        $directiveX->setKey('x');
        $directiveY = $this->getMockForAbstractClass(AbstractScalarDirective::class, ['b']);
        $directiveY->setKey('y');

        $config->registerDirective($directiveX, $directiveY);

        $this->assertEquals(['x' => 'a', 'y' => 'b'], $config->toArray());

    }

    public function testSettingDirectiveAValue()
    {
        $config = new Config();

        $directive = new TestScalarDirective('default value');

        $config->registerDirective($directive);

        $this->assertSame($directive, $testDirective = $config->get('test'));

        $this->assertEquals('default value', $testDirective->getValue());

        $config->set('test', 'custom value');
        $this->assertEquals('custom value', $testDirective->getValue());

    }

}


class TestScalarDirective extends AbstractScalarDirective
{
    protected $key = 'test';

}
