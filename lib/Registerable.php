<?php

namespace Barn2\Lib;

/**
 * An object that can be registered with WordPress via the Plugin API (i.e. add_action and add_filter callbacks).
 *
 * @version 1.1
 */
if ( ! \interface_exists( __NAMESPACE__ . '\Registerable' ) ) {

    interface Registerable {

        public function register();

    }

}
