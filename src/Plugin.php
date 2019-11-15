<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort;

use Barn2\Lib\Service_Provider,
    Barn2\Lib\Registerable,
    Barn2\Lib\Plugin\Simple_Plugin,
    Barn2\Lib\Util;

/**
 * The main plugin class.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Simple_Plugin implements Registerable, Service_Provider {

    const NAME = 'Posts Table with Search and Sort';

    /**
     * @var array $services
     */
    private $services;

    /**
     * Constructs and initalizes an EDD VAT plugin instance.
     *
     * @param string $file The main plugin __FILE__
     * @param string $version The current plugin version
     */
    public function __construct( $file = null, $version = '1.0' ) {
        parent::__construct(
            array(
                'name'    => self::NAME,
                'version' => $version,
                'file'    => $file
        ) );

        require_once plugin_dir_path( $file ) . 'lib/class-wp-settings-api-helper.php';
        include_once plugin_dir_path( $file ) . 'src/deprecated.php';

        // Services
        $this->services['shortcode'] = new Table_Shortcode();
        $this->services['scripts']   = new Frontend_Scripts( $this );

        // Admin only services
        if ( Util::is_admin() ) {
            $this->services['admin'] = new Admin\Admin_Controller( $this );
        }
    }

    public function register() {
        \add_action( 'plugins_loaded', array( $this, 'maybe_load_plugin' ) );
    }

    public function maybe_load_plugin() {
        // Don't load plugin if Pro version active
        if ( \class_exists( '\Posts_Table_Pro_Plugin' ) ) {
            return;
        }

        \add_action( 'init', array( $this, 'load_textdomain' ) );

        \array_map( function( $service ) {
            if ( $service instanceof Registerable ) {
                $service->register();
            }
        }, $this->services );
    }

    public function get_service( $id ) {
        if ( isset( $this->services[$id] ) ) {
            return $this->services[$id];
        }
        return null;
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'posts-data-table', false, \dirname( $this->get_basename() ) . '/languages' );
    }

}
