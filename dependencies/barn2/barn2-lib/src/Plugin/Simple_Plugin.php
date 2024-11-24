<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Admin\Notice_Provider;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Service_Container;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Service_Provider;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Util;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\WooCommerce\Compatibility as WooCommerce_Compatibility;
use Exception;
/**
 * Basic implementation of the Plugin interface which stores core data about a
 * WordPress plugin (ID, version number, etc). Data is passed as an array on construction.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   2.0
 * @internal
 */
class Simple_Plugin implements Plugin, Registerable, Service_Provider
{
    use Service_Container;
    protected $data;
    private $basename = null;
    private $dir_path = null;
    private $dir_url = null;
    /**
     * Constructs a new simple plugin with the supplied plugin data.
     *
     * @param array  $data                     {
     *
     * @type int     $id                       (required) The plugin ID. This should be the EDD Download ID.
     * @type string  $name                     (required) The plugin name.
     * @type string  $version                  (required) The plugin version, e.g. '1.2.3'.
     * @type string  $file                     (required) The main plugin __FILE__.
     * @type boolean $is_woocommerce           true if this is a WooCommerce plugin.
     * @type boolean $is_edd                   true if this is an EDD plugin.
     * @type string  $documentation_path       The path to the plugin documentation, relative to https://barn2.com
     * @type string  $settings_path            The plugin settings path, relative to /wp-admin
     * @type array   $wc_features              {
     * @type boolean $custom_order_tables      false if this plugin is not compatible with WooCommerce HPOS. Leave unset otherwise.
     * @type boolean $cart_checkout_blocks     false if this plugin is not compatible with WooCommerce Cart & Checkout Blocks. Leave unset otherwise.
     *                                         }
     *                                         }
     */
    public function __construct(array $data)
    {
        $this->data = \array_merge(['id' => 0, 'name' => '', 'version' => '', 'file' => null, 'is_woocommerce' => \false, 'is_edd' => \false, 'documentation_path' => '', 'settings_path' => '', 'wc_features' => []], $data);
        $this->data['id'] = (int) $this->data['id'];
        $this->data['documentation_path'] = \ltrim($this->data['documentation_path'], '/');
        $this->data['settings_path'] = \ltrim($this->data['settings_path'], '/');
        // Check for 'item_id' in case 'id' not set.
        if (!$this->get_id() && !empty($this->data['item_id'])) {
            $this->data['id'] = (int) $this->data['item_id'];
            unset($this->data['item_id']);
        }
        // WooCommerce plugins cannot be EDD plugins (and vice-versa).
        if ($this->is_edd()) {
            $this->data['is_woocommerce'] = \false;
        } elseif ($this->is_woocommerce()) {
            $this->data['is_edd'] = \false;
        }
        $this->add_service('plugin_data', new Plugin_Data($this));
        $this->add_service('notices', new Notice_Provider($this));
        $this->add_service('i18n', new I18n($this));
        $this->add_service('requirements', new Requirements($this));
        if ($this->is_woocommerce()) {
            $this->add_service('wc_compatibility', new WooCommerce_Compatibility($this));
        }
    }
    public function register()
    {
        $this->register_services();
        $this->start_core_services();
        \add_action('activate_' . $this->get_basename(), [$this, 'before_activate']);
        \add_action('deactivate_' . $this->get_basename(), [$this, 'before_deactivate']);
        \add_action('plugins_loaded', [$this, 'maybe_load_plugin'], 0);
    }
    public function maybe_load_plugin()
    {
        if ($this->requirements()->check()) {
            \add_action('after_setup_theme', [$this, 'start_standard_services']);
        }
    }
    /**
     * Executed on plugin activation.
     *
     * @param boolean $network_wide Whether the plugin is being activated network-wide.
     */
    public final function before_activate($network_wide)
    {
        if (!$this->requirements()->check()) {
            return;
        }
        if (!\get_option($this->get_basename() . '_plugin_activation')) {
            try {
                $this->on_first_activation($network_wide);
                \update_option($this->get_basename() . '_plugin_activation', \time());
            } catch (Exception $e) {
                $this->notices()->add_error_notice($this->get_slug() . '_activation_error', $this->get_name(), \sprintf(__('%1$s could not complete its first activation because the following error occurred:<br>%2$s', 'barn2-lib'), $this->get_name(), $e->getMessage()), ['capability' => 'install_plugins', 'screens' => ['plugins']]);
            }
        }
        foreach ($this->get_services() as $service) {
            if ($service instanceof Plugin_Activation_Listener) {
                $service->on_activate($network_wide);
            }
        }
    }
    /**
     * Executed on plugin deactivation.
     *
     * @param boolean $network_wide Whether the plugin is being deactivated network-wide.
     */
    public final function before_deactivate($network_wide)
    {
        // No requirements check needed on deactivation.
        foreach ($this->get_services() as $service) {
            if ($service instanceof Plugin_Activation_Listener) {
                $service->on_deactivate($network_wide);
            }
        }
    }
    /**
     * Executed only on the very first activation of the plugin.
     *
     * @param boolean $network_wide Whether the plugin is being activated network-wide.
     *
     * @return void
     */
    public function on_first_activation($network_wide)
    {
        // Do nothing by default.
    }
    /**
     * Register a script with WordPress and set the script translations
     *
     * @param string   $handle        The handle the script is registered with.
     * @param string   $relative_path The path to the script file relative to the plugin's root folder.
     * @param string[] $deps          The dependencies for this script.
     * @param string   $version       The version of the script. It defaults to the plugin version.
     * @param bool     $in_footer     Whether to enqueue the script before </body> instead of in the <head>.
     *
     * @return bool                    Whether the script has been registered. True on success, false on failure.
     * @since 1.3
     */
    public function register_script($handle, $relative_path = '', $deps = [], $version = null, $in_footer = \true)
    {
        $registered = \wp_register_script($handle, $this->get_dir_url($relative_path), $deps, $version ?? $this->get_version(), $in_footer);
        if ($registered && \in_array('wp-i18n', $deps, \true)) {
            \wp_set_script_translations($handle, $this->plugin_data()->get_textdomain(), $this->get_dir_path('languages'));
        }
        return $registered;
    }
    /**
     * Get the plugin ID, usually the EDD Download ID.
     *
     * $return int The plugin ID.
     */
    public final function get_id()
    {
        return $this->data['id'];
    }
    /**
     * Get the name of this plugin.
     *
     * @return string The plugin name.
     */
    public final function get_name()
    {
        return $this->plugin_data()->get_name();
    }
    /**
     * Get the plugin version number (e.g. 1.3.2).
     *
     * @return string The version number.
     */
    public final function get_version()
    {
        return $this->plugin_data()->get_version();
    }
    /**
     * Get the full path to the main plugin file.
     *
     * @return string The plugin file.
     */
    public final function get_file()
    {
        return $this->data['file'];
    }
    /**
     * Get the slug for this plugin (e.g. my-plugin).
     *
     * @return string The plugin slug.
     */
    public final function get_slug()
    {
        $dir_path = $this->get_dir_path();
        return !empty($dir_path) ? \basename($dir_path) : '';
    }
    /**
     * Get the 'basename' for the plugin (e.g. my-plugin/my-plugin.php).
     *
     * @return string The plugin basename.
     */
    public final function get_basename()
    {
        if (null === $this->basename) {
            $this->basename = !empty($this->data['file']) ? \plugin_basename($this->data['file']) : '';
        }
        return $this->basename;
    }
    /**
     * Get the full directory path to the plugin folder, with trailing slash (e.g. /wp-content/plugins/my-plugin/).
     *
     * If a relative path is supplied, this will be appended to the plugin directory path.
     *
     * @param string $relative_path Optional. A relative path to append to the plugin directory path.
     *
     * @return string The plugin directory path.
     * @since 1.3.1 Added $relative_path parameter.
     *
     */
    public final function get_dir_path($relative_path = '')
    {
        if (null === $this->dir_path) {
            $this->dir_path = !empty($this->data['file']) ? \plugin_dir_path($this->data['file']) : '';
        }
        return $this->dir_path . \ltrim($relative_path, '/');
    }
    /**
     * Get the URL to the plugin folder.
     *
     * If a relative path is supplied, this will be appended to the plugin directory URL.
     *
     * @param string $relative_path Optional. A relative path to append to the plugin directory path.
     *
     * @return string (URL)
     * @since 1.3.1 Added $relative_path parameter.
     *
     */
    public final function get_dir_url($relative_path = '')
    {
        if (null === $this->dir_url) {
            $this->dir_url = !empty($this->data['file']) ? \plugin_dir_url($this->data['file']) : '';
        }
        return $this->dir_url . \ltrim($relative_path, '/');
    }
    public final function get_woocommerce_features()
    {
        return $this->data['wc_features'];
    }
    /**
     * Is this plugin a WooCommerce extension?
     *
     * @return boolean true if it's a WooCommerce extension.
     */
    public final function is_woocommerce()
    {
        return (bool) $this->data['is_woocommerce'];
    }
    /**
     * Is this plugin an Easy Digital Downloads extension?
     *
     * @return boolean true if it's an EDD extension.
     */
    public final function is_edd()
    {
        return (bool) $this->data['is_edd'];
    }
    /**
     * Get the documentation URL for this plugin.
     *
     * @return string (URL)
     */
    public function get_documentation_url()
    {
        return \esc_url(Util::KNOWLEDGE_BASE_URL . '/' . $this->data['documentation_path']);
    }
    /**
     * Get the support URL for this plugin.
     *
     * @return string (URL)
     */
    public function get_support_url()
    {
        return Util::barn2_url('support-center/');
    }
    /**
     * Get the settings page URL in the WordPress admin.
     *
     * @return string (URL)
     */
    public function get_settings_page_url()
    {
        return !empty($this->data['settings_path']) ? \admin_url($this->data['settings_path']) : '';
    }
    /**
     * Get the design page URL in the WordPress admin.
     *
     * @return string (URL)
     */
    public function get_design_page_url()
    {
        return !empty($this->data['design_path']) ? \admin_url($this->data['design_path']) : '';
    }
    /**
     * Get the plugin data service.
     *
     * @return Plugin_Data
     */
    public final function plugin_data()
    {
        return $this->get_service('plugin_data');
    }
    /**
     * Get the requirements service.
     *
     * @return Requirements
     */
    public final function requirements()
    {
        return $this->get_service('requirements');
    }
    /**
     * Get the notice provider service.
     *
     * @return Notice_Provider
     */
    public final function notices()
    {
        return $this->get_service('notices');
    }
}
