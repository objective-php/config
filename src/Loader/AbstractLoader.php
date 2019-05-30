<?php


namespace ObjectivePHP\Config\Loader;


abstract class AbstractLoader implements LoaderInterface
{
    /**
     * @var string Current application environment
     */
    protected $env;

    /**
     * @return string
     */
    public function getEnv(): ?string
    {
        return $this->env;
    }

    /**
     * @param string $env
     * @return AbstractLoader
     */
    public function setEnv(string $env): AbstractLoader
    {
        $this->env = $env;
        return $this;
    }

}