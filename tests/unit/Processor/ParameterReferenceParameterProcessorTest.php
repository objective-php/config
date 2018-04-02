<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 31/03/2018
 * Time: 13:24
 */

namespace unit\Processor;


use Codeception\Test\Unit;
use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Config\ParameterProcessor\ParameterReferenceParameterProcessor;

class ParameterReferenceParameterProcessorTest extends Unit
{

    public function testParameterProcessing()
    {
        $processor = new ParameterReferenceParameterProcessor();

        $config = $this->makeEmpty(ConfigInterface::class, ['get' => function ($paramKey) {
            if ($paramKey == 'key') return 'parameter value';
        }]);
        // inject processor dependency
        $processor->setConfig($config);

        $directive = $this->makeEmpty(DirectiveInterface::class);


        $this->assertFalse($processor->doesHandle('not a parameter reference', $directive));

        $this->assertEquals('parameter value', $processor->process('param(key)', $directive));
    }

}