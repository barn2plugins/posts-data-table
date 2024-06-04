<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Steps\Welcome_Free;

/**
 * Layout Settings Step.
 *
 * @package   Barn2\posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Welcome extends Welcome_Free {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'welcome_free' );
		$this->set_title( 'Welcome to Posts Table with Search & Sort' );
		$this->set_name( esc_html__( 'Welcome', 'posts-data-table' ) );
		$this->set_tooltip( false );
		$this->set_description( esc_html__( 'Start creating post tables in no time.', 'posts-data-table' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'welcome_messages' => [
				'type'  => 'heading',
				'label' => esc_html__( 'Use this setup wizard to quickly configure your post tables. You can change these options later on the settings page, or override them in the shortcode for individual tables.', 'posts-data-table' ),
				'size'  => 'p',
				'raw'   => true,
				'style' => [
					'textAlign' => 'center',
					'color'     => '#757575'
				]
			]
		];

		return $fields;
	}

}
