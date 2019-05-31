<?php


namespace ObjectivePHP\Config;


use ObjectivePHP\Config\Directive\DirectiveInterface;

interface DirectivesProviderInterface
{

    /**
     * @return DirectiveInterface[]
     */
    public function getDirectives() : array;

}