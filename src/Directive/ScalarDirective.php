<?php


namespace ObjectivePHP\Config\Directive;


class ScalarDirective extends AbstractScalarDirective
{

    /**
     * ScalarDirective constructor.
     */
    public function __construct($name, $value = null)
    {
        parent::__construct($value, $name);
    }
}
