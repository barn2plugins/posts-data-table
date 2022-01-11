<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\Plugin\Posts_Table_Search_Sort\Admin\Settings_Page;
use Barn2\PTS_Lib\Plugin\Simple_Plugin;
use Barn2\PTS_Lib\Registerable;
use Barn2\PTS_Lib\Service_Provider;
use Barn2\PTS_Lib\Util;

/**
 * The main plugin class.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Simple_Plugin implements Registerable, Service_Provider {

	const NAME    = 'Posts Table with Search and Sort';
	const ITEM_ID = 8006;

	/**
	 * @var array $services
	 */
	private $services;

	/**
	 * Constructs and initializes an EDD VAT plugin instance.
	 *
	 * @param string $file    The main plugin __FILE__
	 * @param string $version The current plugin version
	 */
	public function __construct( $file = null, $version = '1.0' ) {
		parent::__construct(
			[
				'id'            => self::ITEM_ID,
				'name'          => self::NAME,
				'version'       => $version,
				'file'          => $file,
				'settings_path' => 'options-general.php?page=' . Settings_Page::MENU_SLUG
			]
		);

		include_once plugin_dir_path( $file ) . 'src/deprecated.php';

		// Services
		$this->services['shortcode'] = new Table_Shortcode();
		$this->services['scripts']   = new Frontend_Scripts( $this );

		// Admin only services
		if ( Util::is_admin() ) {
			$this->services['admin'] = new Admin\Admin_Controller( $this );
		}
	}

	public function register() {
		add_action( 'plugins_loaded', [ $this, 'maybe_load_plugin' ] );
	}

	public function maybe_load_plugin() {
		// Don't load plugin if Pro version active
		if ( class_exists( '\Posts_Table_Pro_Plugin' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'load_textdomain' ] );

		Util::register_services( $this->services );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'posts-data-table', false, $this->get_slug() . '/languages' );
	}

	public function get_service( $id ) {
		if ( isset( $this->services[ $id ] ) ) {
			return $this->services[ $id ];
		}

		return null;
	}

	public function get_services() {
		return $this->services;
	}

}
