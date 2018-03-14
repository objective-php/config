<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:13
 */

namespace ObjectivePHP\Config;


class StackedDirective extends AbstractDirective
{


    protected $identifier;

    protected $identifiers = [];

    /**
     * @param ConfigInterface $config
     *
     * @return DirectiveInterface
     */
    public function mergeInto(ConfigInterface $config): DirectiveInterface
    {

        if(is_null($this->identifier)) {
            $this->identifier = static::class;
        }

        $stack = $config->get($this->identifier, []);
        $stack[] = $this;
        $config->set($this->identifier, $stack);

        foreach($this->identifiers as $alias)
        {
            $stack = $config->get($alias, []);
            $stack[] = $this;
            $config->set($alias, $stack);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        $this->value = $this;
        return parent::getValue();
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return array
     */
    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * @param array $identifiers
     */
    public function setIdentifiers(...$identifiers)
    {
        $this->identifiers = $identifiers;

        return $this;
    }

    public function addAlias(string $alias)
    {
        $this->identifiers[] = $alias;
        $this->identifiers   = array_unique($this->identifiers);

        return $this;
    }

    public function removeAlias($alias)
    {
        if($key = array_search($alias, $this->identifiers)) {
            unset($this->identifiers[$key]);
        }

        return $this;
    }

}
