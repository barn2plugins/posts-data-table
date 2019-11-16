<?php

namespace Barn2\Lib;

if ( ! \class_exists( __NAMESPACE__ . '\Util' ) ) {

    /**
     * Utility functions for Barn2 plugins.
     *
     * @version 1.4.1
     */
    class Util {

        const EDD_STORE_URL      = 'https://barn2.co.uk';
        const KNOWLEDGE_BASE_URL = 'https://barn2.co.uk/kb';

        public static function format_link( $url, $link_text, $new_tab = false ) {
            return \sprintf( '%1$s%2$s</a>', self::format_link_open( $url, $new_tab ), $link_text );
        }

        public static function format_link_open( $url, $new_tab = false ) {
            $target = $new_tab ? ' target="_blank"' : '';
            return \sprintf( '<a href="%1$s"%2$s>', \esc_url( $url ), $target );
        }

        public static function format_store_url( $path = '' ) {
            return self::EDD_STORE_URL . '/' . \ltrim( $path, ' /' );
        }

        public static function format_store_link( $path, $link_text, $new_tab = true ) {
            return self::format_link( self::format_store_url( $path ), $link_text, $new_tab );
        }

        public static function format_store_link_open( $path, $new_tab = true ) {
            return self::format_link_open( self::format_store_url( $path ), $new_tab );
        }

        public static function get_download_add_to_cart_url( $download_id, $price_id = 0, $discount_code = '' ) {
            $args = array(
                'edd_action'  => 'add_to_cart',
                'download_id' => (int) $download_id
            );
            if ( $price_id ) {
                $args['edd_options[price_id]'] = (int) $price_id;
            }
            if ( $discount_code ) {
                $args['discount'] = $discount_code;
            }

            return self::format_store_url( '?' . \http_build_query( $args ) );
        }

        public static function is_admin() {
            return \is_admin();
        }

        public static function is_front_end() {
            return ( ! \is_admin() || \defined( '\DOING_AJAX' ) ) && ! \defined( '\DOING_CRON' );
        }

        public static function is_woocommerce_active() {
            return \class_exists( '\WooCommerce' );
        }

        public static function is_protected_categories_active() {
            if ( \class_exists( '\WC_Protected_Categories_Plugin' ) ) {
                if ( \method_exists( \WC_Protected_Categories_Plugin::instance(), 'has_valid_license' ) ) {
                    return \WC_Protected_Categories_Plugin::instance()->has_valid_license();
                }
                return true;
            }
            return false;
        }

        public static function is_product_table_active() {
            if ( \class_exists( '\WC_Product_Table_Plugin' ) ) {
                if ( \method_exists( \WC_Product_Table_Plugin::instance(), 'has_valid_license' ) ) {
                    return \WC_Product_Table_Plugin::instance()->has_valid_license();
                }
                return true;
            }
            return false;
        }

        public static function is_quick_view_pro_active() {
            if ( \class_exists( '\Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin' ) ) {
                return \Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin::instance()->has_valid_license();
            }
            return false;
        }

        public static function get_script_suffix() {
            return \defined( '\SCRIPT_DEBUG' ) && \SCRIPT_DEBUG ? '' : '.min';
        }

        /**
         * Formats a HTML link to a path on the Barn2 site.
         *
         * @param string $relative_path The path relative to https://barn2.co.uk.
         * @param string $link_text The link text.
         * @param boolean $new_tab Whether to open the link in a new tab.
         * @return string The hyperlink.
         */
        public static function barn2_link( $relative_path, $link_text = '', $new_tab = false ) {
            if ( empty( $link_text ) ) {
                $link_text = __( 'Read more', 'posts-data-table' );
            }
            $target = $new_tab ? ' target="_blank"' : '';
            $return = \sprintf( '<a href="%1$s"%2$s>%3$s</a>', self::barn2_url( $relative_path ), $target, \esc_html( $link_text ) );
            return $return;
        }

        public static function barn2_url( $relative_path ) {
            return \esc_url( 'https://barn2.co.uk/' . \ltrim( $relative_path, '/' ) );
        }

    }

}