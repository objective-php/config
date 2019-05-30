<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 27/03/2018
 * Time: 11:12
 */

namespace ObjectivePHP\Config;


interface ParametersProviderInterface
{
    /**
     * @return array Config parameters that will be used to hydrate a Config instance
     */
    public function getParameters(): array;
}