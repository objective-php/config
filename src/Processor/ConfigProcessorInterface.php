<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:18
 */

namespace ObjectivePHP\Config\Processor;


/**
 * Interface ConfigProcessorInterface
 * @package ObjectivePHP\Config\Processor
 */
interface ConfigProcessorInterface
{
    /**
     * @param $data
     * @return array
     */
    public function process($data): array;

    /**
     * @return array
     */
    public function getHandledExtensions(): array;
}