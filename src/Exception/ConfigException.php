<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 15/08/15
 * Time: 12:19
 */

namespace ObjectivePHP\Config\Exception;


class ConfigException extends \Exception
{
    const FORBIDDEN_DIRECTIVE_NAME = 0x10;
    const FORBIDDEN_SECTION_NAME = 0x11;

    const MISSING_DIRECTIVE = 0x20;

}
