<?php
    /**
     * Created by PhpStorm.
     * User: gauthier
     * Date: 15/08/15
     * Time: 12:43
     */
    
    namespace ObjectivePHP\Config;
    
    
    interface ConfigInterface
    {
        public function get($key);
    }