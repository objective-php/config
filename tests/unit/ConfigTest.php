<?php

namespace Tests\ObjectivePHP\Config;

use Codeception\Test\Unit;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Directive\AbstractScalarDirective;
use ObjectivePHP\Config\Exception\ParamsProcessingException;
use ObjectivePHP\Config\ParameterProcessor\ParameterProcessorInterface;
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

        $this->assertEquals($directive, $config->get('complex'));


    }

    /**
     * @throws ParamsProcessingException
     * @throws \ObjectivePHP\Config\Exception\ConfigException
     */
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
        $config->get('complex');

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
        $config->get('multi-scalar');
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
        $config->get('multi-complex');

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

    public function testParameterProcessingForScalar()
    {
        $config = new Config();

        $config->set('x', 'x value');
        $config->set('y', 'param(x)');

        $this->assertEquals('x value', $config->get('y'));
    }

    public function testParameterProcessingForComplex()
    {
        $config = new Config();
        $config->registerDirective(new ComplexDirective('param(x)', 'param y'));

        $config->set('x', 'x value');

        $this->assertEquals('x value', $config->get('complex')->getX());
    }

    public function testParameterProcessingForMultiComplex()
    {
        $config = new Config();
        $config->registerDirective(new MultiComplexDirective('param(x)', 'param y'));

        $config->set('x', 'x value');

        $this->assertEquals('x value', $config->get('multi-complex')['default']->getX());
    }

    public function testRegisteringMultipleParameterProcessors()
    {
        $config = new Config();

        $config->registerParameterProcessor($this->makeEmpty(ParameterProcessorInterface::class));

        $this->assertCount(2, $config->getParameterProcessors());
    }
}


class TestScalarDirective extends AbstractScalarDirective
{
    protected $key = 'test';

}
