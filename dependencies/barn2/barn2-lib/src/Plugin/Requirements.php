<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Core_Service;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Util;
/**
 * Helper methods to check for WP, WC and EDD requirements.
 *
 * This trait MUST be used together with the Notice_Provider trait.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class Requirements implements Core_Service
{
    /**
     * The plugin instance.
     *
     * @var Simple_Plugin
     */
    private $plugin;
    /**
     * The plugin requirements.
     *
     * @var array
     */
    private $requirements;
    /**
     * Construct the Requirements object.
     *
     * @param Plugin $plugin The plugin instance.
     * @return void
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    /**
     * Get the plugin's requirements.
     *
     * @return array
     */
    public function get_all()
    {
        if (\is_null($this->requirements)) {
            $this->requirements = ['php' => $this->plugin->plugin_data()->get_required_php(), 'wp' => $this->plugin->plugin_data()->get_required_wp()];
            if ($this->plugin->is_woocommerce()) {
                $this->requirements['wc'] = $this->plugin->plugin_data()->get_required_wc();
            }
        }
        return $this->requirements;
    }
    /**
     * Get a specific requirement.
     *
     * @param string $requirement_id The requirement ID.
     * @return string|null
     */
    public final function get(string $requirement_id)
    {
        $requirements = $this->get_all();
        return $requirements[$requirement_id] ?? null;
    }
    /**
     * Check if all the requirements are met.
     *
     * @param bool $add_notice true to add an error notice if the requirements are not met.
     * @return bool true if all requirements are met.
     */
    public final function check(bool $add_notice = \true) : bool
    {
        if (!$this->check_php($add_notice)) {
            return \false;
        }
        if (!$this->check_wp($add_notice)) {
            return \false;
        }
        switch (\true) {
            case $this->plugin->is_woocommerce():
                return $this->check_wc($add_notice);
            case $this->plugin->is_edd():
                return $this->check_edd($add_notice);
            default:
                return \true;
        }
    }
    /**
     * Check if the PHP requirements are met.
     *
     * @param bool $add_notice true to add an error notice if the requirements are not met.
     * @return bool true if the requirements are met.
     */
    public final function check_php(bool $add_notice = \true) : bool
    {
        $required_php = $this->get('php');
        if (\version_compare(\PHP_VERSION, $required_php, '<')) {
            if (\defined('WP_CLI') && \WP_CLI) {
                \wp_die(\sprintf(
                    /* translators: %1$s: Plugin name. %2$s: PHP version required. */
                    __('%1$s requires PHP %2$s or greater. Please update the PHP version used by your web server.', 'barn2-lib'),
                    $this->plugin->get_name(),
                    $required_php
                ));
            }
            if (\is_admin()) {
                if ($add_notice) {
                    $this->plugin->notices()->add_error_notice($this->plugin->get_slug() . '_invalid_php_version', '', \sprintf(
                        /* translators: %1$s: Plugin name. %2$s: PHP version required. */
                        __('%1$s requires PHP %2$s or greater. Please update the PHP version used by your web server.', 'barn2-lib'),
                        $this->plugin->get_name(),
                        $required_php
                    ));
                }
            }
            return \false;
        }
        return \true;
    }
    /**
     * Check if the WP requirements are met.
     *
     * @param bool $add_notice true to add an error notice if the requirements are not met.
     * @return bool true if the requirements are met.
     */
    public final function check_wp(bool $add_notice = \true) : bool
    {
        global $wp_version;
        $required_wp = $this->get('wp');
        if (\version_compare($wp_version, $required_wp, '<')) {
            if (\defined('WP_CLI') && \WP_CLI) {
                \wp_die(\sprintf(
                    /* translators: %1$s: Plugin name. %2$s: WP version required. */
                    __('%1$s requires WordPress %2$s or greater. Please update your WordPress installation.', 'barn2-lib'),
                    $this->plugin->get_name(),
                    $required_wp
                ));
            }
            if (\is_admin()) {
                if ($add_notice) {
                    $can_update_core = \current_user_can('update_core');
                    $this->plugin->notices()->add_error_notice($this->plugin->get_slug() . '_invalid_wp_version', '', \sprintf(
                        /* translators: %1$s: Plugin name. %2$s: Update Core <a> tag open. %3$s: <a> tag close. %4$s: WP version required. */
                        __('%1$s requires WordPress %4$s or greater. Please %2$supdate%3$s your WordPress installation.'),
                        $this->plugin->get_name(),
                        $can_update_core ? \sprintf('<a href="%s">', \esc_url(\self_admin_url('update-core.php'))) : '',
                        $can_update_core ? '</a>' : '',
                        $required_wp
                    ));
                }
            }
            return \false;
        }
        return \true;
    }
    /**
     * Check if the WooCommerce requirements are met.
     *
     * @param bool $add_notice true to add an error notice if the requirements are not met.
     * @return bool true if the requirements are met.
     */
    public final function check_wc(bool $add_notice = \true) : bool
    {
        if (!\class_exists('WooCommerce')) {
            if (\defined('WP_CLI') && \WP_CLI) {
                /* translators: %1$s: Plugin name. */
                \wp_die(\sprintf(__('%1$s requires WooCommerce to be installed and active.', 'barn2-lib'), $this->plugin->get_name()));
            }
            if (\is_admin()) {
                if ($add_notice) {
                    $this->plugin->notices()->add_error_notice($this->plugin->get_slug() . '_woocommerce_missing', '', \sprintf(
                        /* translators: %1$s: Plugin name. %2$s: WooCommerce link. %3$s Install/Upgrade/Activate WooCommerce link. */
                        __('%1$s requires %2$s to be installed and active.%3$s', 'barn2-lib'),
                        $this->plugin->get_name(),
                        Util::get_plugin_link('WooCommerce', 'woocommerce'),
                        Util::get_plugin_install_activate_upgrade_link('WooCommerce', 'woocommerce', 'woocommerce/woocommerce.php')
                    ));
                }
            }
            return \false;
        }
        global $woocommerce;
        $required_wc = $this->get('wc');
        if (\version_compare($woocommerce->version, $required_wc, '<')) {
            if (\defined('WP_CLI') && \WP_CLI) {
                /* translators: %1$s: Plugin name. %2$s: WooCommerce version required. */
                \wp_die(\sprintf(__('%1$s requires WooCommerce %2$s or greater. Please update your WooCommerce setup first.'), $this->plugin->get_name(), $required_wc));
            }
            if (\is_admin()) {
                if ($add_notice) {
                    $this->plugin->notices()->add_error_notice($this->plugin->get_slug() . '_invalid_woocommerce_version', '', \sprintf(
                        /* translators: %1$s: Plugin name. %2$s: WooCommerce link. %3$s WooCommerce version required. %4$s Install/Upgrade/Activate WooCommerce link. */
                        __('%1$s requires %2$s %3$s or greater. Please %4$s your WooCommerce setup first.'),
                        $this->plugin->get_name(),
                        Util::get_plugin_link('WooCommerce', 'woocommerce'),
                        $required_wc,
                        Util::get_plugin_install_activate_upgrade_link('WooCommerce', 'woocommerce', 'woocommerce/woocommerce.php', 'upgrade')
                    ));
                }
            }
            return \false;
        }
        return \true;
    }
    /**
     * Check if the EDD requirements are met.
     *
     * @param bool $add_notice true to add an error notice if the requirements are not met.
     * @return bool true if the requirements are met.
     */
    public final function check_edd($add_notice = \true) : bool
    {
        if (!\class_exists('Easy_Digital_Downloads')) {
            if (\defined('WP_CLI') && \WP_CLI) {
                /* translators: %1$s: Plugin name. */
                \wp_die(\sprintf(__('%1$s requires Easy Digital Downloads to be installed and active.'), $this->plugin->get_name()));
            }
            if (\is_admin()) {
                if ($add_notice) {
                    $this->plugin->notices()->add_error_notice($this->plugin->get_slug() . '_edd_missing', '', \sprintf(
                        /* translators: %1$s: Plugin name. %2$s: EDD link. %3$s Install/Upgrade/Activate EDD link. */
                        __('%1$s requires %2$s to be installed and active.%3$s', 'barn2-lib'),
                        $this->plugin->get_name(),
                        Util::get_plugin_link('Easy Digital Downloads', 'easy-digital-downloads'),
                        Util::get_plugin_install_activate_upgrade_link('Easy Digital Downloads', 'easy-digital-downloads', 'easy-digital-downloads/easy-digital-downloads.php')
                    ));
                }
            }
            return \false;
        }
        return \true;
    }
}
