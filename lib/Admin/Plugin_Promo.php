<?php
namespace Barn2\PTS_Lib\Admin;

use Barn2\PTS_Lib\Registerable,
    Barn2\PTS_Lib\Plugin\Plugin;

/**
 * Provides functions to add the plugin promo to the plugin settings page in the WordPress admin.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 */
class Plugin_Promo implements Registerable {

    private $plugin;
    private $plugin_id;

    public function __construct( Plugin $plugin ) {
        $this->plugin    = $plugin;
        $this->plugin_id = $plugin->get_id();
    }

    public function register() {
        add_action( 'barn2_after_plugin_settings', [ $this, 'render_promo' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'load_styles' ] );
    }

    public function load_styles() {
        wp_enqueue_style( 'barn2-promo', plugins_url( 'lib/assets/css/admin/plugin-promo.min.css', $this->plugin->get_file() ) );
    }

    public function render_promo() {
        $promo_content = $this->get_promo_content();

        if ( ! empty( $promo_content ) ) {
            echo '<div id="barn2_plugin_promo" class="barn2-plugin-promo">' . $promo_content . '</div>';
        }
    }

    private function get_promo_content() {
        if ( ( $promo_content = get_transient( 'barn2_plugin_promo_' . $this->plugin_id ) ) === false ) {
            $promo_response = wp_remote_get( 'https://barn2.co.uk/wp-json/barn2/v2/pluginpromo/' . $this->plugin_id . '?_=' . date( 'mdY' ) );

            if ( wp_remote_retrieve_response_code( $promo_response ) != 200 ) {
                return;
            }

            $promo_content = json_decode( wp_remote_retrieve_body( $promo_response ), true );

            set_transient( 'barn2_plugin_promo_' . $this->plugin_id, $promo_content, DAY_IN_SECONDS );
        }

        if ( empty( $promo_content ) || is_array( $promo_content ) ) {
            return;
        }

        return $promo_content;
    }

}
