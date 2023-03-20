<?php

declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

return [
    // The prefix configuration. If a non null value will be used, a random prefix will be generated.
    'prefix'                     => 'Barn2\\Plugin\\Posts_Table_Search_Sort\\Dependencies',
    'expose-global-constants' => false,
    'expose-global-classes'   => false,
    'expose-global-functions' => false,

    /**
     * By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
     * directory. You can however define which files should be scoped by defining a collection of Finders in the
     * following configuration key.
     *
     * For more see: https://github.com/humbug/php-scoper#finders-and-paths.
     */
    'finders'                    => [
        Finder::create()->
        files()->
        ignoreVCS( true )->
        notName( '/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.(json|lock)/' )->
        exclude(
            [
                'doc',
                'test',
                'build',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ]
        )->
        in(
            [
                'vendor/barn2/setup-wizard/',
            ]
        )->
        append(
            [
                'vendor/barn2/setup-wizard/build/setup-wizard.asset.php',
                'vendor/barn2/setup-wizard/build/setup-wizard.css',
                'vendor/barn2/setup-wizard/build/setup-wizard.js',
            ]
        )->
        name( [ '*.php' ] ),
    ],

    'exclude-classes' => [
        "WP_REST_Response",
    ],

    /** When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
     * original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
     * support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
     * heart contents.
     *
     * For more see: https://github.com/humbug/php-scoper#patchers.
     */
    'patchers'                   => [
        function ( string $file_path, string $prefix, string $contents ): string {
            // Change the contents here.

            return str_replace(
                'Symfony\\\\',
                sprintf( '%s\\\\Symfony\\\\', addslashes( $prefix ) ),
                $contents
            );
        },
    ],
];