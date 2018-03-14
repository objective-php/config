<?php

namespace Test\ObjectivePHP\Config;

use ObjectivePHP\Config\AbstractDirective;
use ObjectivePHP\Config\Config;
use ObjectivePHP\PHPUnit\TestCase;

class ConfigTest extends TestCase
{
    
    public function testDirectiveRegistration()
    {
        $config = new Config();
        
        $directive = $this->getMockForAbstractClass(AbstractDirective::class);
        $directive->method('getIdentifiers')->willReturn(['x']);
        
        
        $config->registerDirective($directive);
        
        $this->assertSame($directive, $config->get('x'));
        
        
    }
    
    
}
