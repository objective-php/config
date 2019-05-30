<?php

namespace ObjectivePHP\Config\Loader\FileLoader;

use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Exception\ConfigException;
use ObjectivePHP\Config\Exception\ConfigLoadingException;
use ObjectivePHP\Config\Loader\AbstractLoader;
use ObjectivePHP\Config\Loader\LoaderInterface;
use SplFileInfo;
use DirectoryIterator;

class FileLoader extends AbstractLoader
{

    /**
     * @var array
     */
    protected $adapters = [];

    protected $loadingEnvironmentSpecificResources = false;

    /**
     * FileLoader constructor.
     * @param array $adapters
     */
    public function __construct(FileLoaderAdapterInterface ...$adapters)
    {
        $this->registerAdapter(new JsonFileLoaderAdapter(), ...$adapters);

    }

    public function registerAdapter(FileLoaderAdapterInterface ...$adapters)
    {
        $this->adapters = array_merge($this->adapters, $adapters);
    }

    public function load(...$locations): array
    {

        $parameters = [];
        $localEntries = [];

        foreach ($locations as $location) {

            // prepare data for further treatment
            $locationRealPath = realpath($location);

            if (!$locationRealPath) {
                throw new ConfigLoadingException(sprintf('The config location "%s" does not exist', $location),
                    ConfigLoadingException::INVALID_LOCATION);
            }

            if (is_dir($locationRealPath)) {
                $entries = new DirectoryIterator($locationRealPath);
            } else {
                $entries = [new SplFileInfo($locationRealPath)];
            }


            /** @var $entry \SplFileInfo */
            foreach ($entries as $entry) {
                if($entry->isDir()) continue;
                // handle local entries later on
                if (strpos($entry->getFilename(), '.local.')) {
                    $localEntries[] = clone $entry;
                    continue;
                }

                // get config data
                $parameters = array_merge($parameters, $this->process($entry));
            }
        }

        // handle local entries,  that should overwrite global ones
        foreach ($localEntries as $localEntry) {
            $parameters = array_merge($parameters, $this->process($localEntry));
        }

        // if current environment is set, try to load environment specific values
        if (($env = $this->getEnv()) && !$this->loadingEnvironmentSpecificResources) {
            $this->loadingEnvironmentSpecificResources = true;
            $envLocations = [];
            foreach ($locations as $location) {
                $envLocation = $location . DIRECTORY_SEPARATOR . $env;
                if (is_dir($envLocation)) {
                    $envLocations[] = $envLocation;
                }
            }

            $envConfig = $this->load(...$envLocations);
            $parameters = $this->merge($parameters, $envConfig);

            $this->loadingEnvironmentSpecificResources = false;
        }


        return $parameters;
    }

    /**
     * @param $file
     * @param $config ConfigInterface Make $config available in imported config file to manipulate it directly
     *
     * @return array
     * @throws ConfigLoadingException
     */
    protected function process(SplFileInfo $file): array
    {
        /** @var FileLoaderAdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter->doesHandle($file)) return $adapter->process($file->getRealPath());
        }

        return [];
    }


    protected function merge($config, $other)
    {
        foreach($config as $key => $value)
        {
            if(array_key_exists($key, $other)) {
               switch(true) {
                   case is_null($other[$key]):

                       $config[$key] = null;
                       break;

                   case is_scalar($config[$key]):
                       if(is_scalar($other[$key])) {
                           $config[$key] = $other[$key];
                       } else {
                           throw new ConfigException(sprintf('Key "%s" currently contains a scalar value. This can not be overriden with non-scalar value.', $key));
                       }
                       break;

                   case is_array($config[$key]):
                       if(is_array($other[$key])) {
                           $config[$key] = array_merge($config[$key], $other[$key]);
                       } else {
                           throw new ConfigException(sprintf('Key "%s" currently contains an array. This can not be merged with non-array value.', $key));
                       }
                       break;
               }

            }

        }

        return $config;
    }
}
