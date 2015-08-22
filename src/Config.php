<?php

    namespace ObjectivePHP\Config;
    
    
    use ObjectivePHP\Matcher\Matcher;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\Merger\MergerInterface;
    use ObjectivePHP\Primitives\Merger\ValueMerger;

    class Config extends Collection implements ConfigInterface
    {

        /**
         * @var string Current config section
         */
        protected $section;

        /**
         * @var Matcher
         */
        protected $matcher;

        /**
         * @param array  $input
         * @param int    $flags
         * @param string $iterator_class
         */
        public function __construct($input = [], $flags = \ArrayObject::ARRAY_AS_PROPS, $iterator_class = "ArrayIterator")
        {
            // force ARRAY_AS_PROPS
            $flags |= \ArrayObject::ARRAY_AS_PROPS;

            parent::__construct($input, $flags, $iterator_class);
        }

        /**
         * Ease fluent interface
         *
         * @param            $key
         * @param null|mixed $default
         *
         * @return mixed|Config
         */
        public function get($key, $default = null)
        {

            // if object has FQN key, return value
            if($this->has($key))
            {
                return $this->toArray()[$key];
            }

            // also try using current section as prefix (if any)
            if($section = $this->getSection())
            {
                $keyFQN = $this->section . '.' . $key;

                if($this->has($keyFQN))
                {
                    return $this->toArray()[$keyFQN];
                }
            }

            /**
             * @var $subSet Config
             */
            $subSet = $this->copy();

            $matcher = new Matcher();

            $subSet->filter(function(&$value, $directive) use($subSet, $key, $matcher)
            {
                return $matcher->match($key . '.*', $directive);

            });

            if(!$subSet->isEmpty())
            {
                $subSet->setSection($key);
                return $subSet;
            }
            else return $default;

        }

        /**
         * Define a key and associate a value to it
         *
         * @param $key
         * @param $value
         *
         * @todo Normalization has to happen twice in this implementation ; this should be prevented
         * @return $this
         * @throws Exception
         */
        public function set($directive, $value)
        {

            // normalize key
            $this->getKeyNormalizers()->each(function ($normalizer) use (&$directive)
            {
                $normalizer($directive);
            })
            ;

            foreach($this as $key => $val)
            {
                if ($this->getMatcher()->match($directive . '.*', $key))
                {
                    throw new Exception(sprintf('Setting directive "%s" is forbidden because it collides with a section name. Consider appending ".[directive-name]" to your directive key', $directive), Exception::FORBIDDEN_DIRECTIVE_NAME);
                }

                if ($this->getMatcher()->match($key . '.*', $directive))
                {
                    throw new Exception(sprintf('Setting directive "%s" is forbidden because it collides with another directive. Consider renaming your section', $directive), Exception::FORBIDDEN_SECTION_NAME);
                }
            }

            return parent::set($directive, $value);
        }

        /**
         * Wrapper for of array_merge
         *
         * @param $data
         *
         * @return $this
         */
        public function merge($data)
        {
            $data = Config::cast($data);

            foreach($data->getMergers() as $keys => $merger)
            {
                $this->addMerger($keys, $merger);
            }

            return parent::merge($data);
        }


        /**
         * @return string
         */
        public function getSection()
        {
            return $this->section;
        }

        /**
         * @param string $section
         *
         * @return $this
         */
        public function setSection($section)
        {

            $this->section = $section;
            if(!is_null($section))
            {
                // clear key normalizers
                $this->keyNormalizers = new Collection();

                $this->addKeyNormalizer(function (&$key)
                {
                    $prefix = $this->getSection() . '.';

                    if (strpos($key, $prefix) !== 0)
                    {
                        $key = $prefix . $key;
                    }
                });
            }


            return $this;
        }

        /**
         * @param array $configData
         */
        public static function factory($configData)
        {

            $section = null;
            $mergers = null;
            $validators = null;

            if(isset($configData['directives']))
            {
                $section  = isset($configData['section']) ? $configData['section'] : null;
                $mergers = isset($configData['mergers']) ? $configData['mergers'] : null;
                $validators = isset($configData['validators']) ? $configData['validators'] : null;
                $configData = $configData['directives'];
            }

            $config = new Config($configData);

            if($section) $config->setSection($section);

            if($mergers)
            {
                foreach($mergers as $keys => $merger)
                {
                    if(!$merger instanceof MergerInterface)
                    {
                        $merger = new ValueMerger($merger);
                    }

                    $config->addMerger($keys, $merger);
                }
            }

            if($validators) {
                foreach($validators as $validator) $config->addValidator($validator);
            }

            return $config;
        }

        /**
         * @return Matcher
         */
        public function getMatcher()
        {
            if(is_null($this->matcher))
            {
                $this->matcher = new Matcher;
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


    }