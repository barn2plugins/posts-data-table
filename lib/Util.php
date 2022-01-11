<?php

namespace Barn2\PTS_Lib;

use Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin,
	WC_Product_Table_Plugin,
	WC_Protected_Categories_Plugin,
	Barn2\PTS_Lib\Plugin\Plugin;
use function Barn2\Plugin\WC_Product_Table\wpt;
use function Barn2\Plugin\WC_Protected_Categories\wpc;
use function Barn2\Plugin\WC_Quick_View_Pro\wqv;
use function Barn2\Plugin\WC_Restaurant_Ordering\wro;

/**
 * Utility functions for Barn2 plugins.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.5.1
 */
class Util {

	const BARN2_URL          = 'https://barn2.com';
	const EDD_STORE_URL      = 'https://barn2.com';
	const KNOWLEDGE_BASE_URL = 'https://barn2.com';

	/**
	 * Formats a HTML link to a path on the Barn2 site.
	 *
	 * @param string $relative_path The path relative to https://barn2.com.
	 * @param string $link_text The link text.
	 * @param boolean $new_tab Whether to open the link in a new tab.
	 * @return string The hyperlink.
	 */
	public static function barn2_link( $relative_path, $link_text = '', $new_tab = false ) {
		if ( empty( $link_text ) ) {
			$link_text = __( 'Read more', 'posts-data-table' );
		}
		return self::format_link( self::barn2_url( $relative_path ), esc_html( $link_text ), $new_tab );
	}

	public static function barn2_url( $relative_path ) {
		return esc_url( trailingslashit( self::BARN2_URL ) . ltrim( $relative_path, '/' ) );
	}

	public static function format_barn2_link_open( $relative_path, $new_tab = false ) {
		return self::format_link_open( self::barn2_url( $relative_path ), $new_tab );
	}

	public static function format_link( $url, $link_text, $new_tab = false ) {
		return sprintf( '%1$s%2$s</a>', self::format_link_open( $url, $new_tab ), $link_text );
	}

	public static function format_link_open( $url, $new_tab = false ) {
		$target = $new_tab ? ' target="_blank"' : '';
		return sprintf( '<a href="%1$s"%2$s>', esc_url( $url ), $target );
	}

	public static function store_url( $relative_path ) {
		return self::EDD_STORE_URL . '/' . ltrim( $relative_path, ' /' );
	}

	public static function format_store_link( $relative_path, $link_text, $new_tab = true ) {
		return self::format_link( self::store_url( $relative_path ), $link_text, $new_tab );
	}

	public static function format_store_link_open( $relative_path, $new_tab = true ) {
		return self::format_link_open( self::store_url( $relative_path ), $new_tab );
	}

	public static function get_add_to_cart_url( $download_id, $price_id = 0, $discount_code = '' ) {
		$args = [
			'edd_action'  => 'add_to_cart',
			'download_id' => (int) $download_id
		];
		if ( $price_id ) {
			$args['edd_options[price_id]'] = (int) $price_id;
		}
		if ( $discount_code ) {
			$args['discount'] = $discount_code;
		}

		return self::store_url( '?' . http_build_query( $args ) );
	}

	public static function is_admin() {
		return is_admin();
	}

	public static function is_front_end() {
		return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
	}

	public static function is_woocommerce_active() {
		return class_exists( '\WooCommerce' );
	}

	public static function is_product_addons_active() {
		return class_exists( '\WC_Product_Addons' );
	}

	public static function is_edd_active() {
		return class_exists( '\Easy_Digital_Downloads' );
	}

	public static function is_acf_active() {
		return class_exists( '\ACF' );
	}

	public static function is_protected_categories_active() {
		if ( function_exists( '\Barn2\Plugin\WC_Protected_Categories\wpc' ) ) {
			return wpc()->has_valid_license();
		} else {
			if ( class_exists( '\WC_Protected_Categories_Plugin' ) ) {
				if ( method_exists( WC_Protected_Categories_Plugin::instance(), 'has_valid_license' ) ) {
					return WC_Protected_Categories_Plugin::instance()->has_valid_license();
				}
				return true;
			}
		}
		return false;
	}

	public static function is_product_table_active() {
		if ( function_exists( '\Barn2\Plugin\WC_Product_Table\wpt' ) ) {
			return wpt()->has_valid_license();
		} elseif ( class_exists( '\WC_Product_Table_Plugin' ) ) {
			if ( method_exists( WC_Product_Table_Plugin::instance(), 'has_valid_license' ) ) {
				return WC_Product_Table_Plugin::instance()->has_valid_license();
			}
			return true;
		}
		return false;
	}

	public static function is_quick_view_pro_active() {
		if ( function_exists( '\Barn2\Plugin\WC_Quick_View_Pro\wqv' ) ) {
			return wqv()->has_valid_license();
		} else {
			if ( class_exists( '\Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Plugin' ) ) {
				return Quick_View_Plugin::instance()->has_valid_license();
			}
			return false;
		}
	}

	public static function is_restaurant_ordering_active() {
		if ( function_exists( '\Barn2\Plugin\WC_Restaurant_Ordering\wro' ) ) {
			return wro()->has_valid_license();
		}

		return false;
	}

	public static function get_script_suffix() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}

	public static function register_services( $services ) {
		array_map(
			function ( $service ) {
				if ( ( $service instanceof Conditional ) && ! $service->is_required() ) {
					  return;
				}
				if ( $service instanceof Registerable ) {
					$service->register();
				}
				if ( $service instanceof Schedulable ) {
					$service->schedule();
				}
			},
			$services
		);
	}

	/**
	 * @deprecated 1.5 Renamed store_url
	 */
	public static function format_store_url( $relative_path ) {
		return self::store_url( $relative_path );
	}

	/**
	 * Retrieves an array of internal WP dependencies for bundled JS files.
	 *
	 * @param Barn2\PTS_Lib\Plugin $plugin
	 * @param string $filename
	 * @return array
	 */
	public static function get_script_dependencies( $plugin, $filename ) {
		$script_dependencies_file = $plugin->get_dir_path() . 'assets/js/wp-dependencies.json';
		$script_dependencies      = file_exists( $script_dependencies_file ) ? file_get_contents( $script_dependencies_file ) : false;

		if ( $script_dependencies === false ) {
			return [];
		}

		$script_dependencies = json_decode( $script_dependencies, true );

		if ( ! isset( $script_dependencies[ $filename ] ) ) {
			return [];
		}

		return $script_dependencies[ $filename ];
	}
}
