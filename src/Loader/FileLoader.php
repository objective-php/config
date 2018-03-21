<?php

namespace ObjectivePHP\Config\Loader;

use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Exception\ConfigLoadingException;

class FileLoader implements LoaderInterface
{

    /**
     * @var array
     */
    protected $locations = [];

    public function load(): array
    {

        foreach ($this->locations as $location) {

            // prepare data for further treatment
            $location = realpath($location);

            if (!$location) {
                throw new ConfigLoadingException(sprintf('The config location "%s" does not exist', $location),
                    ConfigLoadingException::INVALID_LOCATION);
            }

            if (is_dir($location)) {
                $directory = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
                $entries = new \RecursiveIteratorIterator($directory);
            } else {
                $entries = [$location];
            }

            $localEntries = [];

            /** @var $entry \SplFileInfo */
            foreach ($entries as $entry) {
                if ($entry->getExtension() != 'php') {
                    continue;
                }

                // handle local entries later on
                if (strpos($entry, '.local.php')) {
                    $localEntries[] = $entry;
                    continue;
                }

                // get config data
                $importedConfig = $this->import($entry);
                if ($importedConfig) {
                    foreach ($importedConfig as $directive => $value) {
                        $config->set($directive, $value);
                    }
                }
            }
        }

        // handle local entries,  that should overwrite global ones
        foreach ($localEntries as $entry) {
            // get config data
            $importedConfig = $this->import($entry);
            if ($importedConfig) {
                foreach ($importedConfig as $directive => $value) {
                    $config->set($directive, $value);
                }
            }
        }

        return $config;
    }

    /**
     * @param $file
     * @param $config ConfigInterface Make $config available in imported config file to manipulate it directly
     *
     * @return array
     * @throws ConfigLoadingException
     */
    protected function import($file): array
    {

        $fileLoader = function ($path) {
            return (($importedConfig = include $path) !== 1) ? $importedConfig : null;
        };

        return $fileLoader($file);

    }

}
