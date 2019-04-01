<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Lib\Util;

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements \Barn2\Lib\Attachable {

	private $settings_page;

	public function __construct() {
		$this->settings_page = new Admin_Settings_Page();
	}

	public function attach() {
		// Extra links on Plugins page
		add_filter( 'plugin_action_links_' . PTSS_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_pro_version_link' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'settings_page_scripts' ) );

		$this->settings_page->attach();
	}

	public function add_settings_link( $links ) {
		array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'options-general.php?page=' . Admin_Settings_Page::MENU_SLUG ) ), esc_html__( 'Settings', 'posts-data-table' ) ) );
		return $links;
	}

	public function add_pro_version_link( $links, $file ) {
		if ( PTSS_PLUGIN_BASENAME == $file ) {
			$links[] = sprintf( '<a href="%1$s" target="_blank"><strong>%2$s</strong></a>', esc_url( 'https://barn2.co.uk/wordpress-plugins/posts-table-pro/' ), esc_html__( 'Pro Version', 'posts-data-table' ) );
		}

		return $links;
	}

	public function settings_page_scripts( $hook ) {
		if ( 'settings_page_posts_table_search_sort' === $hook ) {
			$suffix = Util::get_script_suffix();
			wp_enqueue_style( 'ptss-admin', plugins_url( "assets/css/admin/posts-data-table-admin{$suffix}.css", Plugin::FILE ), array(), Plugin::VERSION );
		}
	}
}
