<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:13
 */

namespace ObjectivePHP\Config\Directive;


class StackedDirective extends AbstractDirective implements MultipleValuesDirective
{

    protected $id;


    public function getId()
    {
        return $this->id;
    }
}
