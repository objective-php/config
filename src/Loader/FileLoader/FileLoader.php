<?php

namespace ObjectivePHP\Config\Loader\FileLoader;

use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Exception\ConfigLoadingException;
use ObjectivePHP\Config\Loader\LoaderInterface;
use SplFileInfo;

class FileLoader implements LoaderInterface
{

    /**
     * @var array
     */
    protected $adapters = [];

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

        $config = [];
        $localEntries = [];

        foreach ($locations as $location) {

            // prepare data for further treatment
            $locationRealPath = realpath($location);

            if (!$locationRealPath) {
                throw new ConfigLoadingException(sprintf('The config location "%s" does not exist', $location),
                    ConfigLoadingException::INVALID_LOCATION);
            }

            if (is_dir($locationRealPath)) {
                $directory = new \RecursiveDirectoryIterator($locationRealPath, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
                $entries = new \RecursiveIteratorIterator($directory);
            } else {
                $entries = [new SplFileInfo($locationRealPath)];
            }

            /** @var $entry \SplFileInfo */
            foreach ($entries as $entry) {

                // handle local entries later on
                if (strpos($entry, '.local.')) {
                    $localEntries[] = $entry;
                    continue;
                }

                // get config data
                $config = array_merge($config, $this->process($entry));
            }
        }

        // handle local entries,  that should overwrite global ones
        foreach ($localEntries as $entry) {
            $config = array_merge($config, $this->process($entry));
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
    protected function process(SplFileInfo $file): array
    {
        /** @var FileLoaderAdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter->doesHandle($file)) return $adapter->process($file->getRealPath());
        }

        return [];

    }

}
