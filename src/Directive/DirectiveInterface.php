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
 * Interface DirectiveInterface
 *
 * @package ObjectivePHP\Config
 */
interface DirectiveInterface
{
    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function hydrate($data);

}
