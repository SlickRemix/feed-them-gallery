<?php
/**
 * Register Settings.
 *
 * @package     Feed them Gallery
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2020, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since	1.3.4
 * @return	mixed
 */
function ftg_get_option( $key = '', $default = false ) {
	global $ftg_options;

	$value = ! empty( $ftg_options[ $key ] ) ? $ftg_options[ $key ] : $default;
	$value = apply_filters( 'ftg_get_option', $value, $key, $default );

	return apply_filters( 'ftg_get_option_' . $key, $value, $key, $default );
} // ftg_get_option

/**
 * Update an option
 *
 * Updates a ftg setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *          the key from the ftg_options array.
 *
 * @since	1.3.4
 * @param	string            $key    The Key to update
 * @param	string|bool|int   $value  The value to set the key to
 * @return	bool              True if updated, false if not.
 */
function ftg_update_option( $key = '', $value = false ) {

	// If no key, exit
	if ( empty( $key ) ){
		return false;
	}

	if ( empty( $value ) ) {
		$remove_option = ftg_delete_option( $key );
		return $remove_option;
	}

	// First let's grab the current settings
	$options = get_option( 'ftg_settings' );

	// Let's let devs alter that value coming in
	$value = apply_filters( 'ftg_update_option', $value, $key );

	// Next let's try to update the value
	$options[ $key ] = $value;
	$did_update = update_option( 'ftg_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ){
		global $ftg_options;
		$ftg_options[ $key ] = $value;

	}

	return $did_update;
} // ftg_update_option

/**
 * Remove an option.
 *
 * Removes a ftg setting value in both the db and the global variable.
 *
 * @since	1.0
 * @param	str		$key	The Key to delete.
 * @return	bool	True if updated, false if not.
 */
function ftg_delete_option( $key = '' ) {

	// If no key, exit
	if ( empty( $key ) ){
		return false;
	}

	// First let's grab the current settings
	$options = get_option( 'ftg_settings' );

	// Next let's try to update the value
	if( isset( $options[ $key ] ) ) {

		unset( $options[ $key ] );

	}

	$did_update = update_option( 'ftg_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ){
		global $ftg_options;
		$ftg_options = $options;
	}

	return $did_update;
} // ftg_delete_option

/**
 * Get Settings.
 *
 * Retrieves all plugin settings.
 *
 * @since	1.0
 * @return	arr		FTG settings.
 */
function ftg_get_settings() {
	$settings = get_option( 'ftg_settings' );
	
	if( empty( $settings ) ) {

		$settings = array();

		update_option( 'ftg_settings', $settings );
		
	}

	return apply_filters( 'ftg_get_settings', $settings );
} // ftg_get_settings

/**
 * Sanitize text fields
 *
 * @since	1.3.4
 * @param	string		$input	The field value
 * @return	string		$input	Sanitizied value
 */
function ftg_sanitize_text_field( $input ) {
	return trim( $input );
} // ftg_sanitize_text_field
add_filter( 'ftg_settings_sanitize_text', 'ftg_sanitize_text_field' );

/**
 * Sanitize HTML Class Names
 *
 * @since	1.3.4
 * @param	string|array	$class	HTML Class Name(s)
 * @return	string			$class
 */
function ftg_sanitize_html_class( $class = '' ) {

	if ( is_string( $class ) )	{
		$class = sanitize_html_class( $class );
	} else if ( is_array( $class ) )	{
		$class = array_values( array_map( 'sanitize_html_class', $class ) );
		$class = implode( ' ', array_unique( $class ) );
	}

	return $class;

} // ftg_sanitize_html_class

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @return	void
 */
