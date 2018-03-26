<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 08/03/2018
 * Time: 14:58
 */

namespace ObjectivePHP\Config\Merger;


use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\Exception\Exception\ConfigMergingException;
use ObjectivePHP\Primitives\Merger\MergePolicy;
use ObjectivePHP\Primitives\Merger\MergerInterface;

class ConfigMerger implements MergerInterface
{
    /** @var int */
    protected $policy = MergePolicy::AUTO;

    /**
     * @param $policy   mixed
     * @param $keys     mixed
     */
    public function __construct($policy)
    {
        $this->policy = $policy;
    }


    /**
     * Merge two values according to the defined policy
     *
     * @param $key
     * @param $first
     * @param $second
     *
     * @return mixed
     */
    public function merge($first, $second)
    {
        if (!$first instanceof ConfigInterface || !$second instanceof ConfigInterface) {
            throw new ConfigMergingException('Only "' . ConfigInterface::class . '" instances can be merged using "' . __CLASS__ . '"');
        }

        $first->hydrate($second->toArray());

    }

}
