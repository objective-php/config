<?php

    namespace ObjectivePHP\Config;


    use ObjectivePHP\Application\ApplicationInterface;
    use ObjectivePHP\Matcher\Matcher;
    use ObjectivePHP\Primitives\Collection\Collection;

    /**
     * Class Config
     *
     * @package ObjectivePHP\Config
     */
    class Config extends Collection implements ConfigInterface
    {
        /**
         * @var ApplicationInterface
         */
        protected $application;

        /**
         * @var Matcher
         */
        protected $matcher;

        /**
         * @var array Default internal value
         */
        protected $value = [];

        /**
         * Config constructor.
         *
         * @param array $input
         */
        public function __construct(array $input = [])
        {
            $this->fromArray($input);
        }

        /**
         * @param $directives
         *
         * @return $this
         */
        public function fromArray($directives)
        {
            foreach ($directives as $value)
            {
                $this->import($value);
            }

            return $this;
        }

        /**
         * Simpler getter
         *
         * @param            $key
         * @param null|mixed $default
         *
         * @return mixed|Config
         */
        public function get($key, $default = null)
        {
            return $this->value[$key] ?? $default;
        }

        /**
         * Extract a configuration subset
         *
         * This will return a new Config object, only containing values whose identifiers match
         * the given filter.
         *
         * @note Identifiers in the subset are cleaned up so that the filter part is removed.
         *       This is one of the reasons why the '.*' pattern is automatically added to the filter.
         *       This behaviour does not actually prevent from passing a full Matcher compatible pattern,
         *       but discourages it.
         *
         * @param $filter
         *
         * @return Config
         */
        public function subset($filter)
        {
            $filterLength = strlen($filter) + 1; // + 1 for the '.' following the prefix
            // normalize filter
            $filter .= '.*';

            $subset = new Config();
            foreach ($this as $key => $value)
            {
                if ($this->getMatcher()->match($filter, $key))
                {
                    $subset->set(substr($key, $filterLength), $value);
                }
            }

            return $subset;
        }

        /**
         * @return Matcher
         */
        public function getMatcher() : Matcher
        {
            if (is_null($this->matcher))
            {
                $this->matcher = new Matcher();
            }

            return $this->matcher;
        }

        /**
         * @param Matcher $matcher
         *
         * @return $this
         */
        public function setMatcher(Matcher $matcher)
        {
            $this->matcher = $matcher;

            return $this;
        }

        /**
         * @return ApplicationInterface
         */
        public function getApplication() : ApplicationInterface
        {
            return $this->application;
        }

        /**
         * @param ApplicationInterface $application
         *
         * @return $this
         */
        public function setApplication(ApplicationInterface $application) : ConfigInterface
        {
            $this->application = $application;

            return $this;
        }

        /**
         * Import a directive into Config object
         *
         * Note that the action of importing a directive is actually performed by
         * the directive itself. This allows keeping Config very simple while any
         * kind of merging policies can be added through objects implementing
         * DirectiveInterface
         *
         * @param DirectiveInterface $directive
         *
         * @return ConfigInterface
         */
        public function import(DirectiveInterface $directive) : ConfigInterface
        {
            $directive->mergeInto($this);

            return $this;
        }

    }
