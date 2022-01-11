<?php
namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin;

use Barn2\PTS_Lib\Util,
	Barn2\PTS_Lib\Plugin\Plugin,
	Barn2\PTS_Lib\Registerable,
	Barn2\PTS_Lib\Service;

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Service {

	private $plugin;
	private $settings_page;

	public function __construct( Plugin $plugin ) {
		$this->plugin        = $plugin;
		$this->settings_page = new Settings_Page( $plugin );
	}

	public function register() {
		// Extra links on Plugins page
		add_filter( 'plugin_action_links_' . $this->plugin->get_basename(), [ $this, 'add_settings_link' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_pro_version_link' ], 10, 2 );

		// Admin scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'settings_page_scripts' ] );

		$this->settings_page->register();
	}

	public function add_settings_link( $links ) {
		array_unshift(
			$links,
			sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $this->plugin->get_settings_page_url() ),
				esc_html__( 'Settings', 'posts-data-table' )
			)
		);
		return $links;
	}

	public function add_pro_version_link( $links, $file ) {
		if ( $file === $this->plugin->get_basename() ) {
			$links[] = sprintf(
				'<a href="%1$s" target="_blank"><strong>%2$s</strong></a>',
				esc_url( 'https://barn2.com/wordpress-plugins/posts-table-pro/' ),
				esc_html__( 'Pro Version', 'posts-data-table' )
			);
		}

		return $links;
	}

	public function settings_page_scripts( $hook ) {
		if ( 'settings_page_posts_table_search_sort' === $hook ) {
			$suffix = Util::get_script_suffix();
			wp_enqueue_style( 'ptss-admin', plugins_url( "assets/css/admin/posts-data-table-admin{$suffix}.css", $this->plugin->get_file() ), [], $this->plugin->get_version() );
		}
	}

}
