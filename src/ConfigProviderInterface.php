<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 08/03/2018
 * Time: 14:52
 */

namespace ObjectivePHP\Config;


interface ConfigProviderInterface
{
    public function getConfig(): ConfigInterface;

    public function hasConfig(): bool;
}
