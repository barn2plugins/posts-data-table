<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Api;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Setup_Wizard\Step;
use Barn2\Plugin\Posts_Table_Search_Sort\Settings;
use Barn2\Plugin\Posts_Table_Search_Sort\Simple_Posts_Table;

/**
 * Search settings step.
 *
 * @package   Barn2\posts-data-table
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Search extends Step {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'search' );
		$this->set_name( esc_html__( 'Search and Sort', 'posts-data-table' ) );
		$this->set_description( esc_html__( 'Next, make it quick and easy for people to find your posts.', 'posts-data-table' ) );
		$this->set_title( esc_html__( 'Search and Sort', 'posts-data-table' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {

		$values = Settings::get_table_args();

		$fields = [
			'sort_by'    => [
				'label'       => __( 'Sort by', 'posts-data-table' ),
				'description' => __( 'The initial sort order applied to the table.', 'posts-data-table' ),
				'type'        => 'select',
				'options'     => $this->get_sort_by(),
				'value'       => $values['sort_by'] ?? 'date',
			],
			'sort_order' => [
				'label'   => __( 'Sort direction', 'posts-data-table' ),
				'type'    => 'select',
				'options' => [
					[
						'value' => '',
						'label' => __( 'Automatic', 'posts-data-table' ),
					],
					[
						'value' => 'asc',
						'label' => __( 'Ascending (A to Z, 1 to 99)', 'posts-data-table' ),
					],
					[
						'value' => 'desc',
						'label' => __( 'Descending (Z to A, 99 to 1)', 'posts-data-table' ),
					],
				],
				'value'   => $values['sort_order'] ?? '',
			],
			'search'     => [
				'label'   => __( 'Search filters', 'posts-data-table' ),
				'type'    => 'select',
				'description'	=>	__( 'Add filter dropdowns above the table to quickly filter the posts by category, tag and more.', 'posts-data-table' ),
				'options' => [
					[
						'value' => '',
						'label' => __( 'Disabled', 'posts-data-table' ),
					],
				],
				'value'   => '',
				'premium' => true,
			],
		];

		return $fields;
	}

	/**
	 * Get formatted list of sort options.
	 *
	 * @return array
	 */
	private function get_sort_by() {

		$available_columns = wp_list_pluck( Simple_Posts_Table::get_column_defaults(), 'heading' );
		$sort_by           = [];

		foreach ( $available_columns as $key => $heading ) {
			$sort_by[] = [
				'value' => $key,
				'label' => $heading,
			];
		}

		return $sort_by;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( array $values ) {

		$sort_by    = $values['sort_by'] ?? 'id';
		$sort_order = $values['sort_order'] ?? '';
		$options    = Settings::get_table_args();

		$options['sort_by']    = $sort_by;
		$options['sort_order'] = $sort_order;

		$options = Settings::sanitize_table_args( $options );

		update_option( Settings::TABLE_ARGS_SETTING, $options );

		return Api::send_success_response();
	}

}
