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

                $config->merge($this->import($entry));
            }

            return $config;
        }

        protected function import($file)
        {

            $config = include $file;

            if(!$config instanceof Config)
            {
                $config = Config::factory($config);
            }

            return $config;
        }

    }