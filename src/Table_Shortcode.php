<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\Lib\Registerable,
    Barn2\Lib\Service;

/**
 * This class handles the posts table shortcode registration.
 *
 * @author    Barn2 Plugins <support@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Shortcode implements Registerable, Service {

    const SHORTCODE = 'posts_table';

    public function register() {
        add_shortcode( self::SHORTCODE, array( $this, 'do_shortcode' ) );
        add_shortcode( 'posts_data_table', array( $this, 'do_shortcode' ) ); // back compat: support old shortcode
    }

    /**
     * Handles our posts data table shortcode.
     *
     * @param array $atts The shortcode attributes specified by the user.
     * @param string $content The content between the open and close shortcode tags (not used)
     * @return string The shortcode output
     */
    public function do_shortcode( $atts, $content = '' ) {
        // Parse attributes
        $atts = \shortcode_atts( Simple_Posts_Table::get_defaults(), $atts, self::SHORTCODE );

        // Create table and return output
        $table = new Simple_Posts_Table();
        return $table->get_table( $atts );
    }

}
