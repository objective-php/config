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
use ObjectivePHP\Config\Directive\ScalarDirective;
use ObjectivePHP\Config\Exception\ParamsProcessingException;

class ScalarDirectiveTest extends Unit
{

    public function testDefaultBehaviour()
    {
        $config = new Config();

        $directive = new ScalarDirective('test', 'x');

        $config->registerDirective($directive);

        $this->assertEquals('x', $config->get('test'));

    }

    public function testAbstractScalarDirectiveConstructorRejectsNonScalarValues()
    {
        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        new ScalarDirective('test', ['not a scalar value']);

    }

    public function testScalarDirectiveRejectsNonScalarValues()
    {
        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        (new ScalarDirective('test'))->hydrate(['not a scalar value']);

    }
}
