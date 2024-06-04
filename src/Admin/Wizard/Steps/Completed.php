<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Steps\Ready;

/**
 * Completed step.
 *
 * @package   Barn2\posts-data-table
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
		$this->set_name( esc_html__( 'Ready', 'posts-data-table' ) );
		$this->set_title( esc_html__( 'Setup Complete', 'posts-data-table' ) );
		$this->set_description(
			sprintf(
				__( 'You’re all set! Take a look at our <a href="%s" target="_blank">Knowledge Base</a> for further instructions, tutorials, videos, and much more.', 'posts-data-table' ),
				'https://barn2.com/kb/list-your-wordpress-blog-posts/'
			)
		);
	}

}
