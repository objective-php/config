<?php
    /**
     * This file is part of the Objective PHP project
     *
     * More info about Objective PHP on www.objective-php.org
     *
     * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
     */
    
    namespace ObjectivePHP\Config;
    
    
    use ObjectivePHP\Primitives\Merger\MergePolicy;
    use ObjectivePHP\Primitives\Merger\ValueMerger;

    abstract class SingleValueDirective extends AbstractDirective
    {

        protected $mergePolicy = MergePolicy::REPLACE;

        /**
         * SingleValueDirective constructor.
         *
         * @param $value
         */
        public function __construct($value)
        {
            $this->value = $value;
        }

        /**
         * @param ConfigInterface $config
         *
         * @return DirectiveInterface
         * @throws Exception
         */
        public function mergeInto(ConfigInterface $config) : DirectiveInterface
        {
            $identifier = static::class;

            // only set directive if it is not present or if it can be overridden
            $config->set($identifier, (new ValueMerger($this->mergePolicy))->merge($config->get($identifier), $this->getValue()));

            return $this;
        }

    }
