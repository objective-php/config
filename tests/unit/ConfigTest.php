<?php

namespace Tests\ObjectivePHP\Config;

use Codeception\Test\Unit;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Directive\AbstractScalarDirective;
use ObjectivePHP\Config\Exception\ParamsProcessingException;
use Tests\Helper\TestDirectives\ComplexDirective;
use Tests\Helper\TestDirectives\MultiComplexDirective;
use Tests\Helper\TestDirectives\MultiComplexDirectiveWithoutDefault;
use Tests\Helper\TestDirectives\MultiScalarDirective;

class ConfigTest extends Unit
{

    public function testDirectiveRegistration()
    {
        $config = new Config();

        $directive = new ComplexDirective('a', 'b');

        $config->registerDirective($directive);

        $this->assertSame($directive, $config->get('complex'));


    }


    public function testHydratingScalarDirective()
    {
        $config = new Config();

        $directive = new TestScalarDirective('default value');

        $config->registerDirective($directive);

        $this->assertEquals('default value', $config->get('test'));

        $config->set('test', 'custom value');
        $this->assertEquals('custom value', $config->get('test'));

    }

    public function testHydratingComplexDirective()
    {
        $config = new Config();

        $directive = new ComplexDirective('a', 'b');

        $config->registerDirective($directive);

        $this->assertEquals('a', $config->get('complex')->getX());
        $this->assertEquals('b', $config->get('complex')->getY());

        $config->set('complex', ['x' => 'updated']);
        $this->assertEquals('updated', $config->get('complex')->getX());
        $this->assertEquals('b', $config->get('complex')->getY());

        $this->expectException(ParamsProcessingException::class);

        $config->set('complex', 'scalar value');

    }

    public function testRegisteringMultiScalarDirective()
    {
        $config = new Config(new MultiScalarDirective('default value'));

        $this->assertInternalType('array', $config->get('multi-scalar'));
        $this->assertCount(1, $config->get('multi-scalar'));
        $this->assertArrayHasKey('default', $config->get('multi-scalar'));

        // multi-scalar[custom] = custom value
        $config->set('multi-scalar', ['custom' => 'custom value']);

        $this->assertCount(2, $config->get('multi-scalar'));
        $this->assertArrayHasKey('custom', $config->get('multi-scalar'));

        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        $config->set('multi-scalar', ['custom' => ['not scalar value']]);

    }

    public function testRegisteringMultiComplexDirective()
    {
        $config = new Config(new MultiComplexDirective('default x value', 'default y value'));


        $this->assertInternalType('array', $config->get('multi-complex'));
        $this->assertCount(1, $config->get('multi-complex'));
        $this->assertArrayHasKey('default', $config->get('multi-complex'));

        $config->set('multi-complex', ['default' => ['x' => 'custom value']]);

        $this->assertEquals('custom value', $config->get('multi-complex')['default']->getX());

        $config->set('multi-complex', ['other' => ['x' => 'other x value']]);

        $this->assertEquals('other x value', $config->get('multi-complex')['other']->getX());
        $this->assertEquals('default y value', $config->get('multi-complex')['other']->getY());

        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        $config->set('multi-complex', ['other' => ['not an associative array']]);

    }

    public function testMultiValuesDirectiveWithDefaultIgnored()
    {
        $config = new Config(new MultiComplexDirectiveWithoutDefault('default x value', 'default y value'));

        $this->assertEmpty($config->get('multi-complex'));

    }

    public function testFallbackDirectiveHandling()
    {
        $config = new Config();

        $config->set('unregistered.directive', 'any value');

        $this->assertEquals('any value', $config->get('unregistered.directive'));
    }

}


class TestScalarDirective extends AbstractScalarDirective
{
    protected $key = 'test';

}
