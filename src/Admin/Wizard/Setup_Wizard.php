<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Setup_Wizard as Wizard;
use Barn2\PTS_Lib\Plugin\Simple_Plugin;
use Barn2\PTS_Lib\Registerable;

class Setup_Wizard implements Registerable {

	/**
	 * Plugin instance
	 *
	 * @var Simple_Plugin
	 */
	private $plugin;

	/**
	 * Wizard instance
	 *
	 * @var WPF_Setup_Wizard
	 */
	private $wizard;

	/**
	 * Get things started.
	 *
	 * @param Simple_Plugin $plugin
	 */
	public function __construct( Simple_Plugin $plugin ) {
		$this->plugin = $plugin;

		$steps = [];

		$wizard = new Wizard( $this->plugin, $steps );

		$wizard->configure(
			[
				'skip_url'        => admin_url( 'edit.php?post_type=product&page=filters&tab=settings' ),
				'license_tooltip' => esc_html__( 'The licence key is contained in your order confirmation email.', 'woocommerce-product-filters' ),
			]
		);

		$wizard->set_lib_url( wcf()->get_dir_url() . '/dependencies/barn2/setup-wizard/' );
		$wizard->set_lib_path( wcf()->get_dir_path() . '/dependencies/barn2/setup-wizard/' );

		$this->wizard = $wizard;
	}

	/**
	 * Register the service.
	 *
	 * @return void
	 */
	public function register() {
		$this->wizard->boot();
	}

}
