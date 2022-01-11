<?php
/**
 * The main plugin file for Posts Table with Search & Sort.
 *
 * @package   Barn2\posts-table-search-sort
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     Posts Table with Search & Sort
 * Plugin URI:      https://wordpress.org/plugins/posts-data-table/
 * Description:     List your posts in an instantly searchable & sortable table.
 * Version:         1.3.7
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     posts-data-table
 * Domain Path:     /languages
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     https://www.gnu.org/licenses/gpl.html
 */

namespace Barn2\Plugin\Posts_Table_Search_Sort;

// Prevent direct file access
if ( ! defined( '\ABSPATH' ) ) {
	exit;
}

const PLUGIN_VERSION = '1.3.7';
const PLUGIN_FILE    = __FILE__;

// Autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Helper function to access the shared plugin instance.
 *
 * @return Plugin
 */
function posts_table_search_sort() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

// Load the plugin.
posts_table_search_sort()->register();
