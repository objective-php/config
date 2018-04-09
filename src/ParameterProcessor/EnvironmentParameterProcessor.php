<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 04/04/2018
 * Time: 16:05
 */

namespace ObjectivePHP\Config\ParameterProcessor;


class EnvironmentParameterProcessor extends AbstractParameterProcessor
{

    protected $referenceKeyword = 'env';

    public function process($parameter)
    {
        $environmentVariable = $this->parseParameter($parameter);

        return getenv($environmentVariable);
    }


}