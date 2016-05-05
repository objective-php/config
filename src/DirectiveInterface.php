<?php
    /**
     * This file is part of the Objective PHP project
     *
     * More info about Objective PHP on www.objective-php.org
     *
     * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
     */
    
    namespace ObjectivePHP\Config;

    /**
     * Interface DirectiveInterface
     *
     * @package ObjectivePHP\Config
     */
    interface DirectiveInterface
    {

        /**
         * @param ConfigInterface $config
         *
         * @return DirectiveInterface
         */
        public function mergeInto(ConfigInterface $config) : DirectiveInterface;

    }
