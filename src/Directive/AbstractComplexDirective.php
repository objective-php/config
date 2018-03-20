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
abstract class AbstractComplexDirective extends AbstractDirective implements ComplexDirectiveInterface
{
    
    
    /**
     * AbstractDirective constructor.
     *
     * @param mixed $defaultValue
     */
    public function __construct($key = null)
    {
        if ($key) {
            $this->setKey($key);
        }
    }
    
    
    /**
     * @param $value
     *
     * @return $this
     */
    public function hydrate($data)
    {
        if (!is_array($data)) {
            throw new ConfigLoadingException(sprintf('Hydration of "%s" requires data array. Scalar value passed.', get_class($this)), ConfigLoadingException::INVALID_VALUE);
        }
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
    
}
