<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        __DIR__ . DIRECTORY_SEPARATOR . "vendor"
    ])
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();

$config->setRiskyAllowed(true);

$config->setRules([
    'modernize_strpos' => true,
    '@PhpCsFixer' => true
]);

$config->setFinder($finder);

return $config;