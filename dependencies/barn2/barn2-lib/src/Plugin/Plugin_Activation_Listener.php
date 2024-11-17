<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin;

/**
 * Something which listens for plugin activation or deactivation events.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
interface Plugin_Activation_Listener
{
    /**
     * Fires when the plugin is activated.
     *
     * @param boolean $network_wide Whether the plugin is being activated network-wide or not.
     * @return void
     */
    public function on_activate($network_wide);
    /**
     * Fires when the plugin is deactivated.
     *
     * @param boolean $network_wide Whether the plugin is being deactivated network-wide or not.
     * @return void
     */
    public function on_deactivate($network_wide);
}
