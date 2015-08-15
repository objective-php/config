<?php

    namespace ObjectivePHP\Config\Loader;

    interface LoaderInterface
    {
        /**
         *
         *
         * @param $location
         *
         * @return mixed
         */
        public function load($location);
    }