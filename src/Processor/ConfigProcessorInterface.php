<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 21/03/2018
 * Time: 11:18
 */

namespace ObjectivePHP\Config\Processor;


interface ConfigProcessorInterface
{
    public function process($data): array;
}