<?php

    namespace ObjectivePHP\Config\Loader;

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Config\Exception;

    class DirectoryLoader implements LoaderInterface
    {
        /**
         * @param $location
         *
         * @return mixed
         */
        public function load($location)
        {
            $config = new Config();

            // prepare data for further treatment
            $location = realpath($location);

            if (!$location)
            {
                throw new Exception(sprintf('The config directory "%s" does not exist', $location), Exception::INVALID_LOCATION);
            }

            $directory = new \RecursiveDirectoryIterator($location);

            /** @var $entry \SplFileInfo */
            foreach (new \RecursiveIteratorIterator($directory) as $entry)
            {
                if ($entry->getExtension() != 'php') continue;

                // get config data
                $importedConfig = $this->import($entry, $config);
                if($importedConfig)
                {
                    $config->merge($importedConfig);
                }
            }

            return $config;
        }

        /**
         * @param $file
         * @param $config Config Make $config available in imported config file to manipulate it directly
         *
         * @return mixed
         */
        protected function import($file, $config)
        {
            $originalConfig = spl_object_hash($config);

            $importedConfig = (($importedConfig = include $file) !== 1) ? $importedConfig : null;

            // prevent current config overwriting
            if(spl_object_hash($config) != $originalConfig)
            {
                throw new Exception(sprintf('$config has been overwritten while importing "%s" ; please do not assign a value to $config in your config files', $file));
            }

            if($importedConfig && !$importedConfig instanceof Config)
            {
                $importedConfig = Config::factory($importedConfig);
            }

            return $importedConfig;
        }

    }