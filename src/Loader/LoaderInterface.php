<?php

namespace ObjectivePHP\Config\Loader;

use ObjectivePHP\Config\ConfigInterface;

interface LoaderInterface
{
    /**
     *
     *
     * @param $location
     *
     * @return ConfigInterface
     */
    public function load(): array;

    /**
     * @return mixed Tell the loader the current environment
     */
    public function setEnv(string $env);

    /**
     * @return mixed Return current environment the loader is aware of
     */
    public function getEnv() :? string;
}
