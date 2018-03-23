<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Config\Directive;

use ObjectivePHP\Config\Exception\ParamsProcessingException;


/**
 * Class AbstractDirective
 *
 * Describe here directive meaning and usage for auto-generated documentation
 *
 * @package ObjectivePHP\Config
 */
abstract class AbstractScalarDirective extends AbstractDirective implements ScalarDirectiveInterface
{
    /**
     * @var bool|float|int|string
     */
    protected $defaultValue;

    /**
     * @var bool|float|int|string Directive value
     */
    protected $value;


    /**
     * AbstractDirective constructor.
     *
     * @param mixed $defaultValue
     */
    public function __construct($defaultValue = null, $key = null)
    {
        if (!is_scalar($defaultValue)) {
            throw new ParamsProcessingException(sprintf('Config directive "%s" expects a scalar value. Trying to set "%s" value.',
                get_class($this), gettype($defaultValue)), ParamsProcessingException::INVALID_VALUE);
        }

        $this->defaultValue = $defaultValue;

        if (!is_null($key)) {
            $this->setKey($key);
        }
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value ?: $this->defaultValue;
    }

    /**
     * @param $value
     *
     * @return AbstractScalarDirective
     */
    public function hydrate($data)
    {
        if (!is_scalar($data)) {
            throw new ParamsProcessingException(sprintf('Config directive "%s" expects a scalar value. Trying to set "%s" value.',
                get_class($this), gettype($data)), ParamsProcessingException::INVALID_VALUE);
        }

        $this->value = $data;

        return $this;
    }

}
