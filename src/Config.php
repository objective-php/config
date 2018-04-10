<?php

namespace ObjectivePHP\Config;


use ObjectivePHP\Config\Directive\ComplexDirectiveInterface;
use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Config\Directive\FallbackDirective;
use ObjectivePHP\Config\Directive\IgnoreDefaultInterface;
use ObjectivePHP\Config\Directive\MultiValueDirectiveInterface;
use ObjectivePHP\Config\Directive\ScalarDirectiveInterface;
use ObjectivePHP\Config\Exception\ConfigException;
use ObjectivePHP\Config\Exception\ParamsProcessingException;
use ObjectivePHP\Config\ParameterProcessor\ParameterProcessorInterface;
use ObjectivePHP\Config\ParameterProcessor\ParameterReferenceParameterProcessor;

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

        // register default parameter processor
        $this->registerParameterProcessor((new ParameterReferenceParameterProcessor())->setConfig($this));
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

    /**
     * @param ParameterProcessorInterface[] $parameterProcessors
     */
    public function registerParameterProcessor(ParameterProcessorInterface ...$parameterProcessors)
    {
        foreach ($parameterProcessors as $parameterProcessor) {
            $parameterProcessor->setConfig($this);
        }
        $this->parameterProcessors = array_merge($this->parameterProcessors, $parameterProcessors);
        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
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
                return $this->processParameter($this->values[$key]);
            } else {
                try {
                    $processedParameters = $this->processParameters($this->values[$key]);
                } catch (\Throwable $exception) {
                    throw new ParamsProcessingException('Unable to process parameters', ParamsProcessingException::INVALID_VALUE, $exception);
                }
                $instance = (clone $directive)->hydrate($processedParameters);
                return $instance;
            }
        } else {
            $data = $this->values[$directive->getKey()];
            $parameters = [];
            foreach ($data as $id => $instanceParameters) {

                if ($id == 'default' && $directive instanceof IgnoreDefaultInterface) continue;

                if(!is_null($instanceParameters)) {
                    if (is_scalar($instanceParameters)) {
                        $instanceParameters = $this->processParameter($instanceParameters, $directive);
                    } else {
                        array_walk_recursive($instanceParameters, function (&$parameter) use ($directive) {
                            $parameter = $this->processParameter($parameter, $directive);
                        });
                    }
                }

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

    /**
     * @param $parameter
     * @return mixed
     */
    public function processParameter($parameter)
    {
        foreach ($this->getParameterProcessors() as $processor) {
            if ($processor->doesHandle($parameter)) {
                return $processor->process($parameter);
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

    /**
     * @param array $data
     * @return array
     */
    public function processParameters(array $data)
    {
        array_walk_recursive($data, function (&$parameter) {
            $parameter = $this->processParameter($parameter);
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

}
