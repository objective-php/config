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
            throw new ParamsProcessingException(sprintf('Failed decoding JSON params: %s', json_last_error_msg()), ParamsProcessingException::INVALID_VALUE);
        }

        if (!is_array($params) || isset($params[0])) {
            throw new ParamsProcessingException('JSON params must be enclosed in an object structure ({"directive-name": "param value"})', ParamsProcessingException::INVALID_VALUE);
        }

        return (array)$params;

    }

    /**
     * @return array
     */
    public function getHandledExtensions(): array
    {
        return ['json'];
    }

}