function ftg_header_callback( $args ) {
	echo apply_filters( 'ftg_after_setting_output', '', $args );
} // ftg_header_callback

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_checkbox_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$name = '';
	} else {
		$name = 'name="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );

	$checked = ! empty( $ftg_option ) ? checked( 1, $ftg_option, false ) : '';
	$html = '<input type="checkbox" id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"' . $name . ' value="1" ' . $checked . ' class="' . $class . '"/>';
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_checkbox_callback

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_multicheck_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	$class = ftg_sanitize_html_class( $args['field_class'] );

	$html = '';

	if ( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option )	{
			if ( isset( $ftg_option[ $key ] ) )	{
				$enabled = $option;
			} else	{
				$enabled = NULL;
			}

			$html .= '<input name="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . '][' . ftg_sanitize_key( $key ) . ']" id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . '][' . ftg_sanitize_key( $key ) . ']" class="' . $class . '" type="checkbox" value="' . esc_attr( $option ) . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';

			$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . '][' . ftg_sanitize_key( $key ) . ']">' . wp_kses_post( $option ) . '</label><br/>';
		}

		$html .= '<p class="description">' . $args['desc'] . '</p>';
	}

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_multicheck_callback

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_radio_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	$html = '';

	$class = ftg_sanitize_html_class( $args['field_class'] );

	foreach ( $args['options'] as $key => $option )	{
		$checked = false;

		if ( $ftg_option && $key == $ftg_option )	{
			$checked = true;
		} elseif ( isset( $args['std'] ) && $key == $args['std'] && ! $ftg_option )	{
			$checked = true;
		}

		$html .= '<input name="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']" id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . '][' . ftg_sanitize_key( $key ) . ']" class="' . $class . '" type="radio" value="' . ftg_sanitize_key( $key ) . '" ' . checked( true, $checked, false ) . '/>&nbsp;';

		$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . '][' . ftg_sanitize_key( $key ) . ']">' . esc_html( $option ) . '</label><br/>';
	}

	$html .= '<p class="description">' . apply_filters( 'ftg_after_setting_output', wp_kses_post( $args['desc'] ), $args ) . '</p>';

	echo $html;
} // ftg_radio_callback

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_text_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;
	} else	{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="ftg_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$class       = ftg_sanitize_html_class( $args['field_class'] );
	$readonly    = $args['readonly'] === true    ? ' readonly="readonly"' : '';
    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder']   : '';
	$size        = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html = sprintf(
        '<input type="text" class="%s" id="ftg_settings[%s]" %s value="%s" placeholder="%s"%s />',
        $class . ' ' . sanitize_html_class( $size ) . '-text',
        ftg_sanitize_key( $args['id'] ),
        $name,
        esc_attr( stripslashes( $value ) ),
        $placeholder,
        $readonly
    );

	$html .= sprintf(
        '<label for="ftg_settings[%s]"> %s</label>',
        ftg_sanitize_key( $args['id'] ),
        wp_kses_post( $args['desc'] )
    );

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_text_callback

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_number_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option ) {
		$value = $ftg_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="ftg_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_number_callback

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_textarea_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;
	} else	{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );
    $cols  = isset( $args['cols'] ) && ! empty( absint( $args['cols'] ) ) ? absint( $args['cols'] ) : '50';
    $rows  = isset( $args['rows'] ) && ! empty( absint( $args['rows'] ) ) ? absint( $args['rows'] ) : '5';

	$html = sprintf(
        '<textarea class="%s large-text" cols="%s" rows="%s" id="ftg_settings[%s]" name="ftg_settings[%s]">%s</textarea>',
        $class,
        $cols,
        $rows,
        ftg_sanitize_key( $args['id'] ),
        esc_attr( $args['id'] ),
        esc_textarea( stripslashes( $value ) )
    );
	$html .= sprintf(
        '<label for="ftg_settings[%s]"> %s</label>',
        ftg_sanitize_key( $args['id'] ),
        wp_kses_post( $args['desc'] )
    );

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_textarea_callback

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_password_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;
	} else	{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="password" class="' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']" name="ftg_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_password_callback

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @return	void
 */
function ftg_missing_callback($args) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'feed-them-gallery' ),
		'<strong>' . $args['id'] . '</strong>'
	);
} // ftg_missing_callback

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_select_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;
	} else	{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['placeholder'] ) ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	if ( ! empty( $args['multiple'] ) ) {
		$multiple   = ' MULTIPLE';
		$name_array = '[]';
	} else {
		$multiple   = '';
		$name_array = '';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );

	if ( isset( $args['chosen'] ) ) {
		$class .= ' ftg_select_chosen';
	}

	$html = '<select id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']" name="ftg_settings[' . esc_attr( $args['id'] ) . ']' . $name_array . '" class="' . $class . '"' . $multiple . ' data-placeholder="' . esc_html( $placeholder ) . '" />';

	foreach ( $args['options'] as $option => $name ) {
		if ( ! empty( $multiple ) && is_array( $value ) ) {
			$selected = selected( true, in_array( $option, $value ), false );
		} else	{
			$selected = selected( $option, $value, false );
		}
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_select_callback

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options Array of all the FTG Options
 * @return	void
 */
function ftg_color_select_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;
	} else	{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );

	$html = '<select id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']" class="' . $class . '" name="ftg_settings[' . esc_attr( $args['id'] ) . ']"/>';

	foreach ( $args['options'] as $option => $color ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $color['label'] ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_color_select_callback

/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since	1.3.4
 * @param	array	$args	Arguments passed by the setting
 * @return	void
 */
function ftg_color_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option ) {
		$value = $ftg_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$default     = isset( $args['std'] )         ? $args['std']         : '';
    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$class       = ftg_sanitize_html_class( $args['field_class'] );

	$html = sprintf(
        '<input type="text" class="%s ftg-color-picker" id="ftg_settings[%s]" name="ftg_settings[%s]" value="%s" data-default-color="%s" placeholder="%s" />',
        $class,
        ftg_sanitize_key( $args['id'] ),
        esc_attr( $args['id'] ),
        esc_attr( $value ),
        esc_attr( $default ),
        esc_attr( $placeholder )
    );
    
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_color_callback

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @global	$wp_version		WordPress Version
 */
function ftg_rich_editor_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;

		if ( empty( $args['allow_blank'] ) && empty( $value ) )	{
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
	} else	{
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$rows = isset( $args['size'] ) ? $args['size'] : 20;

	$class = ftg_sanitize_html_class( $args['field_class'] );

	ob_start();
	wp_editor(
		stripslashes( $value ),
		'ftg_settings_' . esc_attr( $args['id'] ),
		array(
			'textarea_name' => 'ftg_settings[' . esc_attr( $args['id'] ) . ']',
			'textarea_rows' => absint( $rows ),
			'editor_class'  => $class
		)
	);
	$html = ob_get_clean();

	$html .= '<br/><label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_rich_editor_callback

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @global	$ftg_options	Array of all the FTG Options
 * @return	void
 */
function ftg_upload_callback( $args ) {
	$ftg_option = ftg_get_option( $args['id'] );

	if ( $ftg_option )	{
		$value = $ftg_option;
	} else	{
		$value = isset($args['std']) ? $args['std'] : '';
	}

	$class = ftg_sanitize_html_class( $args['field_class'] );

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" "' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']" name="ftg_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="ftg_settings_upload_button button-secondary" value="' . __( 'Upload File', 'feed-them-gallery' ) . '"/></span>';
	$html .= '<label for="ftg_settings[' . ftg_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_upload_callback

/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @return	void
 */
function ftg_descriptive_text_callback( $args ) {
	$html = wp_kses_post( $args['desc'] );

	echo apply_filters( 'ftg_after_setting_output', $html, $args );
} // ftg_descriptive_text_callback

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since	1.0
 * @param	arr		$args	Arguments passed by the setting
 * @return	void
 */
function ftg_hook_callback( $args ) {
	do_action( 'ftg_' . $args['id'], $args );
} // ftg_hook_callback

/**
 * Adds the tooltip after the setting field.
 *
 * @since	1.3.4
 * @param	string		$html	HTML output
 * @param	array		$args	Array containing tooltip title and description
 * @return	string		Filtered HTML output
 */
function ftg_add_setting_tooltip( $html, $args ) {
	if ( ! empty( $args['tooltip_title'] ) && ! empty( $args['tooltip_desc'] ) ) {
		$tooltip = '<span alt="f223" class="ftg-help-tip dashicons dashicons-editor-help" title="<strong>' . $args['tooltip_title'] . '</strong>: ' . $args['tooltip_desc'] . '"></span>';
		$html .= $tooltip;
	}

	return $html;
} // ftg_add_setting_tooltip
add_filter( 'ftg_after_setting_output', 'ftg_add_setting_tooltip', 10, 2 );

/**
 * Sanitizes a string key for FTG Settings
 *
 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
 *
 * @since 	1.3.4
 * @param	string		$key	String key
 * @return	string		Sanitized key
 */
function ftg_sanitize_key( $key ) {
	$raw_key = $key;
	$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	/**
	 * Filter a sanitized key string.
	 *
	 * @since  1.3.4
	 * @param  string  $key     Sanitized key.
	 * @param  string  $raw_key The key prior to sanitization.
	 */
	return apply_filters( 'ftg_sanitize_key', $key, $raw_key );
} // ftg_sanitize_key

/**
 * File & Title Renaming Callback
 *
 * @since	1.3.4
 * @param	array         $args	Arguments passed by the setting
 * @return	void
 */
function ftg_file_naming_callback( $args ) {
	global $name_example, $title_example;

    $naming_options = ftg_get_file_name_setting_options();
    $ftg_option     = ftg_get_option( $args['id'] );
	$name_example   = array();
	$title_example  = array();
    ob_start();

    ?>

	<table style="width: 75%;">
		<tr>
			<th scope="row">&nbsp;</th>
			<th scope="row"><?php _e( 'Filename', 'feed-them-gallery' ); ?></th>
			<th scope="row"><?php _e( 'Title', 'feed-them-gallery' ); ?></th>
		</tr>
        <?php foreach( $naming_options as $label => $options ) : ?>
            <tr>
                <th scope="row"><?php echo esc_html( $label ); ?></th>
                <?php foreach( $options as $option => $example ) : ?>
					<?php
						if ( strpos( $option, 'attch_name_' ) !== false )	{
							$name_example[] = $example;
						}
						if ( strpos( $option, 'attch_title_' ) !== false )	{
							$title_example[] = $example;
						}
					?>
                    <td>
                        <?php printf(
                            '<input type="checkbox" name="%s" value="1"%s /> <code>%s</code>',
                            'ftg_settings[' . esc_attr( $args['id'] ) . '][' . esc_attr( $option ) . ']',
                            ! empty( $ftg_option[ $option ] ) ? ' checked="checked"' : '',
                            $example
                        ); ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php
    $output = ob_get_clean();

    echo apply_filters( 'ftg_after_file_name_setting_output', $output, $args );
} // ftg_file_naming_callback

/**
 * Attachment Title Formatting Callback
 *
 * @since	1.3.4
 * @param	array         $args	Arguments passed by the setting
 * @return	void
 */
function ftg_attachment_titles_callback( $args ) {
    $options      = ftg_get_option( $args['id'] );
    $cap_selected = ! empty( $options['cap_options'] ) ? $options['cap_options'] : 'dont_alter';
    ob_start();

    ?>

	<table style="width: 75%;">
		<tr>
			<th scope="row"><?php _e( 'Remove Characters', 'feed-them-gallery' ); ?></th>
			<th scope="row"><?php _e( 'Capitalization Method', 'feed-them-gallery' ); ?></th>
		</tr>
        <tr>
            <td>
                <?php printf(
                    '<input type="checkbox" name="%s" value="1"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][fat_hyphen]',
                    ! empty( $options['fat_hyphen'] ) ? ' checked="checked"' : ''
                ); ?> <label><?php _e( 'Hyphen (-)', 'feed-them-gallery' ); ?></label>
            </td>
            <td>
                <?php printf(
                    '<input type="radio" name="%s" value="cap_all"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][cap_options]',
                    checked( $cap_selected, 'cap_all', false )
                ); ?> <label><?php _e( 'Capitalize All Words', 'feed-them-gallery' ); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <?php printf(
                    '<input type="checkbox" name="%s" value="1"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][fat_underscore]',
                    ! empty( $options['fat_underscore'] ) ? ' checked="checked"' : ''
                ); ?> <label><?php _e( 'Underscore (_)', 'feed-them-gallery' ); ?></label>
            </td>
            <td>
                <?php printf(
                    '<input type="radio" name="%s" value="cap_first"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][cap_options]',
                    checked( $cap_selected, 'cap_first', false )
                ); ?> <label><?php _e( 'Capitalize First Word Only', 'feed-them-gallery' ); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <?php printf(
                    '<input type="checkbox" name="%s" value="1"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][fat_period]',
                    ! empty( $options['fat_period'] ) ? ' checked="checked"' : ''
                ); ?> <label><?php _e( 'Period (.)', 'feed-them-gallery' ); ?></label>
            </td>
            <td>
                <?php printf(
                    '<input type="radio" name="%s" value="all_lower"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][cap_options]',
                    checked( $cap_selected, 'all_lower', false )
                ); ?> <label><?php _e( 'All Words Lower Case', 'feed-them-gallery' ); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <?php printf(
                    '<input type="checkbox" name="%s" value="1"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][fat_tilde]',
                    ! empty( $options['fat_tilde'] ) ? ' checked="checked"' : ''
                ); ?> <label><?php _e( 'Tilde (~)', 'feed-them-gallery' ); ?></label>
            </td>
            <td>
                <?php printf(
                    '<input type="radio" name="%s" value="all_upper"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][cap_options]',
                    checked( $cap_selected, 'all_upper', false )
                ); ?> <label><?php _e( 'All Words Upper Case', 'feed-them-gallery' ); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <?php printf(
                    '<input type="checkbox" name="%s" value="1"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][fat_plus]',
                    ! empty( $options['fat_plus'] ) ? ' checked="checked"' : ''
                ); ?> <label><?php _e( 'Plus (+)', 'feed-them-gallery' ); ?></label>
            </td>
            <td>
                <?php printf(
                    '<input type="radio" name="%s" value="dont_alter"%s />',
                    'ftg_settings[' . esc_attr( $args['id'] ) . '][cap_options]',
                    checked( $cap_selected, 'dont_alter', false )
                ); ?> <label><?php _e( "Don't Alter", 'feed-them-gallery' ); ?></label>
            </td>
        </tr>
        <tr>
            <td><span style="font-size: small; font-style: italic;"><?php _e( 'For image title only. The filename will still contain a hyphen.', 'feed-them-gallery' ); ?></span></td>
            <td><span style="font-size: small; font-style: italic;"><?php _e( 'Works on an individual words. If the title contains no spaces after formatting, only the first letter will be capitalized.', 'feed-them-gallery' ); ?></span></span></td>
        </tr>
    </table>

    <?php
    $output = ob_get_clean();

    echo apply_filters( 'ftg_after_attachment_titles_setting_output', $output, $args );
} // ftg_attachment_titles_callback

/**
 * Get file name options.
 *
 * @since   1.3.4
 * @return  array   Array of file name options
 */
function ftg_get_file_name_setting_options()    {
    /*
     * This is where we define the options for file/title renaming.
     *
     * Note how we alternate between file and title.
     * Arrays are option ID => example text
     */
    $naming_options = array(
        __( 'Include Gallery Name', 'feed-the-gallery' ) => array(
            'attch_name_gallery_name'  => 'this-gallery-name',
            'attch_title_gallery_name' => __( 'This Gallery Name', 'feed-them-gallery' )
        ),
        __( 'Include Gallery ID', 'feed-the-gallery' ) => array(
            'attch_name_post_id'       => '20311',
            'attch_title_post_id'      => '20311'
        ),
        __( 'Include Date', 'feed-the-gallery' ) => array(
            'attch_name_date'          => '08-11-17',
            'attch_title_date'         => '08-11-17'
        ),
        __( 'Include File Name', 'feed-the-gallery' ) => array(
            'attch_name_file_name'     => 'my-image-name',
            'attch_title_file_name'    => __( 'My Image Name', 'feed-them-gallery' )
        ),
        __( 'Include Attachment ID', 'feed-the-gallery' ) => array(
            'attch_name_attch_id'      => '1234',
            'attch_title_attch_id'     => '1234'
        )
    );

    return apply_filters( 'ftg_file_name_setting_options', $naming_options );
} // ftg_get_file_name_setting_options

/**
 * Get timezone options.
 *
 * @since	1.3.4
 * @return	array	Array of timezone options
 */
function ftg_get_timezone_setting_options()	{
	date_default_timezone_set( ftg_get_option( 'timezone', 'America/Los_Angeles' ) );

	$timezones = array(
		'Pacific/Midway'                 => __( '(GMT-11:00) Midway Island, Samoa', 'feed-them-social' ),
		'America/Adak'                   => __( '(GMT-10:00) Hawaii-Aleutian', 'feed-them-social' ),
		'Etc/GMT+10'                     => __( '(GMT-10:00) Hawaii', 'feed-them-social' ),
		'Pacific/Marquesas'              => __( '(GMT-09:30) Marquesas Islands', 'feed-them-social' ),
		'Pacific/Gambier'                => __( '(GMT-09:00) Gambier Islands', 'feed-them-social' ),
		'America/Anchorage'              => __( '(GMT-09:00) Alaska', 'feed-them-social' ),
		'America/Anchorage'              => __( '(GMT-09:00) Gambier Islands', 'feed-them-social' ),
		'America/Ensenada'               => __( '((GMT-08:00) Tijuana, Baja California', 'feed-them-social' ),
		'Etc/GMT+8'                      => __( '(GMT-08:00) Pitcairn Islands', 'feed-them-social' ),
		'America/Los_Angeles'            => __( '(GMT-08:00) Pacific Time (US & Canada)', 'feed-them-social' ),
		'America/Denver'                 => __( '(GMT-07:00) Mountain Time (US & Canada)', 'feed-them-social' ),
		'America/Chihuahua'              => __( '(GMT-07:00) Chihuahua, La Paz, Mazatlan', 'feed-them-social' ),
		'America/Dawson_Creek'           => __( '(GMT-07:00) Arizona', 'feed-them-social' ),
		'America/Belize'                 => __( '(GMT-06:00) Saskatchewan', 'feed-them-social' ),
		'America/Cancun'                 => __( '(GMT-06:00) Guadalajara, Mexico City', 'feed-them-social' ),
		'Chile/EasterIsland'             => __( '(GMT-06:00) Easter Island', 'feed-them-social' ),
		'America/Chicago'                => __( '(GMT-06:00) Central Time (US & Canada)', 'feed-them-social' ),
		'America/New_York'               => __( '(GMT-05:00) Eastern Time (US & Canada)', 'feed-them-social' ),
		'America/Havana'                 => __( '(GMT-05:00) Cuba', 'feed-them-social' ),
		'America/Bogota'                 => __( '(GMT-05:00) Bogota, Lima, Quito, Rio Branco', 'feed-them-social' ),
		'America/Caracas'                => __( '(GMT-04:30) Caracas', 'feed-them-social' ),
		'America/Santiago'               => __( '(GMT-04:00) Santiago', 'feed-them-social' ),
		'America/La_Paz'                 => __( '(GMT-04:00) La Paz', 'feed-them-social' ),
		'Atlantic/Stanley'               => __( '(GMT-04:00) Faukland Islands', 'feed-them-social' ),
		'America/Goose_Bay'              => __( '(GMT-04:00) Atlantic Time (Goose Bay)', 'feed-them-social' ),
		'America/Glace_Bay'              => __( '(GMT-04:00) Atlantic Time (Canada)', 'feed-them-social' ),
		'America/St_Johns'               => __( '(GMT-03:30) Newfoundland', 'feed-them-social' ),
		'America/Araguaina'              => __( '(GMT-03:00) UTC-3', 'feed-them-social' ),
		'America/Montevideo'             => __( '(GMT-03:00) Montevideo', 'feed-them-social' ),
		'America/Miquelon'               => __( '(GMT-03:00) Miquelon, St. Pierre', 'feed-them-social' ),
		'America/Godthab'                => __( '(GMT-03:00) Greenland', 'feed-them-social' ),
		'America/Argentina/Buenos_Aires' => __( '(GMT-03:00) Buenos Aires', 'feed-them-social' ),
		'America/Sao_Paulo'              => __( '(GMT-03:00) Brasilia', 'feed-them-social' ),
		'AAmerica/Noronha'               => __( '(GMT-02:00) Mid-Atlantic', 'feed-them-social' ),
		'Atlantic/Cape_Verde'            => __( '(GMT-01:00) Cape Verde Is.', 'feed-them-social' ),
		'Atlantic/Azores'                => __( '(GMT-01:00) Azores', 'feed-them-social' ),
		'Europe/Belfast'                 => __( '(GMT) Greenwich Mean Time : Belfast', 'feed-them-social' ),
		'Europe/Dublin'                  => __( '(GMT) Greenwich Mean Time : Dublin', 'feed-them-social' ),
		'Europe/Lisbon'                  => __( 'GMT) Greenwich Mean Time : Lisbon', 'feed-them-social' ),
		'Europe/London'                  => __( '(GMT) Greenwich Mean Time : London', 'feed-them-social' ),
		'Africa/Abidjan'                 => __( '(GMT) Monrovia, Reykjavik', 'feed-them-social' ),
		'Europe/Amsterdam'               => __( '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', 'feed-them-social' ),
		'Europe/Belgrade'                => __( '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague', 'feed-them-social' ),
		'Africa/Algiers'                 => __( '(GMT+01:00) West Central Africa', 'feed-them-social' ),
		'Africa/Windhoek'                => __( '(GMT+01:00) Windhoek', 'feed-them-social' ),
		'Asia/Beirut'                    => __( 'GMT+02:00) Beirut', 'feed-them-social' ),
		'Africa/Cairo'                   => __( '(GMT+02:00) Cairo', 'feed-them-social' ),
		'Asia/Gaza'                      => __( '(GMT+02:00) Gaza', 'feed-them-social' ),
		'Africa/Blantyre'                => __( '(GMT+02:00) Harare, Pretoria', 'feed-them-social' ),
		'Asia/Jerusalem'                 => __( '(GMT+02:00) Jerusalem', 'feed-them-social' ),
		'Europe/Minsk'                   => __( '(GMT+02:00) Minsk', 'feed-them-social' ),
		'Asia/Damascus'                  => __( '(GMT+02:00) Syria', 'feed-them-social' ),
		'Europe/Moscow'                  => __( '(GMT+03:00) Moscow, St. Petersburg, Volgograd', 'feed-them-social' ),
		'Africa/Addis_Ababa'             => __( '(GMT+03:00) Nairobi', 'feed-them-social' ),
		'Asia/Tehran'                    => __( '(GMT+03:30) Tehran', 'feed-them-social' ),
		'Asia/Dubai'                     => __( '(GMT+04:00) Abu Dhabi, Muscat', 'feed-them-social' ),
		'Asia/Yerevan'                   => __( '(GMT+04:00) Yerevan', 'feed-them-social' ),
		'Asia/Kabul'                     => __( '(GMT+04:30) Kabul', 'feed-them-social' ),
		'Asia/Yekaterinburg'             => __( '(GMT+05:00) Ekaterinburg', 'feed-them-social' ),
		'Asia/Tashkent'                  => __( '(GMT+05:00) Tashkent', 'feed-them-social' ),
		'Asia/Kolkata'                   => __( '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi', 'feed-them-social' ),
		'Asia/Katmandu'                  => __( '(GMT+05:45) Kathmandu', 'feed-them-social' ),
		'Asia/Dhaka'                     => __( '(GMT+06:00) Astana, Dhaka', 'feed-them-social' ),
		'Asia/Novosibirsk'               => __( '(GMT+06:00) Novosibirsk', 'feed-them-social' ),
		'Asia/Rangoon'                   => __( '(GMT+06:30) Yangon (Rangoon)', 'feed-them-social' ),
		'Asia/Bangkok'                   => __( 'GMT+07:00) Bangkok, Hanoi, Jakarta', 'feed-them-social' ),
		'Asia/Krasnoyarsk'               => __( '(GMT+07:00) Krasnoyarsk', 'feed-them-social' ),
		'Asia/Hong_Kong'                 => __( 'GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi', 'feed-them-social' ),
		'Asia/Irkutsk'                   => __( '(GMT+08:00) Irkutsk, Ulaan Bataar', 'feed-them-social' ),
		'Australia/Perth'                => __( '(GMT+08:00) Perth', 'feed-them-social' ),
		'Australia/Eucla'                => __( '(GMT+08:45) Eucla', 'feed-them-social' ),
		'Asia/Tokyo'                     => __( '(GMT+09:00) Osaka, Sapporo, Tokyo', 'feed-them-social' ),
		'Asia/Seoul'                     => __( '(GMT+09:00) Seoul', 'feed-them-social' ),
		'Asia/Yakutsk'                   => __( '(GMT+09:00) Yakutsk', 'feed-them-social' ),
		'Australia/Darwin'               => __( '(GMT+09:30) Darwin', 'feed-them-social' ),
		'Australia/Brisbane'             => __( '(GMT+10:00) Brisbane', 'feed-them-social' ),
		'Australia/Hobart'               => __( '(GMT+10:00) Sydney', 'feed-them-social' ),
		'Asia/Vladivostok'               => __( '(GMT+10:00) Vladivostok', 'feed-them-social' ),
		'Australia/Lord_Howe'            => __( '(GMT+10:30) Lord Howe Island', 'feed-them-social' ),
		'Etc/GMT-11'                     => __( '(GMT+11:00) Solomon Is., New Caledonia', 'feed-them-social' ),
		'Asia/Magadan'                   => __( '(GMT+11:00) Magadan', 'feed-them-social' ),
		'Pacific/Norfolk'                => __( '(GMT+11:30) Norfolk Island', 'feed-them-social' ),
		'Asia/Anadyr'                    => __( '(GMT+12:00) Anadyr, Kamchatka', 'feed-them-social' ),
		'Pacific/Auckland'               => __( '(GMT+12:00) Auckland, Wellington', 'feed-them-social' ),
		'Etc/GMT-12'                     => __( '(GMT+12:00) Fiji, Kamchatka, Marshall Is.', 'feed-them-social' ),
		'Pacific/Chatham'                => __( 'GMT+12:45) Chatham Islands', 'feed-them-social' ),
		'Pacific/Tongatapu'              => __( '(GMT+13:00) Nuku\'alofa', 'feed-them-social' ),
		'Pacific/Kiritimati'             => __( '(GMT+14:00) Kiritimati', 'feed-them-social' )
	);

	return $timezones;
} // ftg_get_timezone_setting_options

/**
 * Get date format options.
 *
 * @since	1.3.4
 * @return	array	Array of date format options
 */
function ftg_get_date_format_setting_options()	{
	$formats = array(
		'l, F jS, Y \a\t g:ia' => date( 'l, F jS, Y \a\t g:ia' ),
		'F j, Y \a\t g:ia'     => date( 'F j, Y \a\t g:ia' ),
		'F j, Y g:ia'          => date( 'F j, Y g:ia' ),
		'F, Y \a\t g:ia'       => date( 'F, Y \a\t g:ia' ),
		'M j, Y @ g:ia'        => date( 'M j, Y @ g:ia' ),
		'M j, Y @ G:i'         => date( 'M j, Y @ G:i' ),
		'm/d/Y \a\t g:ia'      => date( 'm/d/Y \a\t g:ia' ),
		'm/d/Y @ G:i'          => date( 'm/d/Y @ G:i' ),
		'd/m/Y \a\t g:ia'      => date( 'd/m/Y \a\t g:ia' ),
		'd/m/Y @ G:i'          => date( 'd/m/Y @ G:i' ),
		'Y/m/d \a\t g:ia'      => date( 'Y/m/d \a\t g:ia' ),
		'Y/m/d @ G:i'          => date( 'Y/m/d @ G:i' ),
		'one-day-ago'          => __( '1 day ago', 'feed-them-gallery' ),
		'fts-custom-date'      => __( 'Use Custom Date and Time Option Below', 'feed-them-gallery' )
	);

	return $formats;
} // ftg_get_date_format_setting_options
