<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Api;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Step;
use Barn2\Plugin\Posts_Table_Search_Sort\Settings;
use Barn2\PTS_Lib\Util;

/**
 * Loading step.
 *
 * @package   Barn2/posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Loading extends Step {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'loading' );
		$this->set_name( esc_html__( 'Loading', 'document-library-lite' ) );
		$this->set_description( esc_html__( 'Control how the posts in the table load.', 'document-library-lite' ) );
		$this->set_title( esc_html__( 'Table loading', 'document-library-lite' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'posts_per_page' => [
				'label'       => __( 'Posts per page' ),
				'description' => __( 'The number of posts per page of results. Enter -1 to display all posts on one page.' ),
				'type'        => 'number',
				'value'       => Settings::get_table_args()['rows_per_page'] ?? 20,
			],
			'lazy_load'      => [
				'title'       => __( 'Lazy load' ),
				'label'       => __( 'Load the posts one page at a time' ),
				'description' => __( 'Enable this if you have many posts or experience slow page load times.' ) . ' ' . Util::barn2_link( 'kb/posts-table-lazy-load/', esc_html__( 'Read more', 'document-library-lite' ), true ),
				'type'        => 'checkbox',
				'value'       => false,
				'premium'     => true,
			],
		];

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {

		$options                   = Settings::get_table_args();
		$options['posts_per_page'] = isset( $values['posts_per_page'] ) ? $values['posts_per_page'] : 20;
		$options                   = Settings::sanitize_table_args( $options );

		update_option( Settings::TABLE_ARGS_SETTING, $options );

		return Api::send_success_response();
	}

}
