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
 * @since	1.4
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
 * @since	1.4
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
 * Add all settings sections and fields.
 *
 * @since	1.0
 * @return	void
*/
function ftg_register_settings() {

	if ( false == get_option( 'ftg_settings' ) ) {
		add_option( 'ftg_settings' );
	}

	foreach ( ftg_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings) {

			// Check for backwards compatibility
			$section_tabs = ftg_get_settings_tab_sections( $tab );
			if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
				$section = 'main';
				$settings = $sections;
			}

			add_settings_section(
				'ftg_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'ftg_settings_' . $tab . '_' . $section
			);

			foreach ( $settings as $option ) {
				// For backwards compatibility
				if ( empty( $option['id'] ) ) {
					continue;
				}

				$args = wp_parse_args( $option, array(
				    'section'       => $section,
				    'id'            => null,
				    'desc'          => '',
				    'name'          => '',
				    'size'          => null,
				    'options'       => '',
				    'std'           => '',
				    'min'           => null,
				    'max'           => null,
				    'step'          => null,
				    'chosen'        => null,
				    'placeholder'   => null,
				    'allow_blank'   => true,
				    'readonly'      => false,
				    'faux'          => false,
				    'tooltip_title' => false,
				    'tooltip_desc'  => false,
				    'field_class'   => ''
				) );

				add_settings_field(
					'ftg_settings[' . $args['id'] . ']',
					$args['name'],
					function_exists( 'ftg_' . $args['type'] . '_callback' ) ? 'ftg_' . $args['type'] . '_callback' : 'ftg_missing_callback',
					'ftg_settings_' . $tab . '_' . $section,
					'ftg_settings_' . $tab . '_' . $section,
					$args
				);
			}
		}

	}

	// Creates our settings in the options table
	register_setting( 'ftg_settings', 'ftg_settings', 'ftg_settings_sanitize' );

} // ftg_register_settings
add_action( 'admin_init', 'ftg_register_settings' );

