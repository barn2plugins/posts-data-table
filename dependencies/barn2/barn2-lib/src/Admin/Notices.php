<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Admin;

/**
 * Manages admin notices.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 * @internal
 */
class Notices
{
    /**
     * An array of notices.
     *
     * @var array
     */
    private $notices = [];
    /**
     * Boot all added admin notices.
     */
    public function boot()
    {
        \add_action('admin_notices', [$this, 'the_notices']);
    }
    /**
     * Adds a notice.
     *
     * @param string $id      A unique notice ID. Should contain lowercase characters and underscores.
     * @param string $title   The title of the notice.
     * @param string $message The notice message.
     * @param array  $options {
     *     Optional. An array of additional options to change the defaults for this notice.
     * 
     *     @type string   $type               The type of admin notice. Default 'info'. Accepts 'info', 'success', 'warning', 'error'.
     *     @type bool     $alt_style          Whether we want to use alt styles or not. Default false.
     *     @type array    $additional_classes A string array of class names.
     *     @type array    $attributes         Additional attributes for the notice div.
     *     @type bool     $paragraph_wrap     Whether to wrap the message in paragraph tags. Default true.
     *     @type array    $buttons            Associative array with buttons attributes and values. Default [].
     *     @type string   $capability         The user capability required to see the notice. Default 'edit_theme_options'.
     *     @type array    $screens            An array of screens where the notice will be displayed. Default is empty to always show.
     *     @type bool     $dismissible        Whether the admin notice is dismissible. Default true.
     *     @type string   $scope              Saves the dismissed status as an option or user-meta. Accepts 'global', 'user'. Default 'global'.
     *     @type string   $option_prefix      The prefix that will be used to build the option (or post-meta) name. Should contain lowercase characters and underscores.
     *     @type callable $dissmiss_callback  Function called before dismissing a notice. The arguments are $id, $title, $message, $options, $notice_obj.
     * }
     */
    public function add($id, $title, $message, $options = [])
    {
        $this->notices[$id] = new Notice($id, $title, $message, $options);
    }
    /**
     * Removes a notice.
     *
     * @param string $id The unique ID of the notice we want to remove.
     */
    public function remove($id)
    {
        unset($this->notices[$id]);
    }
    /**
     * Gets a single notice.
     *
     * @param string $id The unique ID of the notice we want to retrieve.
     * 
     * @return Notice|null
     */
    public function get($id)
    {
        if (isset($this->notices[$id])) {
            return $this->notices[$id];
        }
        return null;
    }
    /**
     * Gets all notices.
     *
     * @return array
     */
    public function get_all()
    {
        return $this->notices;
    }
    /**
     * Prints all visible notices.
     */
    public function the_notices()
    {
        $notices = $this->get_all();
        foreach ($notices as $notice) {
            $notice->the_notice();
        }
    }
}
