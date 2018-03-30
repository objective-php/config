<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:18
 */

namespace ObjectivePHP\Config\Loader\FileLoader;


/**
 * Interface ConfigProcessorInterface
 * @package ObjectivePHP\Config\Processor
 */
interface FileLoaderAdapterInterface
{
    /**
     * @param $file string
     * @return array
     */
    public function process(string $filePath): array;

    /**
     * @return bool
     */
    public function doesHandle(\SplFileInfo $file): bool;
}