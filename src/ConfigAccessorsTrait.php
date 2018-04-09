<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 09/04/2018
 * Time: 17:25
 */

namespace ObjectivePHP\Config;


trait ConfigAccessorsTrait
{
    /** @var ConfigInterface */
    protected $config;

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function hasConfig(): bool
    {
        return (bool)$this->config;
    }


}