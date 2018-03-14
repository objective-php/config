<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Config;


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
     * Directive identifiers (multiple identifiers are possible to alias directive reference)
     *
     * @var array
     */
    protected $identifiers = [];
    
    /**
     * AbstractDirective constructor.
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->defaultValue = $value;
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
     * @return bool
     */
    public function setValue($value): bool
    {
        $this->value = $value;
        
        return true;
    }
    
    /**
     * @return array
     */
    public function getIdentifiers(): array
    {
        return array_unique($this->identifiers, get_class($this));
    }
    
    /**
     * @param array $identifiers
     */
    public function setIdentifiers(...$identifiers)
    {
        $this->identifiers = $identifiers;
        
        return $this;
    }
    
    public function addIdentifier(string $identifier)
    {
        $this->identifiers[] = $identifier;
        $this->identifiers   = array_unique($this->identifiers);
        
        return $this;
    }
    
    public function removeIdentifier($identifier)
    {
        if ($key = array_search($identifier, $this->identifiers)) {
            unset($this->identifiers[$key]);
        }
        
        return $this;
    }
    
}
