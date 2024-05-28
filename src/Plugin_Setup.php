<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Starter;
use	Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use	Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin_Activation_Listener;
use	Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Util as Lib_Util;
use	Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use	Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Standard_Service;

/**
 * Plugin Setup
 *
 * @package   Barn2/posts-table-search-sort
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Setup implements Registerable, Standard_Service {
	/**
	 * Plugin's entry file
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Wizard starter.
	 *
	 * @var Starter
	 */
	private $starter;

	/**
	 * Constructor.
	 *
	 * @param mixed $file
	 * @param Plugin $plugin
	 */
	public function __construct( $file, Plugin $plugin ) {
		$this->file    = $file;
		$this->plugin  = $plugin;
		$this->starter = new Starter( $this->plugin );
	}

	/**
	 * Register the service.
	 */
	public function register() {
		register_activation_hook( $this->file, [ $this, 'on_activate' ] );
		add_action( 'admin_init', [ $this, 'after_plugin_activation' ] );
	}

	/**
	 * On activation.
	 *
	 * @param mixed $network_wide
	 */
	public function on_activate( $network_wide ) {
		if ( $this->starter->should_start() ) {
			$this->starter->create_transient();
		}
	}

	/**
	 * Do nothing.
	 *
	 * @param bool $network_wide
	 */
	public function on_deactivate( $network_wide ) {
	}

	/**
	 * Detect the transient and redirect to wizard.
	 *
	 * @return void
	 */
	public function after_plugin_activation() {

		if ( ! $this->starter->detected() ) {
			return;
		}

		$this->starter->delete_transient();
		$this->starter->redirect();
	}
}
