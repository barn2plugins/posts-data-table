<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

/**
 * This class is responsible for generating a HTML table from a list of supplied attributes.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Simple_Posts_Table {

	/**
	 * Stores the number of tables on this page. Used to generate the table ID.
	 *
	 * @var int
	 */
	private static $table_count = 1;

	/**
	 * The complete list of table attributes, and their defaults.
	 *
	 * @var array
	 */
	public static $default_args = [
		'columns'         => 'title,content,date,author,categories',
		'rows_per_page'   => 20,
		'sort_by'         => 'date',
		'sort_order'      => '',
		'category'        => '',
		'tag'             => '',
		'author'          => '',
		'post_status'     => '',
		'date_format'     => 'Y/m/d',
		'search_on_click' => true,
		'wrap'            => true,
		'content_length'  => 15,
		'scroll_offset'   => 15
	];

	/**
	 * An array of all possible columns and their default heading, priority, and column width.
	 *
	 * @var array
	 */
	private static $column_defaults = [];

	/**
	 * An array of all allowed column keys.
	 *
	 * @var array
	 */
	private static $allowed_columns = [];

	public static function get_defaults() {
		return wp_parse_args( Settings::get_table_args(), self::$default_args );
	}

	public static function get_column_defaults() {
		if ( empty( self::$column_defaults ) ) {
			/**
			 * Priority values are used to determine visiblity at small screen sizes (1 = highest priority, 6 = lowest priority).
			 * Column widths are automatically calculated by DataTables, but can be overridden by using filter 'posts_data_table_column_defaults'.
			 */
			self::$column_defaults = [
				'id'         => [
					'heading'  => __( 'ID', 'posts-data-table' ),
					'priority' => 3,
					'width'    => ''
				],
				'image'      => [
					'heading'  => __( 'Image', 'posts-data-table' ),
					'priority' => 6,
					'width'    => ''
				],
				'title'      => [
					'heading'  => __( 'Title', 'posts-data-table' ),
					'priority' => 1,
					'width'    => ''
				],
				'categories' => [
					'heading'  => __( 'Categories', 'posts-data-table' ),
					'priority' => 7,
					'width'    => ''
				],
				'tags'       => [
					'heading'  => __( 'Tags', 'posts-data-table' ),
					'priority' => 8,
					'width'    => ''
				],
				'date'       => [
					'heading'  => __( 'Date', 'posts-data-table' ),
					'priority' => 2,
					'width'    => ''
				],
				'author'     => [
					'heading'  => __( 'Author', 'posts-data-table' ),
					'priority' => 4,
					'width'    => ''
				],
				'content'    => [
					'heading'  => __( 'Content', 'posts-data-table' ),
					'priority' => 5,
					'width'    => ''
				]
			];
		}

		return self::$column_defaults;
	}

	public static function get_allowed_columns() {
		if ( empty( self::$allowed_columns ) ) {
			self::$allowed_columns = array_keys( self::get_column_defaults() );
		}

		return self::$allowed_columns;
	}

	/**
	 * Retrieves a data table containing a list of posts based on the specified arguments.
	 *
	 * @param array $args An array of options used to display the posts table
	 * @return string The posts table HTML output
	 */
	public function get_table( $args ) {
		$args = wp_parse_args( $args, self::get_defaults() );
		$args = $this->back_compat_args( $args );

		if ( empty( $args['columns'] ) ) {
			$args['columns'] = self::$default_args['columns'];
		}

		// Get the columns to be used in this table
		$columns = array_filter( array_map( 'trim', explode( ',', strtolower( $args['columns'] ) ) ) );
		$columns = array_intersect( $columns, self::get_allowed_columns() );

		if ( empty( $columns ) ) {
			$columns = explode( ',', self::$default_args['columns'] );
		}

		$args['rows_per_page'] = filter_var( $args['rows_per_page'], FILTER_VALIDATE_INT );

		if ( $args['rows_per_page'] < 1 || ! $args['rows_per_page'] ) {
			$args['rows_per_page'] = -1;
		}

		if ( ! in_array( $args['sort_by'], self::get_allowed_columns() ) ) {
			$args['sort_by'] = self::$default_args['sort_by'];
		}

		if ( ! in_array( $args['sort_order'], [ 'asc', 'desc' ] ) ) {
			$args['sort_order'] = self::$default_args['sort_order'];
		}

		// Set default sort direction
		if ( ! $args['sort_order'] ) {
			if ( $args['sort_by'] === 'date' ) {
				$args['sort_order'] = 'desc';
			} else {
				$args['sort_order'] = 'asc';
			}
		}

		$args['search_on_click'] = filter_var( $args['search_on_click'], FILTER_VALIDATE_BOOLEAN );
		$args['wrap']            = filter_var( $args['wrap'], FILTER_VALIDATE_BOOLEAN );
		$args['content_length']  = filter_var( $args['content_length'], FILTER_VALIDATE_INT );
		$args['scroll_offset']   = filter_var( $args['scroll_offset'], FILTER_VALIDATE_INT );

		if ( empty( $args['date_format'] ) ) {
			$args['date_format'] = self::$default_args['date_format'];
		}

		$output       = '';
		$table_head   = '';
		$table_body   = '';
		$body_row_fmt = '';

		// Start building the args needed for our posts query
		$post_args = [
			'post_type'        => 'post',
			'posts_per_page'   => apply_filters( 'posts_data_table_post_limit', 1000 ),
			'post_status'      => 'publish',
			'suppress_filters' => false // Ensure WPML filters run on this query
		];

		if ( ! empty( $args['category'] ) ) {
			if ( is_numeric( $args['category'] ) ) {
				$post_args['cat'] = $args['category'];
			} else {
				$category = get_category_by_slug( $args['category'] );

				if ( $category ) {
					$post_args['category_name'] = $category->slug;
				}
			}
		}

		if ( ! empty( $args['tag'] ) ) {
			if ( is_numeric( $args['tag'] ) ) {
				$post_args['tag_id'] = $args['tag'];
			} else {
				$post_args['tag'] = $args['tag'];
			}
		}

		if ( ! empty( $args['author'] ) ) {
			$author = $args['author'];

			if ( is_numeric( $author ) || false !== strpos( $author, ',' ) ) {
				$post_args['author'] = $author;
			} else {
				$post_args['author_name'] = $author;
			}
		}

		if ( ! empty( $args['post_status'] ) ) {
			$post_args['post_status'] = $args['post_status'];
		}

		// Get all published posts in the current language
		$all_posts = get_posts( apply_filters( 'posts_data_table_query_args', $post_args, $args ) );

		// Bail early if no posts found
		if ( ! $all_posts || ! is_array( $all_posts ) ) {
			return $output;
		}

		$hidden_columns = [];

		// Set hidden columns and sort indexes
		$table_sort_index = array_search( $args['sort_by'], $columns );
		$date_sort_index  = false;
		$hidden_date      = in_array( 'date', $columns ) || 'date' === $args['sort_by'];

		if ( $hidden_date ) {
			$hidden_columns[] = 'timestamp';
			$date_sort_index  = count( $columns );

			// If we're sorting by date, make sure we use this hidden column for the initial table sort
			if ( 'date' === $args['sort_by'] && false === $table_sort_index ) {
				$table_sort_index = $date_sort_index;
			}
		}

		if ( false === $table_sort_index ) {
			// Sort column is not in list of displayed columns so we'll add it as a hidden column at end of table
			$hidden_columns[] = $args['sort_by'];

			// Set the table sort index to the index of the hidden sort column
			$table_sort_index = $date_sort_index ? $date_sort_index + 1 : \count( $columns );
		}

		// Allow theme/plugins to override defaults
		$column_defaults = apply_filters( 'posts_data_table_column_defaults_' . self::$table_count, apply_filters( 'posts_data_table_column_defaults', self::get_column_defaults() ) );

		// Load the scripts and styles.
		if ( apply_filters( 'posts_data_table_load_scripts', true ) ) {
			wp_enqueue_style( 'posts-data-table' );

			wp_localize_script(
				'posts-data-table',
				'configOptions',
				[
					'lengthMenu' => json_encode( $this->get_page_lengths( $args['rows_per_page'] ) ),
					'displayLength' => $args['rows_per_page']
				]
			);

			wp_enqueue_script( 'posts-data-table' );
		}

		// Build table header
		$heading_fmt = '<th data-name="%1$s" data-priority="%2$u" data-width="%3$s"%5$s>%4$s</th>';
		$cell_fmt    = '<td>{%s}</td>';

		foreach ( $columns as $column ) {
			// Double-check column name is valid
			if ( ! in_array( $column, self::get_allowed_columns() ) ) {
				continue;
			}

			// Do we need to use custom data for ordering this column?
			$order_data = '';

			if ( 'date' === $column && false !== $date_sort_index ) {
				$order_data = sprintf( ' data-order-data="%u"', $date_sort_index );
			}

			// Add heading to table
			$table_head .= sprintf( $heading_fmt, $column, $column_defaults[ $column ]['priority'], $column_defaults[ $column ]['width'], $column_defaults[ $column ]['heading'], $order_data );

			// Add placeholder to table body format string so that content for this column is included in table output
			$body_row_fmt .= sprintf( $cell_fmt, $column );
		}

		foreach ( $hidden_columns as $column ) {
			$table_head .= sprintf( '<th data-name="%s" data-visible="false"></th>', $column );

			// Make sure data for the hidden column is included in table content
			$body_row_fmt .= sprintf( $cell_fmt, $column );
		}

		$table_head = sprintf( '<thead><tr>%s</tr></thead>', $table_head );

		// Build table body
		$body_row_fmt = '<tr>' . $body_row_fmt . '</tr>';

		// Loop through posts and add a row for each
		foreach ( (array) $all_posts as $_post ) {
			setup_postdata( $_post );

			// Format title
			$title = sprintf( '<a href="%1$s">%2$s</a>', get_permalink( $_post ), get_the_title( $_post ) );

			// Format author
			$author = sprintf(
				'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
				esc_url( get_author_posts_url( $_post->post_author ) ),
				/* translators: %s: the author's name */
				esc_attr( sprintf( __( 'Posts by %s', 'posts-data-table' ), get_the_author() ) ),
				get_the_author()
			);

			$post_data_trans = apply_filters(
				'posts_data_table_row_data_format',
				[
					'{id}'         => $_post->ID,
					'{image}'      => get_the_post_thumbnail( $_post, apply_filters( 'posts_data_table_image_size', 'thumbnail' ) ),
					'{title}'      => $title,
					'{categories}' => get_the_category_list( ', ', '', $_post->ID ),
					'{tags}'       => get_the_tag_list( '', ', ', '', $_post->ID ),
					'{date}'       => get_the_date( $args['date_format'], $_post ),
					'{author}'     => $author,
					'{content}'    => $this->get_post_content( $args['content_length'] ),
					'{timestamp}'  => $_post->post_date
				]
			);

			$table_body .= strtr( $body_row_fmt, $post_data_trans );
		} // foreach post

		wp_reset_postdata();

		$table_body = sprintf( '<tbody>%s</tbody>', $table_body );

		$paging_attr = 'false';

		if ( $args['rows_per_page'] && $args['rows_per_page'] < count( $all_posts ) ) {
			$paging_attr = 'true';
		}

		$order_attr = '';

		if ( false !== $table_sort_index ) {
			// Order attribute should be escaped here rather than in sprintf below as we don't want to escape the double-quotes around "asc" or "desc"
			$order_attr = sprintf( '[[%u, "%s"]]', esc_attr( $table_sort_index ), esc_attr( $args['sort_order'] ) );
		}

		$offset_attr = ( $args['scroll_offset'] === false ) ? 'false' : $args['scroll_offset'];
		$table_class = 'posts-data-table';

		if ( ! $args['wrap'] ) {
			$table_class .= ' nowrap';
		}

		$table_attributes = sprintf(
			'id="posts-table-%1$u" class="%2$s" data-page-length="%3$u" data-paging="%4$s" data-order=\'%5$s\' data-click-filter="%6$s" data-scroll-offset="%7$s" cellspacing="0" width="100%%"',
			self::$table_count,
			esc_attr( $table_class ),
			esc_attr( $args['rows_per_page'] ),
			esc_attr( $paging_attr ),
			$order_attr, // escaped above
			esc_attr( $args['search_on_click'] ? 'true' : 'false' ),
			esc_attr( $offset_attr )
		);

		$output = sprintf( '<table %1$s>%2$s%3$s</table>', $table_attributes, $table_head, $table_body );

		// Increment the table count
		self::$table_count++;

		return apply_filters( 'posts_data_table_html_output', $output, $args );
	}

	private function back_compat_args( $args ) {
		if ( ! empty( $args['columns'] ) ) {
			$columns = array_map( 'trim', explode( ',', $args['columns'] ) );

			if ( false !== ( $index = array_search( 'category', $columns, true ) ) ) {
				$columns[ $index ] = 'categories';
			}

			$args['columns'] = implode( ',', $columns );
		}

		if ( ! empty( $args['sort_by'] ) && 'category' === $args['sort_by'] ) {
			$args['sort_by'] = 'categories';
		}

		return $args;
	}

	/**
	 * Retrieve the post content, truncated to the number of words specified by $num_words.
	 *
	 * Must be called with the Loop or a secondary loop after a call to setup_postdata().
	 *
	 * @param int $num_words The number of words to trim the content to
	 * @return string The (truncated) post content
	 */
	private function get_post_content( $num_words = 15 ) {
		$text = get_the_content( '' );
		$text = strip_shortcodes( $text );
		$text = apply_filters( 'the_content', $text );

		if ( $num_words > 0 ) {
			$text = wp_trim_words( $text, $num_words, ' &hellip;' );
		}

		return $text;
	}

	public function get_page_lengths( $default_length ) {
		$length_numbers = apply_filters( 'posts_data_table_default_page_lengths', [ 10, 25, 50, 100 ] );

		if ( $default_length != -1 && !in_array( $default_length, $length_numbers ) ) {
			$length_numbers[] = $default_length;
		}

		sort( $length_numbers );

		$lengths = [
			array_merge( $length_numbers, [ -1 ] ),
			array_merge( $length_numbers, [ __( 'All', 'posts-data-table' ) ] )
		];

		return $lengths;
	}

}
