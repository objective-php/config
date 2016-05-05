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

    abstract class SingleValueDirectiveGroup extends AbstractDirective
    {
        /**
         * @var string Identifier prefix
         */
        protected $prefix;

        /**
         * Directive configuration identifier (will be used as key in the Config object)
         */
        protected $identifier;

        /**
         * SingleValueDirective constructor.
         *
         * @param $identifier
         * @param $value
         */
        public function __construct($identifier, $value)
        {
            $this->identifier = $identifier;
            $this->value      = $value;
        }

        /**
         * @param ConfigInterface $config
         *
         * @return DirectiveInterface
         * @throws Exception
         */
        public function mergeInto(ConfigInterface $config) : DirectiveInterface
        {
            $identifier = sprintf('%s.%s', static::class, $this->identifier);

            // only set directive if it is not present
            if ($config->lacks($identifier))
            {
                $config->set($identifier, $this->getValue());
                return $this;
            }

            $mergePolicy = $this->mergePolicy;

            // automatically define the merge policy depending
            // on current directive value
            //
            // - MERGE arrays
            // - REPLACE other types

            if($mergePolicy == MergePolicy::AUTO)
            {
                if(is_array($config->get($identifier)))
                {
                    $mergePolicy = MergePolicy::NATIVE;
                }
                else $mergePolicy = MergePolicy::REPLACE;
            }

            // otherwise handle MergePolicy
            switch($mergePolicy)
            {
                case MergePolicy::SKIP:
                    break;

                case MergePolicy::REPLACE:
                    $config->set($identifier, $this->getValue());
                    break;

                case MergePolicy::NATIVE:
                    $combinedValue = array_merge((array) $config->get($identifier), (array) $this->value);
                    $config->set($identifier, $combinedValue);
                    break;

                case MergePolicy::COMBINE:
                case MergePolicy::RECURSIVE:
                    $combinedValue = array_merge_recursive((array) $config->get($identifier), (array) $this->value);
                    $config->set($identifier, $combinedValue);
                    break;

                default:
                    throw new Exception(sprintf('Unsupported merge policy "%s"', $this->mergePolicy));
            }

            return $this;
        }

    }
