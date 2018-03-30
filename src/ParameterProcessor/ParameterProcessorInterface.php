<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 29/03/2018
 * Time: 13:36
 */

namespace ObjectivePHP\Config\ParameterProcessor;


use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Directive\DirectiveInterface;

interface ParameterProcessorInterface
{

    public function setConfig(ConfigInterface $config);

    public function process($parameter, DirectiveInterface $config);

    public function doesHandle($parameter, DirectiveInterface $config): bool;
}