<?php

/**
 * The main plugin file for Posts Table with Search & Sort.
 *
 * @wordpress-plugin
 * Plugin Name:     Posts Table with Search & Sort
 * Plugin URI:      https://wordpress.org/plugins/posts-data-table/
 * Description:     Provides a shortcode to show a list of your posts in an instantly searchable & sortable table.
 * Version:         1.2
 * Author:          Barn2 Media
 * Author URI:      https://barn2.co.uk
 * Text Domain:     posts-data-table
 * Domain Path:     /languages
 *
 * Copyright:		Barn2 Media Ltd
 * License:			GNU General Public License v3.0
 * License URI:		https://www.gnu.org/licenses/gpl.html
 */

namespace Barn2\Plugin\Posts_Table_Search_Sort {

	// Prevent direct file access
	if ( ! defined( '\ABSPATH' ) ) {
		exit;
	}

	use Barn2\Lib\Util;

	/**
	 * The main plugin class.
	 *
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 */
	final class Plugin {

		const VERSION	 = '1.2';
		const FILE	 = __FILE__;

		private $helpers;

		/**
		 * The singleton instance
		 */
		private static $_instance = null;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function load() {
			add_action( 'plugins_loaded', array( $this, 'maybe_load_plugin' ) );
		}

		private function includes() {
			require_once __DIR__ . '/includes/lib/interface-attachable.php';
			require_once __DIR__ . '/includes/lib/class-util.php';
			require_once __DIR__ . '/includes/lib/class-wp-settings-api-helper.php';

			require_once __DIR__ . '/includes/class-settings.php';
			require_once __DIR__ . '/includes/class-simple-posts-table.php';
			require_once __DIR__ . '/includes/class-frontend-scripts.php';
			require_once __DIR__ . '/includes/class-table-shortcode.php';
			require_once __DIR__ . '/includes/deprecated.php';

			require_once __DIR__ . '/includes/admin/class-admin-controller.php';
			require_once __DIR__ . '/includes/admin/class-admin-settings-page.php';
		}

		private function define_constants() {
			define( 'PTSS_PLUGIN_BASENAME', plugin_basename( self::FILE ) );
		}

		public function maybe_load_plugin() {
			// Don't load plugin if Pro version active
			if ( class_exists( '\Posts_Table_Pro_Plugin' ) ) {
				return;
			}

			add_action( 'init', array( $this, 'init' ) );
		}

		public function init() {
			$this->includes();
			$this->define_constants();
			$this->load_textdomain();

			$this->helpers = array();

			if ( Util::is_admin() ) {
				$this->helpers['admin'] = new Admin_Controller();
			}

			if ( Util::is_front_end() ) {
				// Register the posts table shortcode
				$this->helpers['shortcode']	 = new Table_Shortcode();
				$this->helpers['scripts']	 = new Frontend_Scripts();
			}

			foreach ( $this->helpers as $attachable ) {
				$attachable->attach();
			}
		}

		private function load_textdomain() {
			load_plugin_textdomain( 'posts-data-table', false, dirname( plugin_basename( self::FILE ) ) . '/languages' );
		}
	}

	// Load the plugin
	Plugin::instance()->load();
}

namespace {

	if ( ! function_exists( 'posts_table_search_sort' ) ) {

		function posts_table_search_sort() {
			return \Barn2\Plugin\Posts_Table_Search_Sort\Plugin::instance();
		}
	}
}
