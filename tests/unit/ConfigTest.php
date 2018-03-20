<?php

namespace Tests\ObjectivePHP\Config;

use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Directive\AbstractScalarDirective;
use ObjectivePHP\Config\Exception\ConfigLoadingException;
use ObjectivePHP\PHPUnit\TestCase;
use Tests\Helper\TestDirectives\ComplexDirective;
use Tests\Helper\TestDirectives\MultiScalarDirective;

class ConfigTest extends TestCase
{
    
    public function testDirectiveRegistration()
    {
        $config = new Config();
        
        $directive = new ComplexDirective('a', 'b');
        
        $config->registerDirective($directive);
        
        $this->assertSame($directive, $config->get('complex'));
        
        
    }
    
    public function testConfigArrayExport()
    {
        $config = new Config();
        
        $directiveX = $this->getMockForAbstractClass(AbstractScalarDirective::class, ['a', 'x']);
        $directiveY = $this->getMockForAbstractClass(AbstractScalarDirective::class, ['b']);
        $directiveY->setKey('y');
        
        $config->registerDirective($directiveX, $directiveY);
        
        $this->assertEquals(['x' => 'a', 'y' => 'b'], $config->toArray());
        
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
        
        $this->expectException(ConfigLoadingException::class);
        
        $config->set('complex', 'scalar value');
        
    }
    
    public function testRegisteringMultiValueScalarDirective()
    {
        $config = new Config(new MultiScalarDirective('default value'));
        
        $this->assertInternalType('array', $config->get('multi-scalar'));
        $this->assertCount(1, $config->get('multi-scalar'));
        $this->assertArrayHasKey('default', $config->get('multi-scalar'));
        
        // multi-scalar[custom] = custom value
        $config->set('multi-scalar', ['custom' => 'custom value']);
        
        $this->assertCount(2, $config->get('multi-scalar'));
        $this->assertArrayHasKey('custom', $config->get('multi-scalar'));
        
        $this->expectException(ConfigLoadingException::class);
        $this->expectExceptionCode(ConfigLoadingException::INVALID_VALUE);
        
        $config->set('multi-scalar', ['custom' => ['not scalar value']]);
        
    }
    
}


class TestScalarDirective extends AbstractScalarDirective
{
    protected $key = 'test';
    
}
