<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 09/04/2018
 * Time: 17:25
 */

namespace ObjectivePHP\Config;


interface ConfigAwareInterface
{
    public function setConfig(ConfigInterface $config);
}