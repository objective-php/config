<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 08/03/2018
 * Time: 15:01
 */

namespace ObjectivePHP\Config\Exception;


class ConfigLoadingException extends ConfigException
{
    const INVALID_LOCATION = 0x20;
}
