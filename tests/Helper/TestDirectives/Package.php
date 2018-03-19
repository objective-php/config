<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 19/03/2018
 * Time: 15:50
 */

namespace Tests\Helper\TestDirectives;


use ObjectivePHP\Config\Directive\StackedScalarDirective;

class Package extends StackedScalarDirective
{
    protected $key = 'packages';
}
