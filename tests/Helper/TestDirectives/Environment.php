<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 19/03/2018
 * Time: 16:11
 */

namespace Tests\Helper\TestDirectives;


use ObjectivePHP\Config\Directive\SingleDirective;

class Environment extends SingleDirective
{
    protected $key = 'env';
}
