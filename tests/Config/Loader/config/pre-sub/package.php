<?php

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Primitives\Merger\MergePolicy;
    use ObjectivePHP\Primitives\Merger\ValueMerger;

    /*

    // Programmatic version

    return (new Config([
                                'app.env' => 'package',
                                'package.token' => 'token',
                                'packages.loaded' => 'pre'
    ]))
        ->addMerger('packages.loaded', new ValueMerger(MergePolicy::COMBINE));
    */


    // declarative version
return [

    'mergers' => [
        'packages.loaded' => MergePolicy::COMBINE
    ],
    'directives' => [
        'app.env'         => 'package',
        'package.token'   => 'token',
        'packages.loaded' => 'pre'
    ]
];