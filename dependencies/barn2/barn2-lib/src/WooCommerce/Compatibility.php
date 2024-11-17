<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\WooCommerce;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Conditional;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Core_Service;
/**
 * Helper methods to handle the compatibility with WooCommerce Features.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class Compatibility implements Registerable, Conditional, Core_Service
{
    /**
     * The plugin instance.
     *
     * @var Simple_Plugin
     */
    private $plugin;
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    public function is_required()
    {
        return $this->plugin->is_woocommerce();
    }
    public function register()
    {
        \add_action('before_woocommerce_init', [$this, 'declare_woocommerce_compatibility']);
    }
    public final function get_features()
    {
        return FeaturesUtil::get_features();
    }
    public final function declare_woocommerce_compatibility()
    {
        if (\class_exists(FeaturesUtil::class)) {
            $plugin_compatibility = $this->plugin->get_woocommerce_features();
            foreach (\array_keys($this->get_features()) as $feature) {
                FeaturesUtil::declare_compatibility($feature, $this->plugin->get_file(), $plugin_compatibility[$feature] ?? \true);
            }
        }
    }
}
