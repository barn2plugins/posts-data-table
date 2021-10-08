<?php
namespace Barn2\Plugin\Posts_Table_Search_Sort;

/**
 * Responsible for fetching and sanitizing the plugin settings.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings {

	const TABLE_ARGS_SETTING = 'ptss_table_args';

	public static function get_table_args() {
		return get_option( self::TABLE_ARGS_SETTING, [] );
	}

	public static function sanitize_table_args( $args ) {
		if ( isset( $args['columns'] ) ) {
			$args['columns'] = sanitize_text_field( $args['columns'] );
		}
		if ( isset( $args['content_length'] ) ) {
			$args['content_length'] = filter_var(
				$args['content_length'],
				FILTER_VALIDATE_INT,
				[
					'options' => [
						'min' => -1
					]
				]
			);
		}
		if ( isset( $args['rows_per_page'] ) ) {
			$args['rows_per_page'] = filter_var(
				$args['rows_per_page'],
				FILTER_VALIDATE_INT,
				[
					'options' => [
						'min' => -1
					]
				]
			);
		}
		if ( isset( $args['sort_by'] ) ) {
			$args['sort_by'] = sanitize_key( $args['sort_by'] );
		}
		if ( isset( $args['sort_order'] ) && ! in_array( $args['sort_order'], [ 'asc', 'desc', '' ] ) ) {
			$args['sort_order'] = '';
		}

		return $args;
	}

}
