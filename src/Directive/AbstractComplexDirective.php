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
use ObjectivePHP\Primitives\String\Camel;


/**
 * Class AbstractDirective
 *
 * Describe here directive meaning and usage for auto-generated documentation
 *
 * @package ObjectivePHP\Config
 */
abstract class AbstractComplexDirective extends AbstractDirective
{

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->isHydrated() ? $this : $this->getDefaultValue();
    }

    public function getDefaultValue()
    {
        return $this->defaultValue ? parent::getDefaultValue() : $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function hydrate($data)
    {
        foreach ($data as $attribute => $value) {
            $setter = 'set' . Camel::case($attribute, Camel::UPPER);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                throw new ConfigLoadingException(sprintf('No setter method for attribute "%s"', $attribute));
            }
        }

        return $this;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        if (!$defaultValue instanceof static) {
            throw new ConfigLoadingException('Default value for complexw directives must be an instance of the directive itself.');
        }

        $this->defaultValue = $defaultValue;

        return $this;
    }

}
