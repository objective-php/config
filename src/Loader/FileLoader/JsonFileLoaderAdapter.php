<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:19
 */

namespace ObjectivePHP\Config\Loader\FileLoader;


use ObjectivePHP\Config\Exception\ParamsProcessingException;

class JsonFileLoaderAdapter implements FileLoaderAdapterInterface
{
    public function process(string $filePath): array
    {

        $data = file_get_contents($filePath);

        $parameters = json_decode($data, true);

        if (is_null($parameters)) {
            throw new ParamsProcessingException(sprintf('Failed decoding JSON parameters: %s', json_last_error_msg()), ParamsProcessingException::INVALID_VALUE);
        }

        if (!is_array($parameters) || isset($parameters[0])) {
            throw new ParamsProcessingException('JSON parameters must be enclosed in an object structure ({"directive-name": "param value"})', ParamsProcessingException::INVALID_VALUE);
        }

        return (array)$parameters;

    }

    /**
     * @return bool
     */
    public function doesHandle(\SplFileInfo $file): bool
    {
        return $file->getExtension() === 'json';
    }


}