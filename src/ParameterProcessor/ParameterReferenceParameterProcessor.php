<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 29/03/2018
 * Time: 13:52
 */

namespace ObjectivePHP\Config\ParameterProcessor;


use ObjectivePHP\Config\Directive\DirectiveInterface;

class ParameterReferenceParameterProcessor extends AbstractParameterProcessor
{
    public function process($parameter, DirectiveInterface $config)
    {
        $directiveKey = $this->parseParameter($parameter);

        return $config->get($directiveKey);
    }

}