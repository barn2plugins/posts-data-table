<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Admin\Notices;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Conditional;
/**
 * Handles plugin updates that are defined on the $updates property.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 * @internal
 */
abstract class Updater implements Standard_Service, Conditional, Registerable
{
    /**
     * Callbacks functions that are called on a plugin update.
     *
     * Please note that these functions are invoked when a plugin is updated from a previous version,
     * but NOT when the plugin is newly installed.
     * 
     * The array keys should contain the version number, and it MUST be sorted from low to high.
     * 
     * Example:
     * 
     * '1.11.0' => [
     * 		'update_1_11_0_do_something',
     * 		'update_1_11_0_do_something_else',
     * 	],
     * 	'1.23.0' => [
     * 		'update_1_23_0_do_something',
     * 	],
     *
     * @var array
     */
    public static $updates = [];
    /**
     * Plugin instance.
     * 
     * @var Plugin
     */
    protected $plugin;
    /**
     * The class options.
     * 
     * See the get_default_options method to verify the array structure.
     * 
     * @var string
     */
    public $options = [];
    /**
     * Constructor.
     * 
     * @param Plugin $plugin
     * @param array  $options {
     *     Optional. An array of additional options to change the default values.
     * 
     *     @type string $version_option_name       Option name to store the version value on the options DB table. Default '<plugin_slug>_version'.
     *     @type array  $needs_update_db_notice    Needs update database admin notice array options. Accepts 'title', 'message', 'buttons' array keys.
     *     @type array  $updating_db_notice        Updating database admin notice options. Accepts 'title', 'message', 'buttons' array keys.
     *     @type array  $update_db_complete_notice Update database complete admin notice options. Accepts 'title', 'message', 'buttons' array keys.
     * }
     */
    public function __construct(Plugin $plugin, $options = null)
    {
        $this->plugin = $plugin;
        $this->set_options($options);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function is_required()
    {
        return \is_admin() && $this->needs_update();
    }
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Check the plugin's version and show the update admin notice message.
        \add_action('admin_init', [$this, 'check_version']);
        // Print the script after common.js.
        \add_action('admin_enqueue_scripts', [$this, 'add_script']);
        // Handle AJAX requests to dismiss the notice.
        \add_action('wp_ajax_' . $this->plugin->get_slug() . '_update_db', [$this, 'ajax_start_update']);
        \add_action('wp_ajax_' . $this->plugin->get_slug() . '_check_update_db', [$this, 'ajax_check_update_db']);
    }
    /**
     * Gets the default options.
     *
     * @return array
     */
    public function get_default_options()
    {
        return ['version_option_name' => $this->plugin->get_slug() . '_version', 'needs_update_db_notice' => ['title' => \sprintf(__('%1$s database update required'), $this->plugin->get_name()), 'message' => \sprintf(__('<p>%1$s has been updated! To keep things running smoothly, we have to update your database to the newest version. The database update process runs in the background and may take a little while, so please be patient.</p>'), $this->plugin->get_name()), 'buttons' => ['update-db' => ['value' => 'Update Database', 'href' => \wp_nonce_url(\add_query_arg($this->plugin->get_slug() . '_update_db', 'true', \admin_url('admin.php?page=wc-settings')), 'wc_db_update', 'wc_db_update_nonce'), 'id' => $this->plugin->get_slug() . '-update-db', 'class' => 'button-primary'], 'learn-more' => ['value' => 'Learn more about updates', 'href' => 'https://barn2.com/kb/learn-more-about-updates/', 'target' => '_blank', 'class' => 'button-secondary', 'style' => 'margin-left: 8px;']]], 'updating_db_notice' => ['title' => \sprintf(__('%1$s database update'), $this->plugin->get_name()), 'message' => \sprintf(__('<p>%1$s is updating the database in the background. The database update process may take a little while, so please be patient.</p>'), $this->plugin->get_name())], 'update_db_complete_notice' => ['title' => \sprintf(__('%1$s database update done'), $this->plugin->get_name()), 'message' => \sprintf(__('<p>%1$s database update complete. Thank you for updating to the latest version!</p>'), $this->plugin->get_name())]];
    }
    /**
     * Sets the final options.
     */
    public function set_options($options)
    {
        $this->options = \array_replace_recursive($this->get_default_options(), $options);
    }
    /**
     * Checks the plugin's version and shows the update admin notice message if an update is required.
     */
    public function check_version()
    {
        if ($this->needs_update() && !\defined('IFRAME_REQUEST')) {
            $this->show_notice();
        }
    }
    /**
     * Show the update admin notice message.
     */
    public function show_notice()
    {
        // Removes all old dismissed admin notice messages status.
        \delete_option('barn2_notice_dismissed_' . $this->plugin->get_slug() . '_updating_db_notice');
        \delete_option('barn2_notice_dismissed_' . $this->plugin->get_slug() . '_update_db_complete_notice');
        $admin_notice = new Notices();
        // If it needs to update.
        if ($this->needs_update() && !$this->is_updating()) {
            $admin_notice->add($this->plugin->get_slug() . '_needs_update_db_notice', $this->options['needs_update_db_notice']['title'], $this->options['needs_update_db_notice']['message'], ['type' => 'warning', 'capability' => 'install_plugins', 'dismissible' => \false, 'buttons' => $this->options['needs_update_db_notice']['buttons'] ?? null]);
        }
        // If it is updating.
        if ($this->needs_update() || $this->is_updating()) {
            $admin_notice->add($this->plugin->get_slug() . '_updating_db_notice', $this->options['updating_db_notice']['title'], $this->options['updating_db_notice']['message'], ['type' => 'info', 'capability' => 'install_plugins', 'additional_classes' => $this->is_updating() ? [] : ['hidden'], 'buttons' => $this->options['updating_db_notice']['buttons'] ?? null]);
        }
        // If the update is complete.
        if ($this->needs_update() || $this->update_is_complete()) {
            $admin_notice->add($this->plugin->get_slug() . '_update_db_complete_notice', $this->options['update_db_complete_notice']['title'], $this->options['update_db_complete_notice']['message'], ['type' => 'success', 'capability' => 'install_plugins', 'additional_classes' => $this->needs_update() ? ['hidden'] : [], 'buttons' => $this->options['update_db_complete_notice']['buttons'] ?? null]);
        }
        $admin_notice->boot();
    }
    /**
     * Runs all the required update callback functions.
     */
    private function update()
    {
        // Deletes previous updating status.
        \delete_transient($this->plugin->get_slug() . '_updating_db');
        \delete_transient($this->plugin->get_slug() . '_update_db_complete');
        // Set updating DB status.
        \set_transient($this->plugin->get_slug() . '_updating_db', \true);
        $db_version = $this->get_current_database_version();
        $code_version = $this->get_current_code_version();
        // Runs the required updates.
        foreach (self::get_update_callbacks() as $version => $update_callbacks) {
            if (\version_compare($db_version, $version, '<')) {
                self::update_version($version);
                if ($this->update_db_version($version)) {
                    $db_version = $version;
                }
            }
        }
        if (\version_compare($code_version, $db_version, '>')) {
            $this->update_db_version($code_version);
        }
        // Deletes updating DB status.
        \delete_transient($this->plugin->get_slug() . '_updating_db');
        // Set update DB complete status.
        \set_transient($this->plugin->get_slug() . '_update_db_complete', \true);
        /**
         * Fires after the plugin is updated.
         * 
         * @param string $db_version The version of the plugin as stored in the database.
         * @param string $code_version The version of the plugin as stored in the code.
         */
        \do_action($this->plugin->get_slug() . '_updated', $db_version, $code_version, $this->plugin);
    }
    /**
     * Updates a specific version.
     */
    public static function update_version($version = '')
    {
        if (isset(static::$updates[$version])) {
            foreach (static::$updates[$version] as $function) {
                if (\method_exists(\get_called_class(), $function)) {
                    static::$function();
                }
            }
        }
    }
    /**
     * Updates the version on the DB.
     *
     * @param string|null $version Version number or null to use the current plugin version.
     * 
     * @return bool
     */
    public function update_db_version($version = null) : bool
    {
        return \update_option($this->options['version_option_name'], \is_null($version) ? $this->get_current_code_version() : $version);
    }
    /**
     * Prints the script for handling the update process.
     */
    public function add_script()
    {
        // If it needs an update.
        if ($this->needs_update() && !$this->is_updating()) {
            $script = "( function( \$, window, document, undefined ) {\r\n\r\n\t\$( function() {\r\n\r\n\t\t\$( '#" . $this->plugin->get_slug() . "-update-db' ).on( 'click', function( e ) {\r\n            e.preventDefault();\r\n\r\n            \$( '#" . $this->plugin->get_slug() . "_needs_update_db_notice' ).hide();\r\n            \$( '#" . $this->plugin->get_slug() . "_updating_db_notice' ).show();\r\n\r\n\t\t\tvar data = \$( this ).data();\r\n\t\t\tdata.action = '" . $this->plugin->get_slug() . "_update_db';\r\n\t\t\tdata.nonce = '" . \wp_create_nonce($this->plugin->get_slug() . '_update_db') . "';\r\n\r\n\t\t\t\$.ajax( {\r\n\t\t\t\turl: ajaxurl, // always defined when running in WP Admin\r\n\t\t\t\ttype: 'POST',\r\n\t\t\t\tdata: data,\r\n\t\t\t\txhrFields: {\r\n\t\t\t\t\twithCredentials: true\r\n\t\t\t\t}\r\n\t\t\t} ).done(function (out) {\r\n                \$( '#" . $this->plugin->get_slug() . "_updating_db_notice' ).hide();\r\n                \$( '#" . $this->plugin->get_slug() . "_update_db_complete_notice' ).show();\r\n\t\t\t}).fail(function (out) {\r\n                \$( '#" . $this->plugin->get_slug() . "_updating_db_notice' ).hide();\r\n                \$( '#" . $this->plugin->get_slug() . "_update_db_error_notice' ).show();\r\n\t\t\t});\r\n\t\t} );\r\n\t} );\r\n\r\n} )( jQuery, window, document );";
        }
        // If it's already updating.
        if ($this->needs_update() && $this->is_updating()) {
            $script = "( function( \$, window, document, undefined ) {\r\n\r\n\t\$( function() {\r\n\r\n        var data = {};\r\n        data.action = '" . $this->plugin->get_slug() . "_check_update_db';\r\n        data.nonce = '" . \wp_create_nonce($this->plugin->get_slug() . '_check_update_db') . "';\r\n\r\n        \$.ajax( {\r\n            url: ajaxurl, // always defined when running in WP Admin\r\n            type: 'POST',\r\n            data: data,\r\n            xhrFields: {\r\n                withCredentials: true\r\n            }\r\n        } ).done(function (out) {\r\n            console.log('CHECKED');\r\n            \$( '#" . $this->plugin->get_slug() . "_updating_db_notice' ).hide();\r\n            \$( '#" . $this->plugin->get_slug() . "_update_db_complete_notice' ).show();\r\n        }).fail(function (out) {\r\n            \$( '#" . $this->plugin->get_slug() . "_updating_db_notice' ).hide();\r\n            \$( '#" . $this->plugin->get_slug() . "_update_db_error_notice' ).show();\r\n        });\r\n\r\n\t} );\r\n\r\n} )( jQuery, window, document );";
        }
        if (isset($script)) {
            \wp_add_inline_script('common', $script, 'after');
        }
    }
    /**
     * Starts the update process through AJAX.
     */
    public function ajax_start_update()
    {
        if (!isset($_POST['action']) || $_POST['action'] !== $this->plugin->get_slug() . '_update_db') {
            return;
        }
        \check_ajax_referer($this->plugin->get_slug() . '_update_db', 'nonce', \true);
        $this->update();
        exit;
    }
    /**
     * Check if the plugin is updating through AJAX.
     */
    public function ajax_check_update_db()
    {
        global $wpdb;
        if (!isset($_POST['action']) || $_POST['action'] !== $this->plugin->get_slug() . '_check_update_db') {
            return;
        }
        \check_ajax_referer($this->plugin->get_slug() . '_check_update_db', 'nonce', \true);
        // Using a query to get the transient value otherwise WordPress will use the first cached values instead.
        $query = "SELECT option_value FROM wp_options WHERE option_name ='" . '_transient_' . $this->plugin->get_slug() . '_updating_db' . "';";
        while ($wpdb->get_var($query) !== null) {
            \sleep(1);
        }
        exit;
    }
    /**
     * Get the list of update callbacks from the plugin Update class.
     * 
     * @return array
     */
    public static function get_update_callbacks() : array
    {
        return static::$updates;
    }
    /**
     * Gets the latest update version from the plugin Update class.
     *
     * @return string
     */
    public function get_latest_update_version()
    {
        return \array_key_last(self::get_update_callbacks());
    }
    /**
     * Gets the current plugin version as stored in the database.
     *
     * @return string
     */
    public function get_current_database_version()
    {
        return \get_option($this->options['version_option_name']);
    }
    /**
     * Gets the current plugin version as stored in the code.
     *
     * @return string
     */
    public function get_current_code_version() : string
    {
        return $this->plugin->get_version();
    }
    /**
     * Condition to verify if it's a new plugin installation and not an update.
     *
     * @return bool
     */
    public function is_new_install() : bool
    {
        return \false;
    }
    /**
     * Verifies if it needs to update.
     *
     * @return bool
     */
    public function needs_update()
    {
        $db_version = $this->get_current_database_version();
        $latest_update_version = $this->get_latest_update_version();
        return !$this->is_new_install() && $latest_update_version !== null && \version_compare($db_version, $latest_update_version, '<');
    }
    /**
     * Verifies if the plugin is updating.
     *
     * @return string|bool
     */
    public function is_updating()
    {
        return \get_transient($this->plugin->get_slug() . '_updating_db');
    }
    /**
     * Verifies if the update is complete.
     *
     * @return string|bool
     */
    public function update_is_complete()
    {
        return \get_transient($this->plugin->get_slug() . '_update_db_complete');
    }
}
