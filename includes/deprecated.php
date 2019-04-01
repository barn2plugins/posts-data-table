<?php

/**
 * @deprecated Replaced by \Barn2\Plugin\Posts_Table_Search_Sort\Plugin
 */
class Posts_Data_Table_Plugin {

	public static function instance() {
		_doing_it_wrong( __FUNCTION__, 'Replaced by \Barn2\Plugin\Posts_Table_Search_Sort\Plugin::instance()', '1.2' );

		return \Barn2\Plugin\Posts_Table_Search_Sort\Plugin::instance();
	}
}

/**
 * @deprecated Replaced by \Barn2\Plugin\Posts_Table_Search_Sort\Table_Shortcode
 */
class Posts_Data_Table_Shortcode {

	public static function do_shortcode( $atts, $content = '' ) {
		_doing_it_wrong( __FUNCTION__, 'Replaced by \Barn2\Plugin\Posts_Table_Search_Sort\Table_Shortcode->do_shortcode()', '1.2' );

		$shortcode = new \Barn2\Plugin\Posts_Table_Search_Sort\Table_Shortcode();
		return $shortcode->do_shortcode( $atts, $content );
	}
}

/**
 * @deprecated Replaced by \Barn2\Plugin\Posts_Table_Search_Sort\Simple_Posts_Table
 */
class Posts_Data_Table_Simple extends \Barn2\Plugin\Posts_Table_Search_Sort\Simple_Posts_Table {

}
