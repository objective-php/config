<?php
    /**
     * This file is part of the Objective PHP project
     *
     * More info about Objective PHP on www.objective-php.org
     *
     * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
     */

    namespace ObjectivePHP\Config;


    use ObjectivePHP\Application\ApplicationInterface;

    interface ConfigInterface
    {
        public function get($key, $default = null);

        public function set($key, $value);

        public function import(DirectiveInterface $directive) : ConfigInterface;

        public function setApplication(ApplicationInterface $app) : ConfigInterface;

        public function getApplication() : ApplicationInterface;

    }
