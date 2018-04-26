<?php

/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace ObjectivePHP\Config\Directive;

/**
 * Class AbstractDirective
 *
 * Describe here directive meaning and usage for auto-generated documentation
 *
 * @package ObjectivePHP\Config
 */
abstract class AbstractDirective
{
    const KEY = '';

    /**
     * @var string Unique directive identifier in the Config object
     */
    protected $key;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key ?: static::KEY;
    }

    /**
     * @param string $key
     *
     * @return AbstractDirective
     */
    public function setKey(string $key)
    {
        $this->key = strtolower($key);

        return $this;
    }
}
