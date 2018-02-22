<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class handles the posts table shortcode registration.
 *
 * @package   Barn2\Posts_Table_Search_And_Sort
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @link      https://barn2.co.uk
 * @copyright 2016-2018 Barn2 Media Ltd
 */
class Posts_Data_Table_Shortcode {

	private $shortcode = 'posts_data_table';

	public function __construct() {
		// Register shortcode
		add_shortcode( $this->shortcode, array( $this, 'shortcode' ) );
	}

	/**
	 * Handles our posts data table shortcode.
	 *
	 * @param array $atts The shortcode attributes specified by the user.
	 * @param string $content The content between the open and close shortcode tags (not used)
	 * @return string The shortcode output
	 */
	public function shortcode( $atts, $content = '' ) {
		// Parse attributes
		$atts = shortcode_atts( Posts_Data_Table_Simple::$default_args, $atts, $this->shortcode );

		// Create table and return output
		$table = new Posts_Data_Table_Simple();
		return $table->get_table( $atts );
	}

}