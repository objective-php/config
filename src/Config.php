<?php

namespace ObjectivePHP\Config;


use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Config\Directive\MultipleValuesDirective;
use ObjectivePHP\Config\Exception\ConfigException;
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

    public function registerDirective(DirectiveInterface ...$directives)
    {
        foreach ($directives as $directive) {

            $this->directives[$directive->getKey()] = $directive;
        }

        return $this;
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

    /**
     * @param $key
     * @param $value
     * @return Config
     */
    public function set($key, $value)
    {

        // extract actual directive key
        $parts = explode('.', strtolower($key), 2);
        $directiveKey = $parts[0];
        $extraKey = $parts[1] ?? null;

        if (!isset($this->directives[$directiveKey])) {
            // add free param
        } else {
            $directive = $this->get($directiveKey);
            $directive->setValue($value, $extraKey);
        }

        return $this;
    }

    /**
     * Simpler getter
     *
     * @param            $key
     * @param null|mixed $default
     *
     * @return mixed|Config
     */
    public function get($key)
    {
        // TODO handle missing directives
        $directive = $this->directives[$key] ?? null;

        if (is_null($directive)) {
            throw new ConfigException(sprintf('No configuration directive has been registered for key "%s"', $key));
        }

        return $directive->getValue();
    }

    public function merge(Config $config, MergerInterface $merger = null)
    {
        // TODO: Implement merge() method.
    }

    public function toArray()
    {
        $export = [];
        foreach ($this->directives as $directive) {
            if (is_array($directive)) {
                var_dump($directive);
                exit;
            }
            $export[$directive->getKey()] = $directive->getValue();
        }

        return $export;
    }


}
