<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Steps\Welcome_Free;

class Welcome extends Welcome_Free {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_id( 'welcome_free' );
		$this->set_title( 'Welcome to Posts Table with Search & Sort' );
		$this->set_name( esc_html__( 'Welcome' ) );
		$this->set_tooltip( false );
		$this->set_description( esc_html__( 'Start creating post tables in no time.' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'welcome_messages' => [
				'type'  => 'heading',
				'label' => esc_html__( 'Use this setup wizard to quickly configure your post tables. You can change these options later on the settings page, or override them in the shortcode for individual tables.' ),
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
