<?php

use bewil19\Site\Site;

require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$site = Site::getInstance(__DIR__);

$site->getPage();
