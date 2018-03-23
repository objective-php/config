<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:33
 */

namespace Tests\ObjectivePHP\Config\Processor;


use Codeception\Test\Unit;
use ObjectivePHP\Config\Exception\ParamsProcessingException;
use ObjectivePHP\Config\Processor\JsonParamsProcessor;

class JsonParamsProcessorTest extends Unit
{

    public function testSuccessfulJsonProcessing()
    {

        $json = '{"key": "value"}';
        $params = (new JsonParamsProcessor())->process($json);
        $this->assertEquals(['key' => 'value'], $params);

    }


    public function testProcessingJsonString()
    {

        $json = '"valid json string but invalid value"';

        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        (new JsonParamsProcessor())->process($json);

    }


    public function testProcessingJsonArray()
    {

        $json = '["valid", "json", "array", "but invalid param value"]';

        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        (new JsonParamsProcessor())->process($json);

    }


}