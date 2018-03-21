<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:19
 */

namespace ObjectivePHP\Config\Processor;


use ObjectivePHP\Config\Exception\ParamsProcessingException;

class JsonParamsProcessor implements ConfigProcessorInterface
{
    public function process($data): array
    {
        $params = json_decode($data, true);

        if (is_null($params)) {
            throw new ParamsProcessingException('Failed decoding JSON params');
        }

        return $params;

    }

}