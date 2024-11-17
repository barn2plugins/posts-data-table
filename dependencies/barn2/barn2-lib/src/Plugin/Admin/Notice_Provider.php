<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Admin;

use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Admin\Notices;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Conditional;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Plugin\Plugin;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Service\Core_Service;
use Exception;
use WP_Error;
use wpdb;
/**
 * Helper methods to handle the admin notices.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @internal
 */
class Notice_Provider implements Registerable, Core_Service, Conditional
{
    /**
     * The plugin instance.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * The notices class.
     *
     * @var Notices
     */
    private $notices;
    /**
     * Construct the Notice_Provider object.
     *
     * @param Plugin $plugin The plugin instance.
     * @return void
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->notices = new Notices();
    }
    /**
     * {@inheritDoc}
     */
    public function is_required()
    {
        return \is_admin();
    }
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->notices->boot();
    }
    public function add_info_notice($id, $title, $message, $options = [])
    {
        $options['type'] = 'info';
        $this->add_notice($id, $title, $message, $options);
    }
    public function add_success_notice($id, $title, $message, $options = [])
    {
        $options['type'] = 'success';
        $this->add_notice($id, $title, $message, $options);
    }
    public function add_warning_notice($id, $title, $message, $options = [])
    {
        $options['type'] = 'warning';
        $this->add_notice($id, $title, $message, $options);
    }
    public function add_error_notice($id, $title, $error, $options = [])
    {
        $message = '';
        switch (\true) {
            case $error instanceof Exception:
                $message = $error->getMessage();
                break;
            case $error instanceof WP_Error:
                $message = $error->get_error_message();
                break;
            case $error instanceof wpdb:
                $message = $error->last_error;
                break;
            case \is_string($error):
                $message = $error;
                break;
        }
        if ($message) {
            $options['type'] = 'error';
            $this->add_notice($id, $title, $message, $options);
        }
    }
    public function remove_notice($id)
    {
        $this->notices->remove($id);
    }
    public function get_notice($id)
    {
        return $this->notices->get($id);
    }
    public function get_notices()
    {
        return $this->notices->get_all();
    }
    /**
     * Add a notice.
     *
     * @param string $id            The ID of the notice.
     * @param string $title         The title of the notice.
     * @param string $message       The message of the notice.
     * @param array  $options       An array of options for the notice.
     *                              {
     * @type array   $screens       An array of screens where the notice will be displayed.
     *                              Leave empty to always show.
     *                              Defaults to an empty array.
     * @type string  $scope         Can be "global" or "user".
     *                              Determines if the dismissed status will be saved as an option or user-meta.
     *                              Defaults to "global".
     * @type string  $type          Can be one of "info", "success", "warning", "error".
     *                              Defaults to "info".
     * @type bool    $alt_style     Whether we want to use alt styles or not.
     *                              Defaults to false.
     * @type string  $capability    The user capability required to see the notice.
     *                              Defaults to "edit_theme_options".
     * @type string  $option_prefix The prefix that will be used to build the option (or post-meta) name.
     *                              Can contain lowercase latin letters and underscores.
     *                              }
     * @return void
     */
    private function add_notice($id, $title, $message, $options = [])
    {
        $options = \wp_parse_args($options, ['screens' => $this->get_screens()]);
        $this->notices->add($id, $title, $message, $options);
    }
    private function get_screens()
    {
        $screens = ['plugins'];
        $url = \wp_parse_url($this->plugin->get_settings_page_url());
        $screen = $url['path'] ?? '';
        $query = \wp_parse_args($url['query'] ?? '');
        $page = $query['page'] ?? '';
        $post_type = $query['post_type'] ?? '';
        switch ($screen) {
            case 'options-general.php':
                $screens[] = 'options-general';
                break;
            case 'admin.php':
                switch ($page) {
                    case 'wc-settings':
                        $screens[] = 'woocommerce_page_wc-settings';
                        break;
                    case 'document_library_pro':
                        $screens[] = 'toplevel_page_document_library_pro';
                        break;
                    case 'posts_table':
                        $screens[] = 'toplevel_page_posts_table';
                        break;
                }
                break;
            case 'edit.php':
                if ($post_type) {
                    $screens[] = "edit-{$post_type}";
                }
                break;
        }
        return $screens;
    }
}
