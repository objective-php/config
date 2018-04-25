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
abstract class AbstractMultiScalarDirective extends AbstractScalarDirective implements MultiValueDirectiveInterface
{
    use MultiValueDirectiveTrait;
}
