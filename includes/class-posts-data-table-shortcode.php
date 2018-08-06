<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class handles the posts table shortcode registration.
 *
 * @package   Posts_Table_Search_And_Sort
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Data_Table_Shortcode {

	const SHORTCODE = 'posts_data_table';

	public static function register_shortcode() {
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'do_shortcode' ) );
	}

	/**
	 * Handles our posts data table shortcode.
	 *
	 * @param array $atts The shortcode attributes specified by the user.
	 * @param string $content The content between the open and close shortcode tags (not used)
	 * @return string The shortcode output
	 */
	public static function do_shortcode( $atts, $content = '' ) {
		// Parse attributes
		$atts = shortcode_atts( Posts_Data_Table_Simple::$default_args, $atts, self::SHORTCODE );

		// Create table and return output
		$table = new Posts_Data_Table_Simple();
		return $table->get_table( $atts );
	}

}