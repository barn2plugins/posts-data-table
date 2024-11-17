<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Admin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Standard_Service;
/**
 * Core admin functions for our plugins (e.g. adding the settings link).
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class Admin_Links implements Registerable, Standard_Service
{
    /**
     * @var Plugin The core plugin data (ID, version, etc).
     */
    protected $plugin;
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    public function register()
    {
        // Add settings link from Plugins page.
        \add_filter('plugin_action_links_' . $this->plugin->get_basename(), [$this, 'add_settings_link']);
        // Add documentation link to meta info on Plugins page.
        \add_filter('plugin_row_meta', [$this, 'add_documentation_link'], 10, 2);
    }
    public function add_settings_link($links)
    {
        if (!($settings_url = $this->plugin->get_settings_page_url())) {
            return $links;
        }
        \array_unshift($links, \sprintf('<a href="%1$s">%2$s</a>', \esc_url($settings_url), __('Settings', 'barn2')));
        return $links;
    }
    public function add_documentation_link($links, $file)
    {
        if ($file !== $this->plugin->get_basename()) {
            return $links;
        }
        // Bail if there's no documentation URL.
        if (!($documentation_url = $this->plugin->get_documentation_url())) {
            return $links;
        }
        $row_meta = ['docs' => \sprintf(
            '<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
            \esc_url($documentation_url),
            /* translators: %s: The plugin name */
            \esc_attr(\sprintf(__('View %s documentation', 'barn2'), $this->plugin->get_name())),
            esc_html__('Docs', 'barn2')
        )];
        return \array_merge($links, $row_meta);
    }
}
