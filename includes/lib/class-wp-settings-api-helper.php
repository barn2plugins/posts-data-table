<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Settings_API_Helper' ) ) {

	/**
	 * Helper functions for the WordPress Settings API.
	 *
	 * @author    Barn2 Media <info@barn2.co.uk>
	 * @license   GPL-3.0
	 * @copyright Barn2 Media Ltd
	 * @version   1.2
	 */
	class WP_Settings_API_Helper {

		public static function add_settings_section( $section, $page, $title, $description_callback, $settings = false ) {
			if ( ! is_callable( $description_callback ) ) {
				$description_callback = '__return_false';
			}
			add_settings_section( $section, $title, $description_callback, $page );
			self::add_settings_fields( $settings, $section, $page );
		}

		public static function add_settings_fields( $settings, $section, $page ) {
			if ( ! $settings || ! is_array( $settings ) ) {
				return;
			}
			foreach ( $settings as $setting ) {
				if ( ! is_array( $setting ) || empty( $setting['id'] ) ) {
					continue;
				}

				$args = wp_parse_args( $setting, array_fill_keys( array( 'id', 'desc', 'label', 'title', 'class', 'field_class', 'default', 'suffix', 'custom_attributes' ), '' ) );

				$args['input_class'] = $args['class'];
				unset( $args['class'] );

				$args['class']		 = $args['field_class'];
				$args['label_for']	 = $args['id'];

				$setting_callback = array( __CLASS__, 'settings_field_' . $setting['type'] );

				if ( is_callable( $setting_callback ) ) {
					add_settings_field( $setting['id'], $setting['title'], $setting_callback, $page, $section, $args );
				}
			}
		}

		public static function settings_field_text( $args ) {
			$class	 = ! empty( $args['input_class'] ) ? $args['input_class'] : 'regular-text';
			$type	 = ! empty( $args['type'] ) ? $args['type'] : 'text';
			?>
			<input id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $class ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( self::get_value( $args['id'], $args['default'] ) ); ?>"<?php self::custom_attributes( $args ); ?>/><?php
			if ( ! empty( $args['suffix'] ) ) {
				echo ' ' . esc_html( $args['suffix'] );
			}
			self::field_description( $args );
		}

		public static function settings_field_number( $args ) {
			$args['input_class'] = ! empty( $args['input_class'] ) ? $args['input_class'] : 'small-text';
			$args['type']		 = 'number';
			self::settings_field_text( $args );
		}

		public static function settings_field_textarea( $args ) {
			$class	 = ! empty( $args['input_class'] ) ? $args['input_class'] : 'large-text';
			$rows	 = isset( $args['rows'] ) ? absint( $args['rows'] ) : 4;
			?>
			<textarea id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $class ); ?>" rows="<?php echo esc_attr( $rows ); ?>"<?php self::custom_attributes( $args ); ?>><?php echo esc_textarea( self::get_value( $args['id'], $args['default'] ) ); ?></textarea>
			<?php
			self::field_description( $args );
		}

		public static function settings_field_select( $args ) {
			$current_value = self::get_value( $args['id'], $args['default'] );
			?>
			<select id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $args['input_class'] ); ?>"<?php self::custom_attributes( $args ); ?>>
				<?php foreach ( $args['options'] as $value => $option ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $current_value ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select><?php
			if ( ! empty( $args['suffix'] ) ) {
				echo ' ' . esc_html( $args['suffix'] );
			}
			self::field_description( $args );
		}

		public static function settings_field_checkbox( $args ) {
			$current_value = self::get_value( $args['id'], $args['default'] );
			?>
			<fieldset>
				<legend class="screen-reader-text"><?php echo esc_html( $args['title'] ); ?></legend>
				<label for="<?php echo esc_attr( $args['id'] ); ?>">
					<input id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $args['input_class'] ); ?>" type="checkbox"<?php checked( $current_value ); ?> value="1"<?php self::custom_attributes( $args ); ?>/>
					<?php
					if ( ! empty( $args['label'] ) ) {
						echo esc_html( $args['label'] );
					}
					?>
				</label>
			</fieldset>
			<?php self::field_description( $args ); ?>
			<?php
		}

		private static function field_description( $args ) {
			if ( ! empty( $args['desc'] ) ) {
				echo '<p class="description">' . $args['desc'] . '</p>';
			}
		}

		private static function custom_attributes( $args ) {
			echo self::get_custom_attributes( $args );
		}

		private static function get_custom_attributes( $args ) {
			if ( empty( $args['custom_attributes'] ) ) {
				return;
			}
			$custom_atts = $args['custom_attributes'];
			$result		 = '';

			foreach ( $custom_atts as $att => $value ) {
				$result .= sprintf( ' %s="%s"', sanitize_key( $att ), esc_attr( $value ) );
			}
			return $result;
		}

		private static function get_value( $option, $default = false ) {
			$value			 = '';
			$matches		 = array();
			$subkey_match	 = preg_match( '/(\w+)\[(\w+)\]/U', $option, $matches );

			if ( $subkey_match && isset( $matches[1], $matches[2] ) ) {
				$subkey			 = $matches[2];
				$parent_option	 = get_option( $matches[1], array() );
				$value			 = isset( $parent_option[$subkey] ) ? $parent_option[$subkey] : $default;
			} else {
				$value = get_option( $option, $default );
			}

			return $value;
		}

	}

}