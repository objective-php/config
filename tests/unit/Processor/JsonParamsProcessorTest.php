<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:33
 */

namespace Tests\ObjectivePHP\Config\Processor;


use Codeception\Test\Unit;
use ObjectivePHP\Config\Processor\JsonParamsProcessor;

class JsonParamsProcessorTest extends Unit
{

    public function testSuccessfulJsonProcessing()
    {
        $json = '{"key": "value"}';

        $params = (new JsonParamsProcessor())->process($json);

        $this->assertEquals(['key' => 'value'], $params);
    }

}