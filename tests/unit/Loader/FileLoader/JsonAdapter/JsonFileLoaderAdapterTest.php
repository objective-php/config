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
use ObjectivePHP\Config\Loader\FileLoader\JsonFileLoaderAdapter;

class JsonFileLoaderAdapterTest extends Unit
{

    public function testSuccessfulJsonProcessing()
    {

        $params = (new JsonFileLoaderAdapter())->process(__DIR__ . '/valid-json-object.json');
        $this->assertEquals(['key' => 'value'], $params);

    }


    public function testProcessingJsonString()
    {

        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        (new JsonFileLoaderAdapter())->process(__DIR__ . '/valid-json-string-invalid-value.json');

    }


    public function testProcessingJsonArray()
    {
        $this->expectException(ParamsProcessingException::class);
        $this->expectExceptionCode(ParamsProcessingException::INVALID_VALUE);

        (new JsonFileLoaderAdapter())->process(__DIR__ . '/valid-json-array-invalid-value.json');

    }

}