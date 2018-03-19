<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 19/03/2018
 * Time: 20:23
 */

namespace Tests\Helper\TestDirectives;


use ObjectivePHP\Config\Directive\AbstractComplexDirective;

class ComplexDirective extends AbstractComplexDirective
{

    protected $x;

    protected $y;

    protected $key = 'complex';

    public function __construct($x, $y)
    {
        $this->setX($x);
        $this->setY($y);
    }

    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param mixed $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param mixed $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }


}