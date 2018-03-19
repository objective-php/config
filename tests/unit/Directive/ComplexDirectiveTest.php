<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:52
 */

namespace Tests\ObjectivePHP\Config\Directive;


use ObjectivePHP\Config\Config;
use ObjectivePHP\PHPUnit\TestCase;
use Tests\Helper\TestDirectives\ComplexDirective;

class ComplexDirectiveTest extends TestCase
{

    public function testDefaultBehaviour()
    {
        $config = new Config();

        $directive = new ComplexDirective('x', 'y');

        $config->registerDirective($directive);

        $this->assertSame($directive, $config->get('complex'));

        $this->assertEquals('x', $config->get('complex')->getX());
    }

}

