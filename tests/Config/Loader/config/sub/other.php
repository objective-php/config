<?php

    use ObjectivePHP\Config\Config;

    $otherConfig = new Config([
        'app.env'         => 'prod',
        'packages.loaded' => 'sub'
    ]);

    $extra = (new Config())->setSection('package.pre');

    $extra->fromArray([
        'version' => '0.1b'

    ]);

    return $otherConfig->merge($extra);