<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:13
 */

namespace ObjectivePHP\Config\Directive;


class StackedScalarDirective extends AbstractScalarDirective
{

    protected $id;

    protected $defaultValue = [];

    protected $value = [];

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $value
     *
     * @return AbstractScalarDirective
     */
    public function setValue($value, $key = null)
    {
        $values = (array)$value;

        foreach ($values as $id => $value) {
            if (is_string($id)) {
                $this->value[$id] = $value;
            } else {
                $this->value[] = $value;
            }
        }
    }


}
