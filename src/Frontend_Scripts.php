<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\Lib\Util,
    Barn2\Lib\Registerable,
    Barn2\Lib\Plugin\Plugin,
    Barn2\Lib\Service;

/**
 * Registers the frontend styles and scripts for the post tables.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements Registerable, Service {

    private $plugin;

    public function __construct( Plugin $plugin ) {
        $this->plugin = $plugin;
    }

    public function register() {
        \add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
        \add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
    }

    public function register_styles() {
        $suffix = Util::get_script_suffix();

        \wp_enqueue_style( 'jquery-data-tables', \plugins_url( 'assets/js/datatables/datatables.min.css', $this->plugin->get_file() ), array(), '1.10.18' );
        \wp_enqueue_style( 'posts-data-table', \plugins_url( "assets/css/posts-data-table{$suffix}.css", $this->plugin->get_file() ), array( 'jquery-data-tables' ), $this->plugin->get_version() );
    }

    public function register_scripts() {
        $suffix = Util::get_script_suffix();

        \wp_enqueue_script( 'jquery-data-tables', \plugins_url( "assets/js/datatables/datatables.min.js", $this->plugin->get_file() ), array( 'jquery' ), '1.10.18', true );
        \wp_enqueue_script( 'posts-data-table', \plugins_url( "assets/js/posts-data-table{$suffix}.js", $this->plugin->get_file() ), array( 'jquery-data-tables' ), $this->plugin->get_version(), true );

        $locale            = \get_locale();
        $supported_locales = $this->get_supported_locales();

        // Add language file to script if locale is supported (English file is not added as this is the default language)
        if ( \array_key_exists( $locale, $supported_locales ) ) {
            \wp_localize_script( 'posts-data-table', 'posts_data_table', array(
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
        $lang_file_base_url = \plugins_url( 'languages/data-tables/', $this->plugin->get_file() );

        return \apply_filters( 'posts_data_table_supported_languages', array(
            'es_ES' => $lang_file_base_url . 'Spanish.json',
            'fr_FR' => $lang_file_base_url . 'French.json',
            'fr_BE' => $lang_file_base_url . 'French.json',
            'fr_CA' => $lang_file_base_url . 'French.json',
            'de_DE' => $lang_file_base_url . 'German.json',
            'de_CH' => $lang_file_base_url . 'German.json',
            'el'    => $lang_file_base_url . 'Greek.json',
            'el_EL' => $lang_file_base_url . 'Greek.json',
            ) );
    }

}
