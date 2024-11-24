<?php

namespace Barn2\Plugin\Posts_Table_Search_Sort\Dependencies\Lib\Admin;

/**
 * Creates an admin notice with dismissible features.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.0
 * @internal
 */
class Notice
{
    /**
     * The notice-ID.
     *
     * @var string
     */
    private $id;
    /**
     * The notice title.
     *
     * @var string
     */
    private $title;
    /**
     * The notice message.
     *
     * @var string
     */
    private $message;
    /**
     * The notice options.
     *
     * @var array
     */
    private $options = ['type' => 'info', 'alt_style' => \false, 'additional_classes' => [], 'attributes' => [], 'paragraph_wrap' => \true, 'buttons' => [], 'capability' => 'edit_theme_options', 'screens' => [], 'dismissible' => \true, 'scope' => 'global', 'option_prefix' => 'barn2_notice_dismissed', 'dissmiss_callback' => null];
    /**
     * Constructor.
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
    public function __construct($id, $title, $message, $options = [])
    {
        $this->id = \sanitize_key($id);
        $this->title = $title;
        $this->message = $message;
        $this->options = \wp_parse_args($options, $this->options);
        if (!$this->id || !$this->message) {
            return;
        }
        if ($this->options['dismissible'] === \true && !$this->is_dismissed()) {
            // Enqueue notices script to handle dismissables notices.
            \add_action('admin_enqueue_scripts', [$this, 'load_scripts']);
            // Handle AJAX requests to dismiss the notice.
            \add_action('wp_ajax_barn2_dismiss_admin_notice', [$this, 'ajax_maybe_dismiss_notice']);
        }
    }
    /**
     * Enqueues barn2-notices script.
     */
    public function load_scripts()
    {
        \wp_enqueue_script('barn2-notices');
    }
    /**
     * Gets the notice markup.
     *
     * @return string
     */
    public function get_notice()
    {
        // Use a deprecated notice function if WP is older than 6.4.0.
        if (!\function_exists('wp_get_admin_notice')) {
            return $this->get_notice_deprecated();
        }
        $title = $this->get_title();
        $message = $this->message;
        $buttons = $this->get_buttons();
        $additional_classes = \array_merge(['barn2-notice'], $this->options['additional_classes']);
        $attributes = $this->options['attributes'];
        $paragraph_wrap = $this->options['paragraph_wrap'];
        // Adds a nonce to the notice data attribute to be used on the AJAX cal if the notice is dismissible.
        if ($this->options['dismissible'] === \true && !$this->is_dismissed()) {
            $attributes = \array_merge($attributes, ['data-nonce' => \wp_create_nonce('barn2_dismiss_admin_notice_' . $this->id)]);
        }
        if ($title !== '' && $this->options['paragraph_wrap'] === \true) {
            $message = \wpautop($message);
            $paragraph_wrap = \false;
        }
        // Adds the title and the buttons to the message.
        $message = $title . $message . $buttons;
        // Gets the notice markup.
        $notice = \wp_get_admin_notice($message, ['id' => $this->id, 'type' => $this->options['type'], 'dismissible' => $this->options['dismissible'], 'additional_classes' => $additional_classes, 'attributes' => $attributes, 'paragraph_wrap' => $paragraph_wrap]);
        return $notice;
    }
    /**
     * Gets the notice markup.
     *
     * @return string
     * @deprecated 6.4.0 Use Notice->get_notice() instead that uses wp_get_admin_notice() function.
     */
    public function get_notice_deprecated()
    {
        $classes = 'notice barn2-notice';
        $attributes = '';
        $message = $this->message;
        if (\is_string($this->options['type'])) {
            $type = \trim($this->options['type']);
            if ($type !== '') {
                $classes .= ' notice-' . $type;
            }
        }
        if ($this->options['dismissible'] === \true) {
            $classes .= ' is-dismissible';
        }
        if ($this->options['alt_style'] === \true) {
            $classes .= ' notice-alt';
        }
        if (\is_array($this->options['additional_classes']) && !empty($this->options['additional_classes'])) {
            $classes .= ' ' . \implode(' ', $this->options['additional_classes']);
        }
        // Adds a nonce to the notice data attribute to be used on the AJAX cal if the notice is dismissible.
        if ($this->options['dismissible'] === \true && !$this->is_dismissed()) {
            $attributes = ' data-nonce="' . \wp_create_nonce('barn2_dismiss_admin_notice_' . $this->id) . '"';
        }
        if (\is_array($this->options['attributes']) && !empty($this->options['attributes'])) {
            foreach ($this->options['attributes'] as $attr => $val) {
                if (\is_bool($val)) {
                    $attributes .= $val ? ' ' . $attr : '';
                } elseif (\is_int($attr)) {
                    $attributes .= ' ' . \esc_attr(\trim($val));
                } elseif ($val) {
                    $attributes .= ' ' . $attr . '="' . \esc_attr(\trim($val)) . '"';
                }
            }
        }
        if ($this->options['paragraph_wrap'] === \true) {
            $message = \wpautop($message);
        }
        // Adds the title and the buttons to the message.
        $message = $this->get_title() . $message . $this->get_buttons();
        // Gets the notice markup.
        $notice = \sprintf('<div id="%1$s" class="%2$s"%3$s>%4$s</div>', $this->id, $classes, $attributes, $message);
        return $notice;
    }
    /**
     * Returns the title markup.
     *
     * @return string
     */
    public function get_title()
    {
        if (!$this->title) {
            return '';
        }
        return \sprintf('<h2 class="notice-title">%s</h2>', \wp_strip_all_tags($this->title));
    }
    /**
     * Returns the buttons markup.
     *
     * @return string
     */
    public function get_buttons()
    {
        if (empty($this->options['buttons'])) {
            return '';
        }
        $buttons = '';
        foreach ($this->options['buttons'] as $key => $button) {
            if (empty($button)) {
                continue;
            }
            $attributes = '';
            foreach ($button as $attr => $val) {
                if ($attr === 'value') {
                    $attributes .= '';
                } elseif (\is_bool($val)) {
                    $attributes .= $val ? ' ' . $attr : '';
                } elseif (\is_int($attr)) {
                    $attributes .= ' ' . \esc_attr(\trim($val));
                } elseif ($val) {
                    $attributes .= ' ' . $attr . '="' . \esc_attr(\trim($val)) . '"';
                }
            }
            $buttons .= '<a' . $attributes . '>' . $button['value'] . '</a>';
        }
        return '<p class="notice-buttons">' . $buttons . '</p>';
    }
    /**
     * Prints the notice.
     */
    public function the_notice()
    {
        // Early exit if we don't want to show this notice.
        if (!$this->show()) {
            return;
        }
        echo \wp_kses_post($this->get_notice());
    }
    /**
     * Determine if the notice should be shown or not.
     *
     * @return bool
     */
    public function show()
    {
        // Don't show if the user doesn't have the required capability.
        if (!\current_user_can($this->options['capability'])) {
            return \false;
        }
        // Don't show if we're not on the right screen.
        if (!$this->is_screen()) {
            return \false;
        }
        // Don't show if notice has been dismissed.
        if ($this->options['dismissible'] === \true && $this->is_dismissed()) {
            return \false;
        }
        return \true;
    }
    /**
     * Evaluate if we're on the right place depending on the "screens" argument.
     *
     * @return bool
     */
    private function is_screen()
    {
        // If screen is empty we want this shown on all screens.
        if (!$this->options['screens'] || empty($this->options['screens'])) {
            return \true;
        }
        // Make sure the get_current_screen function exists.
        if (!\function_exists('get_current_screen')) {
            require_once \ABSPATH . 'wp-admin/includes/screen.php';
        }
        /** @var \WP_Screen $current_screen */
        $current_screen = \get_current_screen();
        // Check if we're on one of the defined screens.
        return \in_array($current_screen->id, $this->options['screens'], \true);
    }
    /**
     * Run check to see if we need to dismiss the notice.
     * If all tests are successful then call the dismiss_notice() method.
     *
     * @return void
     */
    public function ajax_maybe_dismiss_notice()
    {
        // If dissmiss_callback is set.
        if (\is_callable($this->options['dissmiss_callback'])) {
            \call_user_func($this->options['dissmiss_callback'], $this->id, $this->title, $this->message, $this->options, $this);
        }
        // Early exit if we're not on a barn2_dismiss_admin_notice action.
        if (!isset($_POST['action']) || 'barn2_dismiss_admin_notice' !== $_POST['action']) {
            return;
        }
        // Early exit if the ID of the notice is not the one from this object.
        if (!isset($_POST['id']) || $this->id !== $_POST['id']) {
            return;
        }
        // Make sure nonce is OK.
        \check_ajax_referer('barn2_dismiss_admin_notice_' . $this->id, 'nonce', \true);
        // Dismisses the notice.
        $this->dismiss_notice();
    }
    /**
     * Dismisses the notice.
     *
     * @return void
     */
    public function dismiss_notice()
    {
        if ($this->options['scope'] === 'user') {
            \update_user_meta(\get_current_user_id(), $this->options['option_prefix'] . '_' . $this->id, \true);
            return;
        }
        \update_option($this->options['option_prefix'] . '_' . $this->id, \true, \false);
    }
    /**
     * Checks if the notice has been dismissed or not.
     *
     * @return bool
     */
    public function is_dismissed()
    {
        // Check if the notice has been dismissed when using user-meta.
        if ($this->options['scope'] === 'user') {
            return \get_user_meta(\get_current_user_id(), $this->options['option_prefix'] . '_' . $this->id, \true);
        }
        return \get_option($this->options['option_prefix'] . '_' . $this->id);
    }
}
