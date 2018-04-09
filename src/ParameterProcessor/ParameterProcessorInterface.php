<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 29/03/2018
 * Time: 13:36
 */

namespace ObjectivePHP\Config\ParameterProcessor;


use ObjectivePHP\Config\ConfigInterface;

interface ParameterProcessorInterface
{
    public function setConfig(ConfigInterface $config);

    public function process($parameter);

    public function doesHandle($parameter): bool;
}