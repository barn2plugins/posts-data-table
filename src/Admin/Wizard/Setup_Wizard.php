<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Setup_Wizard as Wizard;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Standard_Service;

/**
 * Setup wizard service.
 *
 * @package   Barn2\posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Setup_Wizard implements Registerable, Standard_Service {

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Wizard instance
	 *
	 * @var Wizard
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
			new Steps\Layout(),
			new Steps\Loading(),
			new Steps\Search(),
			new Steps\Upsell(),
			new Steps\Completed()
		];

		$wizard = new Wizard( $this->plugin, $steps );

		$wizard->configure(
			[
				'skip_url'    => admin_url( 'options-general.php?page=posts_table_search_sort' ),
				'plugin_slug' => 'posts-table-with-search-and-sort',
				'signpost'        => [
					[
						'title' => __( 'Go to settings page', 'posts-data-table' ),
						'href'  => admin_url( 'options-general.php?page=posts_table_search_sort' ),
					],
				]
			]
		);

		$wizard->add_restart_link( '', '' );

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
