<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Setup_Wizard as Wizard;
use Barn2\PTS_Lib\Plugin\Plugin;
use Barn2\PTS_Lib\Registerable;

class Setup_Wizard implements Registerable {

	/**
	 * Plugin instance
	 *
	 * @var Plugin
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
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;

		$steps = [
			new Steps\Welcome(),
		];

		$wizard = new Wizard( $this->plugin, $steps );

		$wizard->configure(
			[
				'skip_url' => admin_url( 'edit.php?post_type=product&page=filters&tab=settings' ),
			]
		);

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
