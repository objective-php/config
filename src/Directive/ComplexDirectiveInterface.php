<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 20/03/2018
 * Time: 14:00
 */

namespace ObjectivePHP\Config\Directive;


interface ComplexDirectiveInterface extends DirectiveInterface
{

    public function toArray(): array;
}
