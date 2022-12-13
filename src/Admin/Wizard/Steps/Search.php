<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Admin\Wizard\Steps;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Api;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Barn2\Setup_Wizard\Step;
use Barn2\Plugin\Posts_Table_Search_Sort\Simple_Posts_Table;

class Search extends Step {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->set_id( 'search' );
		$this->set_name( esc_html__( 'Search and Sort', 'document-library-lite' ) );
		$this->set_description( esc_html__( 'Next, make it quick and easy for people to find your posts.', 'document-library-lite' ) );
		$this->set_title( esc_html__( 'Search and Sort', 'document-library-lite' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setup_fields() {
		$fields = [
			'sort_by' => [
				'label'       => __( 'Sort by', 'posts-data-table' ),
				'description' => __( 'The initial sort order applied to the table.', 'posts-data-table' ),
				'type'        => 'select',
				'options'     => $this->get_sort_by(),
				'value'       => 'id',
			],
		];

		return $fields;
	}

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
		return Api::send_success_response();
	}

}
