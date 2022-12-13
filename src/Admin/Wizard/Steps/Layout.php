<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Api;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Step;
use Barn2\Plugin\Posts_Table_Search_Sort\Settings;
use Barn2\PTS_Lib\Util;

/**
 * Layout Settings Step.
 *
 * @package   Barn2/posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Layout extends Step {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'layout' );
		$this->set_name( esc_html__( 'Layout and Content', 'document-library-lite' ) );
		$this->set_description( esc_html__( 'First, choose what to include in your post tables.', 'document-library-lite' ) );
		$this->set_title( esc_html__( 'Layout and content', 'document-library-lite' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'layout'  => [
				'label'       => __( 'Post type', 'posts-data-table' ),
				'description' => __( 'The default post type for your tables.', 'posts-data-table' ),
				'type'        => 'select',
				'options'     => [
					[
						'value' => 'post',
						'label' => __( 'Post' ),
					],
				],
				'value'       => 'post',
				'premium'     => true,
			],
			'columns' => [
				'label'       => __( 'Columns' ),
				'description' => __( 'List the columns to include in your posts tables.' ) . ' ' . Util::barn2_link( 'kb/list-your-wordpress-blog-posts/#table-columns', esc_html__( 'Read more', 'document-library-lite' ), true ),
				'type'        => 'text',
				'value'       => Settings::get_table_args()['columns'] ?? 'title,content,date,author,categories',
			],
			'length'  => [
				'label'       => __( 'Content length' ),
				'description' => __( 'If you have included a ‘content’ column, then enter the number of characters to appear in the table. Enter -1 to show the full content.' ),
				'type'        => 'number',
				'value'       => Settings::get_table_args()['content_length'] ?? 15,
			],
		];

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {

		$columns = isset( $values['columns'] ) ? $values['columns'] : 'title,content,date,author,categories';
		$length  = isset( $values['length'] ) ? $values['length'] : 15;

		$options                   = Settings::get_table_args();
		$options['columns']        = $columns;
		$options['content_length'] = $length;
		$options                   = Settings::sanitize_table_args( $options );

		update_option( Settings::TABLE_ARGS_SETTING, $options );

		return Api::send_success_response();
	}

}
