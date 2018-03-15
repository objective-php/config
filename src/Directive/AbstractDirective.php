<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Config\Directive;


/**
 * Class AbstractDirective
 *
 * Describe here directive meaning and usage for auto-generated documentation
 *
 * @package ObjectivePHP\Config
 */
abstract class AbstractDirective implements DirectiveInterface
{

    /**
     * @var mixed Directive value
     */
    protected $value;

    /**
     * @var
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
    public function __construct($defaultValue = null)
    {
        $this->setDefaultValue($defaultValue);
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
     * @return AbstractDirective
     */
    public function setValue($value)
    {
        $this->value = $value;

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
