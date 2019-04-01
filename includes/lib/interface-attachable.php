<?php

namespace Barn2\Lib;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( '\Barn2\Lib\Attachable' ) ) {

	/**
	 * An object that can be 'attached' to WordPress via the Plugin API (i.e. add_action and add_filter callbacks).
	 *
	 * @version 1.0
	 */
	interface Attachable {

		public function attach();
	}

}
