<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Lib\Util;

/**
 * Registers the frontend styles and scripts for the post tables.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements \Barn2\Lib\Attachable {

	public function attach() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	public function register_styles() {
		$suffix = Util::get_script_suffix();

		wp_enqueue_style( 'jquery-data-tables', plugins_url( 'assets/js/datatables/datatables.min.css', Plugin::FILE ), array(), '1.10.18' );
		wp_enqueue_style( 'posts-data-table', plugins_url( "assets/css/posts-data-table{$suffix}.css", Plugin::FILE ), array( 'jquery-data-tables' ), Plugin::VERSION );
	}

	public function register_scripts() {
		$suffix = Util::get_script_suffix();

		wp_enqueue_script( 'jquery-data-tables', plugins_url( "assets/js/datatables/datatables.min.js", Plugin::FILE ), array( 'jquery' ), '1.10.18', true );
		wp_enqueue_script( 'posts-data-table', plugins_url( "assets/js/posts-data-table{$suffix}.js", Plugin::FILE ), array( 'jquery-data-tables' ), Plugin::VERSION, true );

		$locale				 = get_locale();
		$supported_locales	 = $this->get_supported_locales();

		// Add language file to script if locale is supported (English file is not added as this is the default language)
		if ( array_key_exists( $locale, $supported_locales ) ) {
			wp_localize_script( 'posts-data-table', 'posts_data_table', array(
				'langurl' => $supported_locales[$locale]
			) );
		}
	}

	/**
	 * Returns an array of locales supported by the plugin.
	 * The array returned uses the locale as the array key mapped to the URL of the corresponding translation file.
	 *
	 * @return array The supported locales
	 */
	private function get_supported_locales() {
		$lang_file_base_url = plugins_url( 'languages/data-tables/', Plugin::FILE );

		return apply_filters( 'posts_data_table_supported_languages', array(
			'es_ES' => $lang_file_base_url . 'Spanish.json',
			'fr_FR' => $lang_file_base_url . 'French.json',
			'fr_BE' => $lang_file_base_url . 'French.json',
			'fr_CA' => $lang_file_base_url . 'French.json',
			'de_DE' => $lang_file_base_url . 'German.json',
			'de_CH' => $lang_file_base_url . 'German.json',
			'el' => $lang_file_base_url . 'Greek.json',
			'el_EL' => $lang_file_base_url . 'Greek.json',
			) );
	}
}
