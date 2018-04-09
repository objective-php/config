<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 29/03/2018
 * Time: 13:38
 */

namespace ObjectivePHP\Config\ParameterProcessor;


use ObjectivePHP\Config\ConfigInterface;

abstract class AbstractParameterProcessor implements ParameterProcessorInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    protected $referenceKeyword;

    public function doesHandle($parameter): bool
    {
        $startPattern = $this->getReferenceKeyword() . '(';
        $endPattern = ')';

        return substr($parameter, 0, strlen($startPattern)) === $startPattern && substr($parameter, -1) === $endPattern;

    }

    /**
     * @return mixed
     */
    public function getReferenceKeyword()
    {
        return $this->referenceKeyword;
    }

    /**
     * @param mixed $referenceKeyword
     */
    public function setReferenceKeyword(string $referenceKeyword)
    {
        $this->referenceKeyword = $referenceKeyword;
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
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param $parameter
     * @return string
     */

    protected function parseParameter($parameter)
    {
        return substr($parameter, strlen($this->getReferenceKeyword()) + 1, -1);
    }

}