/**
 * Retrieve the array of plugin settings.
 *
 * @since	1.4
 * @return	array    Array of plugin settings to register
*/
function ftg_get_registered_settings() {

	/**
	 * 'Whitelisted' FTG settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings.
	 */
	$ftg_settings = array(
		/** General Settings */
		'general' => apply_filters( 'ftg_settings_general',
			array(
				'main' => array(
					'use_attachment_naming' => array(
						'id'    => 'use_attachment_naming',
						'name'  => __( 'Rename on Upload?', 'feed-them-gallery' ),
						'desc'  => __( 'Enable to use Attachment File and Title Renaming when uploading each image.', 'feed-them-gallery' ),
						'type'  => 'checkbox',
						'std'   => 0,
                        'class' => 'ftg_setting_option_attachment_naming'
					),
                    'file_naming' => array(
						'id'      => 'file_naming',
						'name'    => __( 'File & Title Renaming', 'feed-them-gallery' ),
						'type'    => 'file_naming'
					)
                ),
                'formatting' => array(
                    'attachment_titles' => array(
						'id'      => 'attachment_titles',
						'name'    => __( 'Format Attachment Titles', 'feed-them-gallery' ),
						'type'    => 'attachment_titles'
					)
				),
                'options' => array(
                    'fat_alt' => array(
                        'id'      => 'fat_alt',
                        'name'    => __( 'Title as Alt Text', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 0,
                        'desc'    => __( "If enabled, the attachment title will be added to the 'Alternative Text' field", 'feed-them-gallery' )
                    ),
                    'fat_caption' => array(
                        'id'      => 'fat_caption',
                        'name'    => __( 'Title as Caption', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 0,
                        'desc'    => __( "If enabled, the attachment title will be added to the 'Caption' field", 'feed-them-gallery' )
                    ),
                    'fat_description' => array(
                        'id'      => 'fat_description',
                        'name'    => __( 'Title as Description', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 0,
                        'desc'    => __( "If enabled, the attachment title will be added to the 'Description' field", 'feed-them-gallery' )
                    )
                )
            )		
		),
        'styles' => apply_filters( 'ftg_settings_styles',
			array(
				'main' => array(
					'text_color' => array(
                        'id'          => 'text_color',
                        'name'        => __( 'Title Text Color', 'feed-them-gallery' ),
                        'type'        => 'color',
                        'placeholder' => __( '#222', 'feed-them-gallery' )
                    ),
                    'text_size' => array(
                        'id'          => 'text_size',
                        'name'        => __( 'Title Text Size', 'feed-them-gallery' ),
                        'type'        => 'text',
                        'placeholder' => '14px',
                        'size'        => 'small'
                    ),
                    'description_color' => array(
                        'id'          => 'description_color',
                        'name'        => __( 'Description Text Color', 'feed-them-gallery' ),
                        'type'        => 'color',
                        'placeholder' => __( '#222', 'feed-them-gallery' )
                    ),
                    'description_size' => array(
                        'id'          => 'description_size',
                        'name'        => __( 'Description Text Size', 'feed-them-gallery' ),
                        'type'        => 'text',
                        'placeholder' => '14px',
                        'size'        => 'small'
                    ),
                    'link_color' => array(
                        'id'          => 'link_color',
                        'name'        => __( 'Link Text Color', 'feed-them-gallery' ),
                        'type'        => 'color',
                        'placeholder' => __( '#ddd', 'feed-them-gallery' )
                    ),
                    'link_color_hover' => array(
                        'id'          => 'link_color_hover',
                        'name'        => __( 'Link Hover Text Size', 'feed-them-gallery' ),
                        'type'        => 'text',
                        'placeholder' => '14px',
                        'size'        => 'small'
                    ),
                    'post_time' => array(
                        'id'          => 'post_time',
                        'name'        => __( 'Date Text Color', 'feed-them-gallery' ),
                        'type'        => 'color',
                        'placeholder' => __( '#ddd', 'feed-them-gallery' )
                    )
                ),
                'css' => array(
                    'custom_css_second' => array(
                        'id'      => 'custom_css_second',
                        'name'    => __( 'Use Custom CSS?', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 0,
                        'desc'    => __( 'If enabled, CSS you enter below will be loaded and used', 'feed-them-gallery' )
                    ),
                    'custom_css' => array(
                        'id'      => 'custom_css',
                        'name'    => __( 'Custom CSS?', 'feed-them-gallery' ),
                        'type'    => 'textarea',
                        'desc'    => __( 'Add your custom CSS code into the textarea', 'feed-them-gallery' )
                    )
                )
            )
        ),
        'misc' => apply_filters( 'ftg_settings_misc',
			array(
                'main' => array(
                    'fix_magnific' => array(
                        'id'      => 'fix_magnific',
                        'name'    => __( 'Disable Magnific Popup CSS?', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 0,
                        'desc'    => __( 'Enable this if your theme is already loading the style sheet for the popup.', 'feed-them-gallery' )
                    ),
                    'duplicate_post_show' => array(
                        'id'      => 'duplicate_post_show',
                        'name'    => __( 'Disable Duplicate Gallery?', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 0,
                        'desc'    => __( 'Enable this if already have a duplicate post plugin installed.', 'feed-them-gallery' )
                    ),
                    'show_admin_bar' => array(
                        'id'      => 'show_admin_bar',
                        'name'    => __( 'Show Admin Menu Bar?', 'feed-them-gallery' ),
                        'type'    => 'checkbox',
                        'std'     => 1
                    )
                )
            )		
		)
	);

    return apply_filters( 'ftg_registered_settings', $ftg_settings );
} // ftg_get_registered_settings

/**
 * Settings Sanitization.
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input.
 *
 * @since	1.4
 * @param	array	$input	The value inputted in the field.
 * @return	string	$input	Sanitizied value.
 */
function ftg_settings_sanitize( $input = array() ) {

	global $ftg_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = ftg_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
	$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

	$input = $input ? $input : array();

	$input = apply_filters( 'ftg_settings_' . $tab . '-' . $section . '_sanitize', $input );
	if ( 'main' === $section )  {
		// Check for extensions that aren't using new sections
		$input = apply_filters( 'ftg_settings_' . $tab . '_sanitize', $input );

		// Check for an override on the section for when main is empty
		if ( ! empty( $_POST['ftg_section_override'] ) ) {
			$section = sanitize_text_field( $_POST['ftg_section_override'] );
		}
	}

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[ $key ] = apply_filters( 'ftg_settings_sanitize_' . $type, $value, $key );
		}

		// Specific key filter
		$input[ $key ] = apply_filters( 'ftg_settings_sanitize_' . $key, $value );

		// General filter
		$input[ $key ] = apply_filters( 'ftg_settings_sanitize', $input[ $key ], $key );

	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	$main_settings    = $section == 'main' ? $settings[ $tab ] : array(); // Check for extensions that aren't using new sections
	$section_settings = ! empty( $settings[ $tab ][ $section ] ) ? $settings[ $tab ][ $section ] : array();

	$found_settings = array_merge( $main_settings, $section_settings );

	if ( ! empty( $found_settings ) ) {
		foreach ( $found_settings as $key => $value ) {

			// Settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if ( is_numeric( $key ) ) {
				$key = $value['id'];
			}

			if ( empty( $input[ $key ] ) && isset( $ftg_options[ $key ] ) ) {
				unset( $ftg_options[ $key ] );
			}
		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $ftg_options, $input );

	add_settings_error( 'ftg-notices', '', __( 'Settings updated.', 'feed-them-gallery' ), 'updated' );

	return $output;
} // ftg_settings_sanitize

/**
 * Sanitize text fields
 *
 * @since	1.4
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
 * @since	1.4
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
 * Retrieve settings tabs
 *
 * @since	1.4
 * @return	array		$tabs
 */
function ftg_get_settings_tabs() {

	$settings = ftg_get_registered_settings();

	$tabs                     = array();
	$tabs['general']          = __( 'Attachments', 'feed-them-gallery' );
    $tabs['styles']           = __( 'Gallery Styling', 'feed-them-gallery' );
    $tabs['misc']             = __( 'Misc', 'feed-them-gallery' );

	return apply_filters( 'ftg_settings_tabs', $tabs );
} // ftg_get_settings_tabs

/**
 * Retrieve settings tabs
 *
 * @since	1.4
 * @return	array		$section
 */
function ftg_get_settings_tab_sections( $tab = false ) {

	$tabs     = false;
	$sections = ftg_get_registered_settings_sections();

	if( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	} else if ( $tab ) {
		$tabs = false;
	}

	return $tabs;
} // ftg_get_settings_tab_sections

/**
 * Get the settings sections for each tab
 * Uses a static to avoid running the filters on every request to this function
 *
 * @since	1.4
 * @return	array		Array of tabs and sections
 */
function ftg_get_registered_settings_sections() {

	static $sections = false;

	if ( false !== $sections ) {
		return $sections;
	}

	$sections = array(
		'general' => apply_filters( 'ftg_settings_sections_general', array(
			'main'       => __( 'Renaming', 'feed-them-gallery' ),
            'formatting' => __( 'Title Format', 'feed-them-gallery' ),
            'options'    => __( 'Title Options', 'feed-them-gallery' )
		) ),
        'styles'  => apply_filters( 'ftg_settings_sections_styles', array(
            'main'       => __( 'Image Color & Size', 'feed-them-gallery' ),
            'css'        => __( 'Custom CSS', 'feed-them-gallery' )
        ) ),
        'misc'  => apply_filters( 'ftg_settings_sections_misc', array(
            'main'       => __( 'General', 'feed-them-gallery' )
        ) )
	);

	$sections = apply_filters( 'ftg_settings_sections', $sections );

	return $sections;
} // ftg_get_registered_settings_sections

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
 * @since	1.4
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
 * @since	1.4
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
 * @since 	1.4
 * @param	string		$key	String key
 * @return	string		Sanitized key
 */
function ftg_sanitize_key( $key ) {
	$raw_key = $key;
	$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	/**
	 * Filter a sanitized key string.
	 *
	 * @since  1.4
	 * @param  string  $key     Sanitized key.
	 * @param  string  $raw_key The key prior to sanitization.
	 */
	return apply_filters( 'ftg_sanitize_key', $key, $raw_key );
} // ftg_sanitize_key

/**
 * File & Title Renaming Callback
 *
 * @since	1.4
 * @param	array         $args	Arguments passed by the setting
 * @return	void
 */
function ftg_file_naming_callback( $args ) {
    $naming_options = ftg_get_file_name_setting_options();
    $ftg_option     = ftg_get_option( $args['id'] );
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
 * @since	1.4
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
    </table>

    <?php
    $output = ob_get_clean();

    echo apply_filters( 'ftg_after_attachment_titles_setting_output', $output, $args );
} // ftg_attachment_titles_callback

/**
 * Get file name options.
 *
 * @since   1.4
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
