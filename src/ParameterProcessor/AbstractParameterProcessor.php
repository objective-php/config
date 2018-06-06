<?php

namespace ObjectivePHP\Config\ParameterProcessor;

use ObjectivePHP\Config\ConfigInterface;

/**
 * Class AbstractParameterProcessor
 *
 * @package ObjectivePHP\Config\ParameterProcessor
 */
abstract class AbstractParameterProcessor implements ParameterProcessorInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var string
     */
    protected $referenceKeyword;

    /**
     * {@inheritdoc}
     */
    public function doesHandle($parameter): bool
    {
        $startPattern = $this->getReferenceKeyword() . '(';
        $endPattern = ')';

        if (!is_scalar($parameter)) {
            if (is_object($parameter) && method_exists($parameter, '__toString')) {
                $parameter = (string) $parameter;
            } else {
                return false;
            }
        }

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
