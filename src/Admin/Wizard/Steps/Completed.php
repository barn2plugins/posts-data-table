<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Steps\Ready;

/**
 * Completed step.
 *
 * @package   Barn2/posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Completed extends Ready {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();
		$this->set_name( esc_html__( 'Ready', 'woocommerce-product-filters' ) );
		$this->set_title( esc_html__( 'Ready!', 'woocommerce-product-filters' ) );
		$this->set_description(
			''
		);
	}

}
