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

}
