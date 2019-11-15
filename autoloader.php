<?php

spl_autoload_register( function( $class ) {
    $namespace = 'Barn2\\Plugin\\Posts_Table_Search_Sort';
    $src_path  = __DIR__ . '/src';

    // Bail if the class is not in our namespace.
    if ( 0 !== strpos( $class, $namespace ) ) {
        return;
    }

    // Remove the namespace.
    $class = str_replace( $namespace, '', $class );

    // Build the filename - realpath returns false if file doesn't exist.
    $file = realpath( $src_path . '/' . str_replace( '\\', '/', $class ) . '.php' );

    // If the file exists for the class name, load it.
    if ( $file ) {
        include_once $file;
    }
}
);

spl_autoload_register( function( $class ) {
    $namespace = 'Barn2\\Lib';
    $lib_path  = __DIR__ . '/lib';

    // Bail if the class is not in our namespace.
    if ( 0 !== strpos( $class, $namespace ) ) {
        return;
    }

    // Remove the namespace.
    $class = str_replace( $namespace, '', $class );

    // Build the filename - realpath returns false if file doesn't exist.
    $file = realpath( $lib_path . '/' . str_replace( '\\', '/', $class ) . '.php' );

    // If the file exists for the class name, load it.
    if ( $file ) {
        include_once $file;
    }
}
);
