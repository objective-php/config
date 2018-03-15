<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 05/09/2017
 * Time: 16:52
 */

namespace Tests\ObjectivePHP\Config\Directive;


use ObjectivePHP\Config\Config;
use ObjectivePHP\Config\Directive\StackedDirective;
use ObjectivePHP\PHPUnit\TestCase;

class StackedDirectiveTest extends TestCase
{

    public function testDefaultBehaviour()
    {
        $config = new Config();

        $directive = new SampleStackedDirective('x', 'y');

        $config->registerDirective($directive);

        $this->assertSame($directive, $config->get('sample.directive')[0]);
    }

}

class SampleStackedDirective extends StackedDirective
{

    protected $x;

    protected $y;

    protected $key = 'sample.directive';

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
