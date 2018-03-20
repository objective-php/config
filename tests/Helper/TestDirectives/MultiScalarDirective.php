<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 19/03/2018
 * Time: 20:15
 */

namespace Tests\Helper\TestDirectives;


use ObjectivePHP\Config\Directive\AbstractScalarDirective;
use ObjectivePHP\Config\Directive\MultiValueDirectiveInterface;

class MultiScalarDirective extends AbstractScalarDirective implements MultiValueDirectiveInterface
{
    protected $key = 'multi-scalar';
}
