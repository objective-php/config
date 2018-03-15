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

interface ConfigInterface
{
    public function get($key, $default = null);

    public function merge(Config $config, MergerInterface $merger = null);

    public function registerDirective(DirectiveInterface ...$directives);
}
