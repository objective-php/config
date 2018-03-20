<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:52
 */

namespace Tests\ObjectivePHP\Config\Directive;


use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Exception\ConfigLoadingException;
use ObjectivePHP\PHPUnit\TestCase;
use Tests\Helper\TestDirectives\ScalarDirective;

class ScalarDirectiveTest extends TestCase
{

    public function testDefaultBehaviour()
    {
        $config = new Config();

        $directive = new ScalarDirective('x');

        $config->registerDirective($directive);

        $this->assertEquals('x', $config->get('scalar'));

    }

    public function testAbstractScalarDirectiveConstructorRejectsNonScalarValues()
    {
        $this->expectException(ConfigLoadingException::class);
        $this->expectExceptionCode(ConfigLoadingException::INVALID_VALUE);
        
        new ScalarDirective(['not a scalar value']);
        
    }
    
    public function testScalarDirectiveRejectsNonScalarValues()
    {
        $this->expectException(ConfigLoadingException::class);
        $this->expectExceptionCode(ConfigLoadingException::INVALID_VALUE);
        
        (new ScalarDirective())->hydrate(['not a scalar value']);
        
    }
}
