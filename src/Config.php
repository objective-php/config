<?php

    namespace ObjectivePHP\Config;
    
    
    use ObjectivePHP\Matcher\Matcher;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\Merger\MergerInterface;
    use ObjectivePHP\Primitives\Merger\ValueMerger;

    /**
     * Class Config
     *
     * @package ObjectivePHP\Config
     */
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
            // normalize key
            $this->getKeyNormalizers()->each(function ($normalizer) use (&$directive)
            {
                $normalizer($directive);
            });

            // if a parent has been set, write to the parent, not the current object
            if($parent = $this->getParent())
            {
                return $parent->set($directive, $value);
            }

            $matcher = $this->getMatcher();
            foreach($this->value as $key => $val)
            {
                if ($matcher->match($directive . '.*', $key))
                {
                    throw new Exception(sprintf('Setting directive "%s" is forbidden because it collides with section "%s". Consider appending ".[directive-name]" to your directive key.', $directive, $key), Exception::FORBIDDEN_DIRECTIVE_NAME);
                }

                if ($matcher->match($key . '.*', $directive))
                {
                    throw new Exception(sprintf('Setting section "%s" is forbidden because it collides with directive "%s". Consider renaming your section.', $directive, $key), Exception::FORBIDDEN_SECTION_NAME);
                }
            }

            if($value instanceof Config)
            {

                $this->__get($directive)->fromArray($value);
            }
            else parent::set($directive, $value);

            return $this;
        }

        /**
         * Wrapper for of array_merge
         *
         * @param $data
         *
         * @return $this
         */
        /**
         * Merge a collection into another
         *
         * @param $data mixed Data to merge (will be casted to Collection)
         *
         * @return $this
         */
        public function merge($data)
        {

            if($parent = $this->getParent())
            {
                return $parent->merge($data);
            }

            $data    = Config::cast($data);

            // get mergers from merged data
            $data->getMergers()->each(function($merger, $key) {
                $this->addMerger($key, $merger);
            });

            $mergers = $this->getMergers();

            $mergedData = [];
            if (!$mergers->isEmpty())
            {
                // prepare data by manually merging some keys
                foreach ($mergers as $key => $merger)
                {
                    if (isset($data[$key]) && isset($this[$key]))
                    {
                        $data[$key] = $merger->merge($this[$key], $data[$key])->toArray();
                        $mergedData[$key] = true;
                    }
                }
            }

            $data->each(function($value, $key) use($mergedData)
            {
                // apply default merging strategies
                if(!isset($mergedData[$key]) && $this->has($key))
                {
                    //get current value
                    $currentValue = $this->get($key);

                    // merge if one of the values is an array
                    if(is_array($value) || is_array($currentValue) || $value instanceof Collection || $currentValue instanceof Collection)
                    {
                        $currentValue = Collection::cast($currentValue)->toArray();
                        $valueToMerge = Collection::cast($value)->toArray();
                        $value        = array_merge_recursive($currentValue, $valueToMerge);
                    }
                }

                $this->set($key, $value);
            });


            return $this;
        }

        /**
         * Add a merger
         *
         * @param                 $keys
         * @param MergerInterface $merger
         *
         * @return $this
         */
        public function addMerger($keys, MergerInterface $merger)
        {
            if ($parent = $this->getParent())
            {
                // normalize keys first
                $keys = Collection::cast($keys);

                $keys->each(function(&$key) {
                   $this->getKeyNormalizers()->each(function($normalizer) use(&$key){
                      $normalizer($key);
                   });
                });

                return $parent->addMerger($keys, $merger);
            }

            return parent::addMerger($keys, $merger);
        }


        /**
         *
         */
        public function toArray()
        {
            $array = [];
            $source = $this->getParent() ?: $this;
            $section = $this->getSection();
            $filter = false;
            if($section)
            {
                $matcher = $this->getMatcher();
                $filter = $section . '.*';
            }

            foreach($source->value as $key => $value)
            {

                if($filter && !$matcher->match($filter, $key))
                {
                    // skip current key
                    continue;
                }

                $key = $this->getRelativeDirectiveName($key);

                $sections = explode('.', $key);

                $directive = array_pop($sections);

                $currentLevelArray = &$array;

                foreach($sections as $section)
                {
                    if(!isset($currentLevelArray[$section]))
                    {
                        $currentLevelArray[$section] = [];
                    }
                    $currentLevelArray = &$currentLevelArray[$section];
                }
                $currentLevelArray[$directive] = $value;
            }

            return $array;
        }



        public function getRelativeDirectiveName($directive)
        {
            $section = $this->getSection();
            if($section)
            {
                if (strpos($directive, $section . '.') === 0)
                {
                    $directive = substr($directive, strlen($section) + 1);
                }
            }

            return $directive;

        }

        public function getCurrentLevelDirectiveName($directive)
        {
            $section   = $this->getSection();
            $directive = $this->getRelativeDirectiveName($directive);
            if(!$directive)
            {
                return null;
            }

            if ((string) $section)
            {
                $directive = $section . '.' . $directive;
            }

            return $directive;
        }

        /**
         * @return array
         * @throws \ObjectivePHP\Matcher\Exception
         */
        public function getDirectives()
        {

            $directives = [];
            $filter = false;
            if($section = $this->getSection())
            {
                $filter = $this->getSection() . '.*';
            }

            $source = $this->getParent() ?: $this;
            foreach($source->value as $key => $value)
            {
                if($filter && !$this->getMatcher()->match($filter, $key)) continue;

                $directives[$key] = $value;
            }

            return $directives;
        }

        /**
         * @param $directives
         *
         * @return $this
         */
        public function fromArray($directives)
        {
            $directives = Config::cast($directives);

            foreach($directives as $key => $value)
            {
                $this->set($key, $value);
            }

            return $this;
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


        public function addKeyNormalizer(callable $normalizer)
        {
            // applies normalizer to currently stored entries
            $data = $this->getDirectives();
            $this->clear();

            foreach ($data as $key => $value)
            {
                $normalizer($key);
                $this->set($key, $value);
            }

            // stack the new normalizer
            $this->getKeyNormalizers()[] = $normalizer;

            return $this;
        }
        /**
         * @param $section
         *
         * @return \Closure
         */protected function generateNormalizer($section)
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

        /**
         * @param $section
         *
         * @return bool
         * @throws \ObjectivePHP\Matcher\Exception
         */
        public function hasSection($section)
        {
            $section = $this->computeCurrentSection($section);
            $source = $this->getParent() ?: $this;
            $keys = array_keys($source->value);

            foreach($keys as $key)
            {
                if($this->getMatcher()->match($section . '.*', $key))
                {
                    return true;
                }
            }
            return false;
        }


        /**
         * @param $directive
         *
         * @return bool
         */
        public function hasDirective($directive)
        {

            $source = $this->getParent() ?: $this;
            $keys = array_keys($source->value);

            return \array_key_exists($directive, array_flip($keys));

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

            if($section) $config = $config->__get($section);

            foreach($configData as $key => $value)
            {
                $config->set($key, $value);
            }

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


        /**
         * @param $key
         *
         * @return mixed|null|Config
         */public function __get($key)
        {

            if($value = $this->get($key)) return $value;

            $config = $this->getClone($key);

            return $config;

        }

        /**
         * @param $directive
         * @param $value
         *
         * @throws Exception
         */public function __set($directive, $value)
        {
            if ($currentSection = $this->getSection())
            {
                $directive = $currentSection . '.' . $directive;
            }

            $this->set($directive, $value);
        }

        /**
         * @param $key
         *
         * @return mixed|null|Config
         */protected function getClone($key)
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

        /**
         *
         */
        public function __clone()
        {
            $this->keyNormalizers = null;
            $this->parent = null;
            $this->value = [];
        }

        /**
         * @param $section
         *
         * @return string
         */public function computeCurrentSection($section)
        {
            ;
            if ($currentSection = $this->getSection())
            {
                $section = $currentSection . '.' . $section;
            }

            return $section;
        }

        /**
         * @param mixed $index
         * @param mixed $value
         */
        public function offsetSet($index, $value)
        {
            $this->__set($index, $value);
        }
    }