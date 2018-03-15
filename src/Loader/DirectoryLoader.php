<?php

namespace ObjectivePHP\Config\Loader;

use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Exception\ConfigLoadingException;

class DirectoryLoader implements LoaderInterface
{
    /**
     * Load config files from a given directory
     *
     * @param $location
     *
     * @return Config
     */
    public function load($location): Config
    {
        $config = new Config();

        return $this->loadInto($config, $location);

    }

    public function loadInto(Config $config, $location): Config
    {
        // prepare data for further treatment
        $location = realpath($location);

        if (!$location) {
            throw new ConfigLoadingException(sprintf('The config directory "%s" does not exist', $location),
                ConfigLoadingException::INVALID_LOCATION);
        }

        $directory = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);

        $localEntries = [];

        /** @var $entry \SplFileInfo */
        foreach (new \RecursiveIteratorIterator($directory) as $entry) {
            if ($entry->getExtension() != 'php') {
                continue;
            }

            // handle local entries later on
            if (strpos($entry, '.local.php')) {
                $localEntries[] = $entry;
                continue;
            }

            // get config data
            $importedConfig = $this->import($entry, $config);
            if ($importedConfig) {
                foreach ($importedConfig as $directive) {
                    $config->registerDirective($directive);
                }
            }
        }


        // handle local entries,  that should overwrite global ones
        foreach ($localEntries as $entry) {
            // get config data
            $importedConfig = $this->import($entry, $config);
            if ($importedConfig) {
                foreach ($importedConfig as $directive) {
                    $config->registerDirective($directive);
                }
            }
        }

        return $config;
    }

    /**
     * @param $file
     * @param $config Config Make $config available in imported config file to manipulate it directly
     *
     * @return array
     * @throws ConfigLoadingException
     */
    protected function import($file, $config): array
    {
        $originalConfig = spl_object_hash($config);


        $fileLoader = function ($path) {
            return (($importedConfig = include $path) !== 1) ? $importedConfig : null;
        };

        $importedConfig = $fileLoader($file);

        // prevent current config overwriting
        if (spl_object_hash($config) != $originalConfig) {
            throw new ConfigLoadingException(sprintf('$config has been overwritten while importing "%s" ; please do not assign a value to $config in your config files',
                $file));
        }

        return $importedConfig;
    }

    /**
     * Load extra (optional) config files from a given directory
     *
     * @param $location
     *
     * @return Config
     */
    public function loadExtra($location): Config
    {
        $config = new Config();

        return is_dir($location) ? $this->loadInto($config, $location) : $config;

    }
}
