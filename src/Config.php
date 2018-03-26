<?php

namespace ObjectivePHP\Config;


use ObjectivePHP\Config\Directive\ComplexDirectiveInterface;
use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Config\Directive\MultiValueDirectiveInterface;
use ObjectivePHP\Config\Directive\ScalarDirectiveInterface;
use ObjectivePHP\Config\Exception\ConfigException;
use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\Primitives\Collection\Collection;
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
     * @var array Store multi valued directive values
     */
    protected $values = [];

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
     * @param DirectiveInterface[] ...$directives
     * @return $this|mixed
     */
    public function registerDirective(DirectiveInterface ...$directives)
    {
        foreach ($directives as $directive) {
            $this->directives[$directive->getKey()] = $directive;
            if ($directive instanceof MultiValueDirectiveInterface) {
                $this->values[$directive->getKey()]['default'] = $directive;
            }
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
     * @inheritdoc
     */
    public function set($key, $value): ConfigInterface
    {
        // extract actual directive key
        if (!isset($this->directives[$key])) {
            // add free param
        } else {
            $directive = $this->directives[$key];
            if (!$directive instanceof MultiValueDirectiveInterface) {
                $directive->hydrate($value);
            } else {

                if (!is_array($value)) {
                    throw new ConfigException(sprintf('MultiValueDirective "%s" must be hydrated using an array.',
                        get_class($this)));
                }

                foreach ($value as $reference => $data) {

                    if (!isset($this->values[$key][$reference])) {
                        /** @var ComplexDirectiveInterface $newInstance */
                        $newInstance = clone $this->values[$key]['default'];
                        $newInstance->hydrate($data);

                        if (is_int($reference)) {
                            $this->values[$key][] = $newInstance;
                        } else {
                            $this->values[$key][$reference] = $newInstance;
                        }
                    } else {
                        /** @var DirectiveInterface $directive */
                        $directive = $this->values[$key][$reference];
                        $directive->hydrate($data);
                    }
                }

            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $directive = $this->directives[$key] ?? null;

        if (is_null($directive)) {
            throw new ConfigException(sprintf('No configuration directive has been registered for key "%s"', $key));
        }

        if (!$directive instanceof MultiValueDirectiveInterface) {
            if ($directive instanceof ScalarDirectiveInterface) {
                return $directive->getValue();
            } else {
                return $directive;
            }
        } else {
            if ($directive instanceof ComplexDirectiveInterface) {
                return $this->values[$directive->getKey()];
            } else {

                $values = $this->values[$directive->getKey()];

                /** @var ScalarDirectiveInterface $value */
                foreach ($values as &$value) {
                    $value = $value->getValue();
                }

                return $values;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function merge(Config $config, MergerInterface $merger = null)
    {
        if ($merger) {
            $merger->merge($this, $config);
        } else {
            $this->hydrate($config->toArray());
        }

        return $this;
    }

    /**
     * @param $data
     * @return $this|void
     * @throws ConfigException
     */
    public function hydrate($data)
    {
        $data = Collection::cast($data);
        $data->each(function ($value, $key) {
            $this->set($key, $value);
        });
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $export = [];
        foreach ($this->directives as $directive) {
            $export[$directive->getKey()] = $directive->getValue();
        }

        return $export;
    }

}
