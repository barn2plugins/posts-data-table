<?php
namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Util;
use	Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Service_Container;

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Standard_Service {

	use Service_Container;

	private $plugin;
	private $settings_page;

	public function __construct( Plugin $plugin ) {
		$this->plugin        = $plugin;
		// $this->settings_page = new Settings_Page( $plugin );
	}

	public function register() {
		$this->register_services();
		$this->start_all_services();
		// Extra links on Plugins page
		add_filter( 'plugin_action_links_' . $this->plugin->get_basename(), [ $this, 'add_settings_link' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_pro_version_link' ], 10, 2 );

		// Admin scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'settings_page_scripts' ] );

		// $this->settings_page->register();
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
				esc_url( 'https://barn2.com/wordpress-plugins/posts-table-pro/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=ptss-ptp' ),
				esc_html__( 'Pro Version', 'posts-data-table' )
			);
		}

		return $links;
	}

	public function add_services() {
		$this->add_service( 'settings_page', new Settings_Page( $this->plugin ) );
	}

	public function settings_page_scripts( $hook ) {
		if ( 'settings_page_posts_table_search_sort' === $hook ) {
			$suffix = Util::get_script_suffix();
			wp_enqueue_style( 'ptss-admin', plugins_url( "assets/css/admin/posts-data-table-admin.css", $this->plugin->get_file() ), [], $this->plugin->get_version() );
		}
	}

}
