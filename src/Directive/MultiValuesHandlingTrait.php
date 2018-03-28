<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 28/03/2018
 * Time: 15:51
 */

namespace ObjectivePHP\Config\Directive;


trait MultiValuesHandlingTrait
{
    protected $ignoreDefault = false;

    /**
     * @return bool
     */
    public function isDefaultIgnored(): bool
    {
        return $this->ignoreDefault;
    }

    public function ignoreDefault(bool $ignore = true)
    {
        $this->ignoreDefault = $ignore;
    }
}