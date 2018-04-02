<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 29/03/2018
 * Time: 13:52
 */

namespace ObjectivePHP\Config\ParameterProcessor;


use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Directive\DirectiveInterface;

class ParameterReferenceParameterProcessor extends AbstractParameterProcessor
{

    protected $referenceKeyword = 'param';

    /** @var ConfigInterface */
    protected $config;

    public function process($parameter, DirectiveInterface $directive)
    {
        $directiveKey = $this->parseParameter($parameter);
        if ($directiveKey) {
            return $this->getConfig()->get($directiveKey);
        } else return $parameter;
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @param ConfigInterface $config
     * @return ParameterReferenceParameterProcessor
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

}