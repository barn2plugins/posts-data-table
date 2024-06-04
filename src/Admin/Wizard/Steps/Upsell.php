<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Steps\Cross_Selling;

/**
 * Upsell step.
 *
 * @package   Barn2\posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Upsell extends Cross_Selling {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'More', 'posts-data-table' ) );
		$this->set_description( __( 'Enhance your store with these fantastic plugins from Barn2.', 'posts-data-table' ) );
		$this->set_title( esc_html__( 'Extra features', 'posts-data-table' ) );
	}
}
