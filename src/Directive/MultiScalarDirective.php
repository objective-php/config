<?php


namespace ObjectivePHP\Config\Directive;


class MultiScalarDirective extends AbstractMultiScalarDirective
{

    /**
     * MultiScalarDirective constructor.
     */
    public function __construct($name, $value = null)
    {
        $this->setKey($name);

        $this->defaultValue = $value;
    }
}
