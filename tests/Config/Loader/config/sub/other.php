<?php

    use ObjectivePHP\Config\Config;

    $config = new Config([
        'app.env'         => 'prod',
        'packages.loaded' => 'sub'
    ]);

    $extra = (new Config())->setSection('package.pre');

    $extra->fromArray([
        'version' => '0.1b'

    ]);

    return $config->merge($extra);