<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Config\Directive;

use ObjectivePHP\Config\Exception\ConfigLoadingException;


/**
 * Class AbstractDirective
 *
 * Describe here directive meaning and usage for auto-generated documentation
 *
 * @package ObjectivePHP\Config
 */
abstract class AbstractScalarDirective implements DirectiveInterface
{

    /**
     * @var mixed Directive value
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var string Unique directive identifier in the Config object
     */
    protected $key;

    /**
     * @var string Directive description (for documentation generation)
     */
    protected $description;

    /**
     * AbstractDirective constructor.
     *
     * @param mixed $defaultValue
     */
    public function __construct($defaultValue = null, $key = null)
    {
        $this->setDefaultValue($defaultValue);
        if (!is_null($key)) $this->setKey($key);
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value ?? $this->defaultValue;
    }

    /**
     * @param $value
     *
     * @return AbstractScalarDirective
     */
    public function hydrate($data)
    {
        if (!is_scalar($data)) {
            throw new ConfigLoadingException(sprintf('Config directive "%s" expects a scalar value. Trying to set "%s" value.', get_class($this), gettype($data)));
        }
        $this->value = $data;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = strtolower($key);

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }


}
