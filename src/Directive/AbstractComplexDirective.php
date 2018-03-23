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
     * @param $value
     *
     * @return $this
     */
    public function hydrate($data)
    {
        if (!is_array($data)) {
            throw new ParamsProcessingException(sprintf('Hydration of "%s" requires data array. Scalar value passed.', get_class($this)), ParamsProcessingException::INVALID_VALUE);
        }
        foreach ($data as $attribute => $value) {

            if (is_int($attribute)) {
                throw new ParamsProcessingException(sprintf('Complex directives must be hydrated using associative arrays. Integer key was provided.'), ParamsProcessingException::INVALID_VALUE);
            }

            $setter = 'set' . Camel::case($attribute, Camel::UPPER);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                throw new ParamsProcessingException(sprintf('No setter method for attribute "%s"', $attribute), ParamsProcessingException::UNKNOWN_ATTRIBUTE);
            }
        }

        return $this;
    }

}
