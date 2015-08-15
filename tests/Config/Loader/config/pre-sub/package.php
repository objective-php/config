<?php

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Primitives\Merger\MergePolicy;
    use ObjectivePHP\Primitives\Merger\ValueMerger;


    return (new Config([
                                'app.env' => 'package',
                                'package.token' => 'token',
                                'packages.loaded' => 'pre'
    ]))
        ->addMerger('packages.loaded', new ValueMerger(MergePolicy::COMBINE));