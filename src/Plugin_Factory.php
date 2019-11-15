<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

/**
 * Factory to create/return the shared plugin instance.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Factory {

    private static $plugin = null;

    public static function create( $file, $version ) {
        if ( null === self::$plugin ) {
            self::$plugin = new Plugin( $file, $version );
        }
        return self::$plugin;
    }

}
