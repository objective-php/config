<?php

namespace ObjectivePHP\Config;


use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\Primitives\Merger\MergerInterface;

/**
 * Class Config
 *
 * @package ObjectivePHP\Config
 */
class Config implements ConfigInterface
{
    
    
    /**
     * @var Matcher
     */
    protected $matcher;
    
    /**
     * @var array Default internal value
     */
    protected $directives = [];
    
    /**
     * Config constructor.
     *
     * @param array $input
     */
    public function __construct(DirectiveInterface ...$directives)
    {
        $this->registerDirective(...$directives);
    }
    
    
    /**
     * Simpler getter
     *
     * @param            $key
     * @param null|mixed $default
     *
     * @return mixed|Config
     */
    public function get($key, $default = null)
    {
        return $this->directives[$key] ?? $default;
    }
    
    
    /**
     * Extract a configuration subset
     *
     * This will return a new Config object, only containing values whose identifiers match
     * the given filter.
     *
     * @param $filter
     *
     * @return Config
     */
    public function subset($filter)
    {
        $filterLength = strlen($filter) + 1; // + 1 for the '.' following the prefix
        
        $subset = new Config();
        foreach ($this as $key => $value) {
            if ($this->getMatcher()->match($filter, $key)) {
                $subset->set(substr($key, $filterLength), $value);
            }
        }
        
        return $subset;
    }
    
    /**
     * @return Matcher
     */
    public function getMatcher(): Matcher
    {
        if (is_null($this->matcher)) {
            $this->matcher = new Matcher();
        }
        
        return $this->matcher;
    }
    
    /**
     * @param Matcher $matcher
     *
     * @return $this
     */
    public function setMatcher(Matcher $matcher)
    {
        $this->matcher = $matcher;
        
        return $this;
    }
    
    
    public function registerDirective(DirectiveInterface ...$directives)
    {
        foreach ($directives as $directive) {
            $identifiers = $directive->getIdentifiers();
            foreach ($identifiers as $identifier) {
                $this->directives[$identifier] = $directive;
            }
        }
        
        return $this;
    }
    
    public function merge(Config $config, MergerInterface $merger = null)
    {
        // TODO: Implement merge() method.
    }
    
    
}
