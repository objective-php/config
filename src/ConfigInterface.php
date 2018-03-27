<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Config;


use ObjectivePHP\Config\Directive\DirectiveInterface;
use ObjectivePHP\Primitives\Merger\MergerInterface;

/**
 * Interface ConfigInterface
 * @package ObjectivePHP\Config
 */
interface ConfigInterface
{
    /**
     * Get a parameter matching the provided key
     *
     * This method may return various kind of value:
     *
     * - a DirectiveInterface instance for ComplexDirectiveInterface
     * - a directive value for ScalarDirectiveInterface
     * - an array of DirectiveInterface instance for ComplexDirectiveInterface + MultiValueDirectiveInterface
     * - an array of values for ScalarDirectiveInterface + MultiValueDirectiveInterface
     *
     * @param $key string
     * @return mixed
     */
    public function get($key);

    /**
     * Set a configuration parameter
     *
     * Provided value will be passed to the hydrate() method of the matching directive.
     *
     * @param $key string
     * @param $value mixed
     * @return ConfigInterface
     */
    public function set($key, $value): ConfigInterface;

    /**
     * @param Config $config
     * @param MergerInterface|null $merger
     * @return $this
     */
    public function merge(ConfigInterface $config);

    /**
     * @param DirectiveInterface[] ...$directives
     * @return mixed
     */
    public function registerDirective(DirectiveInterface ...$directives);

    /**
     * @param $data
     * @return $this
     */
    public function hydrate($data);

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return array
     */
    public function getDirectives() : array;
}
