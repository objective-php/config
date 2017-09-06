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

    protected $aliases = [];

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

        foreach($this->aliases as $alias)
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
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param array $aliases
     */
    public function setAliases(...$aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function addAlias(string $alias)
    {
        $this->aliases[] = $alias;
        $this->aliases = array_unique($this->aliases);

        return $this;
    }

    public function removeAlias($alias)
    {
        if($key = array_search($alias, $this->aliases)) {
            unset($this->aliases[$key]);
        }

        return $this;
    }

}