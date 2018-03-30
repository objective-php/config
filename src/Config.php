<?php

namespace ObjectivePHP\Config;


use ObjectivePHP\Config\Directive\ComplexDirectiveInterface;
use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Config\Directive\FallbackDirective;
use ObjectivePHP\Config\Directive\MultiValueDirectiveInterface;
use ObjectivePHP\Config\Directive\ScalarDirectiveInterface;
use ObjectivePHP\Config\Exception\ConfigException;
use ObjectivePHP\Config\ParameterProcessor\ParameterProcessorInterface;

/**
 * Class Config
 *
 * @package ObjectivePHP\Config
 */
class Config implements ConfigInterface
{

    /**
     * @var array Default internal value
     */
    protected $directives = [];

    /**
     * @var array Store multi valued directive values
     */
    protected $values = [];

    /** @var ParameterProcessorInterface[] */
    protected $parameterProcessors = [];

    /**
     * Config constructor.
     *
     * @param DirectiveInterface[]
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
                if ($directive instanceof ComplexDirectiveInterface) {
                    $this->values[$directive->getKey()]['default'] = $directive->toArray();
                } elseif ($directive instanceof ScalarDirectiveInterface) {
                    $this->values[$directive->getKey()]['default'] = $directive->getValue();
                }
            } else
                if ($directive instanceof ScalarDirectiveInterface) {
                    $this->values[$directive->getKey()] = $directive->getValue();
                } else {
                    $this->values[$directive->getKey()] = $directive->toArray();
                }
        }

        return $this;
    }

    public function has($key): bool
    {
        return isset($this->directives[$key]);
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
                return $this->processParameter($directive->getValue(), $directive);
            } else {
                $instance = (clone $directive)->hydrate($this->processParameters($this->values[$key], $directive));
                return $instance;
            }
        } else {
            codecept_debug($this->values);
            $data = $this->values[$directive->getKey()];
            $parameters = [];
            foreach ($data as $id => $instanceParameters) {

                if ($id == 'default' && $directive->isDefaultIgnored()) continue;

                if (is_scalar($instanceParameters)) {
                    $instanceParameters = $this->processParameter($instanceParameters, $directive);
                } else {
                    array_walk_recursive($instanceParameters, function (&$parameter) use ($directive) {
                        $parameter = $this->processParameter($parameter, $directive);
                    });
                }

                codecept_debug($instanceParameters);
                if (($directive instanceof ScalarDirectiveInterface)) {
                    $instance = (clone $directive)->hydrate($instanceParameters)->getValue();
                } else {
                    $instance = (clone $directive)->hydrate($instanceParameters);
                }

                $parameters[$id] = $instance;
            }


            return $parameters;
        }
    }

    public function processParameter($parameter, DirectiveInterface $directive)
    {
        foreach ($this->getParameterProcessors() as $processor) {
            if ($processor->doesHandle($parameter, $directive)) {
                return $processor->process($parameter, $directive);
            }
        }

        return $parameter;
    }

    /**
     * @return ParameterProcessorInterface[]
     */
    public function getParameterProcessors(): array
    {
        return $this->parameterProcessors;
    }

    protected function processParameters(array $data, $directive)
    {
        array_walk_recursive($data, function (&$parameter) use ($directive) {
            $parameter = $this->processParameter($parameter, $directive);
        });

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function merge(ConfigInterface $config)
    {
        $this->registerDirective(...array_values($config->getDirectives()));

        return $this;
    }

    /**
     * @param $data
     * @return $this|void
     * @throws ConfigException
     */
    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value): ConfigInterface
    {
        // extract actual directive key
        if (!isset($this->directives[$key])) {
            $this->registerDirective(new FallbackDirective($key, $value));
            $this->values[$key] = $value;

        } else {
            $directive = $this->directives[$key];
            if (!$directive instanceof MultiValueDirectiveInterface) {
                $this->values[$key] = $value;
            } else {

                if (!is_array($value)) {
                    throw new ConfigException(sprintf('MultiValueDirective "%s" must be hydrated using an array.',
                        get_class($this)));
                }

                foreach ($value as $reference => $data) {

                    if (!isset($this->values[$key][$reference])) {

                        if (is_int($reference)) {
                            $this->values[$key][] = $data;
                        } else {
                            $this->values[$key][$reference] = $data;
                        }
                    } else {
                        /** @var DirectiveInterface $directive */
                        $this->values[$key][$reference] = $data;
                    }
                }

            }
        }

        return $this;
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

    /**
     * @return array
     */
    public function getDirectives(): array
    {
        return $this->directives;
    }

    /**
     * @param ParameterProcessorInterface[] $parameterProcessors
     */
    public function registerParameterProcessor(ParameterProcessorInterface ...$parameterProcessors)
    {
        $this->parameterProcessors += $parameterProcessors;
    }

}
