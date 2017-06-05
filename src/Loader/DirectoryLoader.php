<?php

namespace ObjectivePHP\Config\Loader;

use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Exception;
use ObjectivePHP\Config\StackedValuesDirective;

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
    
    public function loadInto(Config $config, $location): Config
    {
        // prepare data for further treatment
        $location = realpath($location);
        
        if (!$location) {
            throw new Exception(sprintf('The config directory "%s" does not exist', $location),
                Exception::INVALID_LOCATION);
        }
        
        $directory = new \RecursiveDirectoryIterator($location, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        
        $localEntries = [];
        
        $this->activateFakeAutoloader();
        
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
                    $config->import($directive);
                }
            }
        }
        
        
        // handle local entries,  that should overwrite global ones
        foreach ($localEntries as $entry) {
            // get config data
            $importedConfig = $this->import($entry, $config);
            if ($importedConfig) {
                foreach ($importedConfig as $directive) {
                    $config->import($directive);
                }
            }
        }
        
        $this->deactivateFakeAutoloader();
        
        return $config;
    }
    
    protected function activateFakeAutoloader()
    {
        spl_autoload_register([$this, 'fakeAutoload']);
    }
    
    /**
     * @param $file
     * @param $config Config Make $config available in imported config file to manipulate it directly
     *
     * @return array
     * @throws Exception
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
            throw new Exception(sprintf('$config has been overwritten while importing "%s" ; please do not assign a value to $config in your config files',
                $file));
        }
        
        return $importedConfig;
    }
    
    protected function deactivateFakeAutoloader()
    {
        spl_autoload_unregister([$this, 'fakeAutoload']);
    }
    
    public function fakeAutoload($className)
    {
        // separate namespace from class name
        $parts     = explode('\\', $className);
        $className = array_pop($parts);
        
        $namespace = implode('\\', $parts);
        
        eval('
                    namespace ' . $namespace . ' {
                        class ' . $className . ' extends \\' . StackedValuesDirective::class . ' {}
                    }'
        );
        
        return true;
    }
}
