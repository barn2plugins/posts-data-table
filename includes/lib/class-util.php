<?php

namespace Barn2\Lib;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Barn2\Lib\Util' ) ) {

	/**
	 * General utility functions for WordPress.
	 *
	 * @version 1.3
	 */
	class Util {

		public static function is_admin() {
			return is_admin();
		}

		public static function is_front_end() {
			return ( ! is_admin() || defined( '\DOING_AJAX' ) ) && ! defined( '\DOING_CRON' );
		}

		public static function is_woocommerce_active() {
			return class_exists( '\WooCommerce' );
		}

		public static function is_protected_categories_active() {
			if ( class_exists( '\WC_Protected_Categories_Plugin' ) ) {
				if ( method_exists( \WC_Protected_Categories_Plugin::instance(), 'has_valid_license' ) ) {
					return \WC_Protected_Categories_Plugin::instance()->has_valid_license();
				}

				return true;
			}

			return false;
		}

		public static function is_product_table_active() {
			if ( class_exists( '\WC_Product_Table_Plugin' ) ) {
				if ( method_exists( \WC_Product_Table_Plugin::instance(), 'has_valid_license' ) ) {
					return \WC_Product_Table_Plugin::instance()->has_valid_license();
				}

				return true;
			}
			return false;
		}

		public static function is_quick_view_pro_active() {
			if ( class_exists( '\Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin' ) ) {
				return \Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin::instance()->has_valid_license();
			}

			return false;
		}

		public static function get_script_suffix() {
			return defined( '\SCRIPT_DEBUG' ) && \SCRIPT_DEBUG ? '' : '.min';
		}

		public static function barn2_link( $relative_path, $link_text = '', $external = false ) {
			if ( empty( $link_text ) ) {
				$link_text = __( 'Read more', 'posts-data-table' );
			}

			$target	 = $external ? ' target="_blank"' : '';
			$icon	 = $external ? '<span class="dashicons dashicons-external"></span>' : '';

			$return = sprintf( '<a href="%1$s"%2$s>%3$s%4$s</a>', self::barn2_url( $relative_path ), $target, esc_html( $link_text ), $icon );
			return $return;
		}

		public static function barn2_url( $relative_path ) {
			return esc_url( 'https://barn2.co.uk/' . ltrim( $relative_path, '/' ) );
		}

	}

}