<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 04/04/2018
 * Time: 16:05
 */

namespace ObjectivePHP\Config\ParameterProcessor;


use ObjectivePHP\Config\Directive\DirectiveInterface;

class EnvironmentParameterProcessor extends AbstractParameterProcessor
{

    protected $referenceKeyword = 'env';

    public function process($parameter, DirectiveInterface $directive)
    {
        $environmentVariable = $this->parseParameter($parameter);

        return getenv($environmentVariable);
    }


}