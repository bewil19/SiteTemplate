<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/example',
        __DIR__.'/src',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(php83: true)
    ->withTypeCoverageLevel(9)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
    ])
;
