<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Core_Service;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Translatable;
/**
 * Service class to handle the i18n of the plugin.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class I18n implements Registerable, Translatable, Core_Service
{
    /**
     * The plugin instance.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * Construct the I18n object.
     *
     * @param Plugin $plugin The plugin instance.
     * @return void
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        \add_action('after_setup_theme', array($this, 'load_textdomain'));
    }
    /**
     * Load the plugin's textdomain.
     *
     * @return void
     */
    public function load_textdomain()
    {
        \load_plugin_textdomain($this->plugin->plugin_data()->get_textdomain(), \false, \dirname(\plugin_basename($this->plugin->get_file())) . '/languages');
    }
}
