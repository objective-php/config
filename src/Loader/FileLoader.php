<?php

namespace ObjectivePHP\Config\Loader;

use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Exception\ConfigLoadingException;
use ObjectivePHP\Config\Processor\ConfigProcessorInterface;
use ObjectivePHP\Config\Processor\JsonParamsProcessor;
use SplFileInfo;

class FileLoader implements LoaderInterface
{

    /**
     * @var array
     */
    protected $processors = [];

    /**
     * FileLoader constructor.
     * @param array $processors
     */
    public function __construct()
    {
        $this->registerProcessor(new JsonParamsProcessor());
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
                if (!isset($this->processors[$entry->getExtension()])) {
                    continue;
                }

                // handle local entries later on
                if (strpos($entry, '.local.')) {
                    $localEntries[] = $entry;
                    continue;
                }

                // get config data
                $importedConfig = $this->import($entry);
                if ($importedConfig) {
                    $config = array_merge($config, $importedConfig);
                }
            }
        }

        // handle local entries,  that should overwrite global ones
        foreach ($localEntries as $entry) {
            // get config data
            $importedConfig = $this->import($entry);
            if ($importedConfig) {
                $config = array_merge($config, $importedConfig);
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
    protected function import(SplFileInfo $file): array
    {
        if ($file->getExtension() == '.php') {
            $data = include $file->getRealPath();
        } else {
            $data = file_get_contents($file->getRealPath());
        }

        $processor = $this->processors[$file->getExtension()];

        $processedData = $processor->process($data);

        return $processedData;

    }

    public function registerProcessor(ConfigProcessorInterface $processor, string ...$handledExtensions)
    {
        $handledExtensions += $processor->getHandledExtensions();
        $handledExtensions = array_unique($handledExtensions);
        
        if (!$handledExtensions) {
            throw new ConfigLoadingException('Param processors must be associated to at least one file extension.');
        }
        foreach ($handledExtensions as $extension) {
            $this->processors[$extension] = $processor;
        }
    }

}
