<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 31/03/2018
 * Time: 13:24
 */


use Codeception\Test\Unit;
use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Config\ParameterProcessor\EnvironmentParameterProcessor;

class EnvironmentParameterProcessorTest extends Unit
{

    public function testParameterProcessing()
    {
        $processor = new EnvironmentParameterProcessor();

        $directive = $this->makeEmpty(DirectiveInterface::class);


        $this->assertFalse($processor->doesHandle('not a parameter reference', $directive));

        putenv('key=parameter value');

        $this->assertEquals('parameter value', $processor->process('env(key)', $directive));
    }

}