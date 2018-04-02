<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:52
 */

namespace Tests\ObjectivePHP\Config\Directive;


use Codeception\Test\Unit;
use ObjectivePHP\Config\Config;
use Tests\Helper\TestDirectives\ComplexDirective;

class ComplexDirectiveTest extends Unit
{

    public function testDefaultBehaviour()
    {
        $config = new Config();

        $directive = new ComplexDirective('x', 'y');

        $config->registerDirective($directive);

        $this->assertEquals($directive, $config->get('complex'));

        $this->assertEquals('x', $config->get('complex')->getX());
    }

}

