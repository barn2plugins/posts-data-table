<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Core_Service;
/**
 * Helper methods to retrieve the plugin properties from its header.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class Plugin_Data implements Core_Service
{
    /**
     * The plugin instance.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * The plugin properties as retrieved from the plugin header.
     *
     * @var array
     */
    private $plugin_data;
    /**
     * Construct the Plugin_Data object.
     *
     * @param Plugin $plugin The plugin instance.
     * @return void
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    /**
     * Get the plugin's main file header.
     *
     * The default property names are referenced here:
     * https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-admin/includes/plugin.php#L75-L86
     *
     * @param string|null $property Optional. The name of a specific property to return.
     * @return array|string The plugin header data or a specific property.
     */
    public function get_plugin_data(string $property = null)
    {
        if (\is_null($this->plugin_data)) {
            if (!\function_exists('get_plugin_data')) {
                require_once \ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $this->plugin_data = \get_plugin_data($this->plugin->get_file(), \false, \false);
        }
        if (!\is_null($property)) {
            return $this->plugin_data[$property] ?? '';
        }
        return $this->plugin_data;
    }
    /**
     * Get the name of this plugin.
     *
     * The name is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'Name' )`.
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_plugin_data('Name');
    }
    /**
     * Get the version of this plugin.
     *
     * The version is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'Version' )`.
     *
     * @return string
     */
    public function get_version()
    {
        return $this->get_plugin_data('Version');
    }
    /**
     * Get the textdomain for this plugin.
     *
     * The textdomain is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'TextDomain' )`.
     *
     * @return string
     */
    public function get_textdomain()
    {
        return $this->get_plugin_data('TextDomain');
    }
    /**
     * Get the textdomain for this plugin.
     *
     * The Update URI is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'UpdateURI' )`.
     *
     * @return string
     */
    public function get_update_uri()
    {
        return $this->get_plugin_data('UpdateURI');
    }
    /**
     * Get the minimum PHP version this plugin supports.
     *
     * The `Requires PHP` version is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'RequiresPHP' )`.
     *
     * @return string
     */
    public function get_required_php()
    {
        return $this->get_plugin_data('RequiresPHP');
    }
    /**
     * Get the minimum WP version this plugin supports.
     *
     * The `Requires at least` version is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'RequiresWP' )`.
     *
     * @return string
     */
    public function get_required_wp()
    {
        return $this->get_plugin_data('RequiresWP');
    }
    /**
     * Get the minimum WooCommerce version this plugin supports.
     *
     * The `WC requires at least` version is retrieved from the plugin's main file header.
     * This method is a shorthand of `get_plugin_data( 'WC requires at least' )`.
     *
     * @return string
     */
    public function get_required_wc()
    {
        return $this->get_plugin_data('WC requires at least');
    }
}
