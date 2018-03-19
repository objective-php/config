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

}
