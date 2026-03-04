<?php

/**
 * @see \Rector\Config\RectorConfig
 * @see https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md
 */

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/bootstrap',
    ])
    // register single rule
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class,
    ])
    // here we can define, what prepared sets of rules will be applied
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )
    // Skip Filament and other directories
    ->withSkip([
        __DIR__.'/vendor/*',
        __DIR__.'/node_modules/*',
        __DIR__.'/bootstrap/cache/*',
        __DIR__.'/storage/*',
        __DIR__.'/resources/*',
        __DIR__.'/app/Providers/Filament/*',
        __DIR__.'/app/Filament/*',
        __DIR__.'/vendor/filament/*',
        __DIR__.'/vendor/bezhansalleh/filament-google-analytics/*',
    ]);
