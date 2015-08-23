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
         * @var Config
         */
        protected $parent;


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


            // if section exists, return section
            if($this->hasSection($key))
            {
                return $this->getClone($key);
            }

            $key = $this->computeCurrentSection($key);

            $target = $this->getParent() ?: $this;

            if (isset($target->value[$key]))
            {
                return $target->value[$key];
            }
            else
            {
                return $default;
            }

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
            // if a parent has been set, write to the parent, not the current object
            if($parent = $this->getParent())
            {
                return $parent->set($directive, $value);
            }

            // normalize key
            $this->getKeyNormalizers()->each(function ($normalizer) use (&$directive)
            {
                $normalizer($directive);
            })
            ;

            foreach($this->value as $key => $val)
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

        public function toArray()
        {

            if(!$parent = $this->getParent())
            {
                return parent::toArray();
            }
            $directives = [];
            $filter = $this->getSection() . '.*';

            foreach($parent as $key => $value)
            {
                if($this->getMatcher()->match($filter, $key))
                {
                    $directives[$key] = $value;
                }
            }

            return $directives;
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

                $this->addKeyNormalizer($this->generateNormalizer($section));
            }

            return $this;
        }

        protected function generateNormalizer($section)
        {
            return function (&$key) use($section)
            {
                $prefix = $section . '.';

                if (strpos($key, $prefix) !== 0)
                {
                    $key = $prefix . $key;
                }
            };
        }

        public function hasSection($section)
        {
            $section = $this->computeCurrentSection($section);
            $keys = $this->getParent() ? $this->getParent()->keys() : $this->keys();

            foreach($keys as $key)
            {
                if($this->getMatcher()->match($section . '.*', $key))
                {
                    return true;
                }
            }
            return false;
        }


        public function hasDirective($directive)
        {
            $keys = $this->getParent() ? $this->getParent()->keys() : $this->keys();
            foreach($keys as $key)
            {
                if($key == $directive) return true;
            }

            return false;
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

            $config = new Config();

            if($section) $config->setSection($section);

            $config->fromArray($configData);

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

            // unset section to prevent mis-computation of FQN for directives
            $config->setSection(null);


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

        /**
         * @return Config
         */
        public function getParent()
        {
            return $this->parent;
        }

        /**
         * @param Config $parent
         *
         * @return $this
         */
        public function setParent($parent)
        {
            $this->parent = $parent;

            return $this;
        }



        public function __get($key)
        {

            if($value = $this->get($key)) return $value;

            $config = $this->getClone($key);

            return $config;

        }

        public function __set($directive, $value)
        {
            if ($currentSection = $this->getSection())
            {
                $directive = $currentSection . '.' . $directive;
            }

            $this->set($directive, $value);
        }

        protected function getClone($key)
        {
            $section = $this->computeCurrentSection($key);

            $target = $this->getParent() ?: $this;

            if (isset($target[$section])) return $target[$section];

            $config = clone $this;

            // shunt setSection to prevent keys from being prefixed with current section
            //$config->section = $section;
            //$config->keyNormalizers = new Collection([$this->generateNormalizer($section)]);
            $config->setSection($section);
            $config->setParent($this->getParent() ?: $this);

            return $config;
        }
        public function __clone()
        {
            $this->keyNormalizers = null;
            $this->parent = null;
            $this->value = [];
        }

        public function computeCurrentSection($section)
        {
            ;
            if ($currentSection = $this->getSection())
            {
                $section = $currentSection . '.' . $section;
            }

            return $section;
        }

        public function offsetSet($index, $value)
        {
            $this->__set($index, $value);
        }
    }