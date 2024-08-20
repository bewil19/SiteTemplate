<?php

use bewil19\Site\Site;

$possibleFiles = [
    __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php',
    __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php',
    __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php',
    __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php',
];

$file = null;
foreach ($possibleFiles as $possibleFile) {
    if (file_exists($possibleFile)) {
        $file = $possibleFile;

        break;
    }
}

if (null === $file) {
    throw new Exception('Unable to locate autoload.php file.');
}

require_once $file;

unset($file, $possibleFiles, $possibleFile);

$site = Site::getInstance(__DIR__);

$site->getPage();
