<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\PTS_Lib\Plugin\Plugin;
use Barn2\PTS_Lib\Registerable;
use Barn2\PTS_Lib\Service;
use Barn2\PTS_Lib\Util;

/**
 * Registers the frontend styles and scripts for the post tables.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements Registerable, Service {

	const DATATABLES_VERSION = '1.11.3';

	private $plugin;

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function register() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
	}

	public function register_styles() {
		$suffix = Util::get_script_suffix();

		wp_register_style( 'jquery-datatables-pss', plugins_url( 'assets/js/datatables/datatables.min.css', $this->plugin->get_file() ), [], self::DATATABLES_VERSION );
		wp_register_style( 'posts-data-table', plugins_url( "assets/css/posts-data-table{$suffix}.css", $this->plugin->get_file() ), [ 'jquery-datatables-pss' ], $this->plugin->get_version() );
	}

	public function register_scripts() {
		$suffix = Util::get_script_suffix();

		wp_register_script( 'jquery-datatables-pss', plugins_url( "assets/js/datatables/datatables{$suffix}.js", $this->plugin->get_file() ), [ 'jquery' ], self::DATATABLES_VERSION, true );
		wp_register_script( 'posts-data-table', plugins_url( "assets/js/posts-data-table{$suffix}.js", $this->plugin->get_file() ), [ 'jquery', 'jquery-datatables-pss' ], $this->plugin->get_version(), true );

		$locale            = get_locale();
		$supported_locales = $this->get_supported_locales();

		// Add language file to script if locale is supported (English file is not added as this is the default language)
		if ( array_key_exists( $locale, $supported_locales ) ) {
			wp_localize_script(
				'posts-data-table',
				'posts_data_table',
				[
					'langurl' => $supported_locales[ $locale ]
				]
			);
		}
	}

	/**
	 * Returns an array of locales supported by the plugin.
	 * The array returned uses the locale as the array key mapped to the URL of the corresponding translation file.
	 *
	 * @return array The supported locales
	 */
	private function get_supported_locales() {
		$lang_file_base_url = plugins_url( 'languages/data-tables/', $this->plugin->get_file() );

		return apply_filters(
			'posts_data_table_supported_languages',
			[
				'es_ES' => $lang_file_base_url . 'Spanish.json',
				'fr_FR' => $lang_file_base_url . 'French.json',
				'fr_BE' => $lang_file_base_url . 'French.json',
				'fr_CA' => $lang_file_base_url . 'French.json',
				'de_DE' => $lang_file_base_url . 'German.json',
				'de_CH' => $lang_file_base_url . 'German.json',
				'el'    => $lang_file_base_url . 'Greek.json',
				'el_EL' => $lang_file_base_url . 'Greek.json',
				'zh_TW' => $lang_file_base_url . 'Taiwan.json',
			]
		);
	}

}
