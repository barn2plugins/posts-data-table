<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\Plugin\Posts_Table_Search_Sort\Admin\Settings_Page;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Simple_Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service_Provider;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Util;

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

		$this->add_service( 'plugin_setup', new Plugin_Setup( $this->get_file(), $this ), true );
	}

	public function register() {
		parent::register();
		add_action( 'plugins_loaded', [ $this, 'add_services' ] );

		add_action( 'init', [ $this, 'register_services' ] );
		add_action( 'init', [ $this, 'load_textdomain' ], 5 );
	}

	public function maybe_load_plugin() {
		// Don't load plugin if Pro version active
		if ( Util::is_barn2_plugin_active('\\Barn2\\Plugin\\Posts_Table_Pro\\ptp') ) {
			return;
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'posts-data-table', false, $this->get_slug() . '/languages' );
	}

	public function add_services() {
		if ( Util::is_barn2_plugin_active('\\Barn2\\Plugin\\Posts_Table_Pro\\ptp') ) {
			return;
		}

		$this->add_service( 'shortcode', new Table_Shortcode() );
		$this->add_service( 'scripts', new Frontend_Scripts( $this ) );
		$this->add_service( 'setup_wizard', new Admin\Wizard\Setup_Wizard( $this ) );

		// Admin only services
		if ( Util::is_admin() ) {
			$this->add_service( 'admin', new Admin\Admin_Controller( $this ) );
		}
	}

}
