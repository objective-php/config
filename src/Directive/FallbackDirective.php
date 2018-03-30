<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 28/03/2018
 * Time: 12:29
 */

namespace ObjectivePHP\Config\Directive;

/**
 * Class FallbackDirective
 *
 * This directive is automatically registered when configuration parameter
 * refers to an unregistered directive. It's not actually a scalar directive,
 * since it may contains any value, but we implement it to return it's value
 * rather that the FallbackDirective instance itself.
 *
 * @package ObjectivePHP\Config\Directive
 */
class FallbackDirective extends AbstractDirective implements ScalarDirectiveInterface
{
    /** @var mixed */
    protected $value;


    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function hydrate($data)
    {
        $this->value = $data;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}