<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Traits\Plugin_Aware;
/**
 * Service to automatically include PHP files from a given directory,
 * relative to the plugin root.
 * 
 * Usage:
 * 
 * In the plugin class call the "add_service" method to add the service.
 * 
 * Example:
 * ```
 * $this->add_service( 'includes_files', new Includes_Files( $this ) );
 * ```
 * 
 * The service will automatically include all PHP files in the 'inc' directory.
 * 
 * To include files from a different directory, pass the directory name as the second argument.
 * 
 * Example:
 * ```
 * $this->add_service( 'includes_files', new Includes_Files( $this, [ 'includes' ] ) );
 * ```
 * 
 * To include files from multiple directories, pass an array of directory names as the second argument.
 * 
 * Example:
 * ```
 * $this->add_service( 'includes_files', new Includes_Files( $this, [ 'includes', 'admin/includes' ] ) );
 * ```
 * 
 * It is recommended to use the 'inc' directory for all plugin includes.
 * 
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class Includes_Files implements Core_Service, Registerable
{
    use Plugin_Aware;
    /**
     * The paths to include files from.
     * 
     * @var array
     */
    protected $paths = [];
    /**
     * Set the plugin instance and paths to include files from.
     * 
     * @param Plugin $plugin The plugin instance.
     * @param array $paths The paths to include files from. Defaults to 'inc'. Paths are relative to the plugin root.
     * @return void
     */
    public function __construct(Plugin $plugin, $paths = ['inc'])
    {
        $this->set_plugin($plugin);
        $this->paths = $paths;
    }
    /**
     * Register the service.
     * 
     * @return void
     */
    public function register()
    {
        $this->register_includes();
    }
    /**
     * Register the includes.
     * 
     * @return void
     */
    public function register_includes()
    {
        foreach ($this->paths as $path) {
            $this->include_files($path);
        }
    }
    /**
     * Automatically include all PHP files in a given directory.
     * 
     * @param string $path The directory path relative to the plugin root.
     * @param string $pattern The glob pattern to match files against.
     * @return void
     */
    public function include_files($path = 'inc', $pattern = '*.php')
    {
        $dir = $this->get_plugin()->get_dir_path($path);
        $files = \glob(\trailingslashit($dir) . $pattern);
        foreach ($files as $file) {
            require_once $file;
        }
    }
}
