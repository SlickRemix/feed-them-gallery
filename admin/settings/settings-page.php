<?php
/**
 * Settings Page
 *
 * Class Feed Them Gallery Settings Page
 *
 * @class    Settings_Page
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

namespace feed_them_gallery;

/**
 * Class Settings_Page
 */
class Settings_Page {

	/**
	 * Load Function
	 *
	 * Load up all our actions and filters.
	 *
	 * @since 1.0.0
	 */
	public static function load() {
		$instance = new self();

		// Add Actions and Filters.
		$instance->hooks();
	}

	/**
	 * Add Action Filters
	 *
	 * Add Settings to our menu.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

        // Add the settings menu page
        add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

        // Register Settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Premium extension license upsells
        //add_filter( 'ftg_settings_licenses', array( $this, 'premium_extension_license_fields' ), 100 );

		// Additional date format fields
		add_filter( 'ftg_after_setting_output', array( $this, 'date_translate_fields' ), 10, 2 );

		// Add renaming informational text
		add_action( 'ftg_settings_tab_top_general_main', array( $this, 'attach_rename_note' ) );

        // Add title options informational text
		add_action( 'ftg_settings_tab_bottom_general_options', array( $this, 'title_options_note' ) );

		// Add file name and title examples
		add_action( 'ftg_settings_tab_bottom_general_main', array( $this, 'file_title_examples' ) );

		// Add title format examples
		add_action( 'ftg_settings_tab_bottom_general_formatting', array( $this, 'title_format_example' ) );
        add_action( 'ftg_settings_tab_bottom_general_options', array( $this, 'title_format_example' ) );

        // Add authors note
        add_action( 'ftg_settings_bottom', array( $this, 'authors_note' ) );
	} // hooks

	/**
	 * Settings_Page constructor.
	 */
	public function __construct() {}

	/**
	 * FT Gallery Submenu Pages
	 *
	 * Admin Submenu buttons.
	 *
	 * @since 1.0.0
	 */
	public function add_submenu_page() {
		// Settings Page.
		add_submenu_page(
			'edit.php?post_type=ft_gallery',
			esc_html__( 'Settings', 'feed-them-gallery' ),
			esc_html__( 'Settings', 'feed-them-gallery' ),
			'manage_options',
			'ft-gallery-settings-page',
			array( $this, 'display_settings_page' )
		);

	}

    /**
     * Add all settings sections and fields.
     *
     * @since	1.0
     * @return	void
    */
    public function register_settings() {

        if ( false == get_option( 'ftg_settings' ) ) {
            add_option( 'ftg_settings' );
        }

        foreach ( $this->get_registered_settings() as $tab => $sections ) {
            foreach ( $sections as $section => $settings) {

                // Check for backwards compatibility
                $section_tabs = $this->get_settings_tab_sections( $tab );
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
        register_setting( 'ftg_settings', 'ftg_settings', array( 'sanitize_callback' => array( $this, 'settings_sanitize' ) ) );

    } // register_settings

    /**
     * Retrieve the array of plugin settings.
     *
     * @since	1.3.4
     * @return	array    Array of plugin settings to register
     */
    public function get_registered_settings() {

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
                            'desc'  => __( 'Enable to use Attachment File and Title renaming when uploading each image.', 'feed-them-gallery' ),
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
						'timezone' => array(
							'id'      => 'timezone',
							'name'    => __( 'TimeZone', 'feed-them-gallery' ),
							'type'    => 'select',
							'options' => ftg_get_timezone_setting_options(),
							'std'     => 'America/Los_Angeles'
						),
						'date_time_format' => array(
							'id'            => 'date_time_format',
							'name'          => __( 'Image Date Format', 'feed-them-gallery' ),
							'type'          => 'select',
							'options'       => ftg_get_date_format_setting_options(),
							'std'           => 'l, F jS, Y \a\t g:ia',
                            'field_class'   => 'ftg_date_time_format'
						),
                        'custom_date' => array(
                            'id'            => 'custom_date',
							'name'          => __( 'Custom Date', 'feed-them-gallery' ),
							'type'          => 'text',
							'std'           => '',
                            'placeholder'   => __( 'Date', 'feed-them-gallery' )
                        ),
                        'custom_time' => array(
                            'id'            => 'custom_time',
							'name'          => __( 'Custom Time', 'feed-them-gallery' ),
							'type'          => 'text',
							'std'           => '',
                            'placeholder'   => __( 'Time', 'feed-them-gallery' )
                        ),
                        'custom_date_time_desc' => array(
                            'id'            => 'custom_date_time_desc',
                            'type'          => 'descriptive_text',
							'desc'          => sprintf(
                                __( 'This will override the date and time above', 'feed-them-gallery' ) . '<br>' .
                                '<a href="%s" target="_blank">%s.</a>',
                                'https://codex.wordpress.org/Formatting_Date_and_Time',
                                __( 'Options for custom date and time formatting', 'feed-them-gallery' )
                            )
                        )
					),
					'color_size' => array(
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
                        ),
						'show_powered_by' => array(
							'id'      => 'show_powered_by',
							'name'    => __( 'Show Powered by Text?', 'feed-them-gallery' ),
							'type'    => 'checkbox',
							'std'     => 0,
							'desc'    => __( 'If enabled, powered by text will appear in the popup. Awesome! Thanks so much for sharing.', 'feed-them-gallery' )
						),
                        'remove_on_uninstall' => array(
							'id'      => 'remove_on_uninstall',
							'name'    => __( 'Remove Data on Uninstall?', 'feed-them-gallery' ),
							'type'    => 'checkbox',
							'std'     => 0,
							'desc'    => __( 'Check this box if you would like Feed Them Gallery and its extensions to completely remove all of its data when the plugin is deleted.', 'feed-them-gallery' )
						)
                    )
                )		
            ),
            /** License Settings */
            'licenses' => apply_filters( 'ftg_settings_licenses',
                array()
            )
        );

        return apply_filters( 'ftg_registered_settings', $ftg_settings );
    } // get_registered_settings

    /**
     * Adds premium plugins not yet installed to settings.
     *
     * @since   1.0
     * @param   array   $settings   Array of license settings
     * @return  array   Array of license settings
     */
    public function premium_extension_license_fields( $settings )   {
        $plugins          = ft_gallery_premium_plugins();
        $plugins          = apply_filters( 'ftg_unlicensed_plugins_settings', $plugins );
        $license_settings = array();

        foreach( $plugins as $plugin => $data ) {
            $license_settings[] = array(
                'id'   => "{$plugin}_license_upsell",
                'name' => sprintf( __( '%1$s', 'feed-them-gallery' ), $data['title'] ),
                'type' => 'premium_plugin',
                'data' => $data
            );
        }

		return array_merge( $settings, $license_settings );
    } // premium_extension_license_fields

    /**
     * Retrieve settings tabs
     *
     * @since	1.3.4
     * @return	array		$tabs
     */
    public function get_settings_tabs() {

        $settings = $this->get_registered_settings();

        $tabs                     = array();
        $tabs['general']          = __( 'Attachments', 'feed-them-gallery' );
        $tabs                     = apply_filters( 'ftg_settings_tabs_after_general', $tabs );
        $tabs['styles']           = __( 'Gallery Styling', 'feed-them-gallery' );
        $tabs                     = apply_filters( 'ftg_settings_tabs_after_styles', $tabs );
        $tabs['misc']             = __( 'Misc', 'feed-them-gallery' );
        $tabs                     = apply_filters( 'ftg_settings_tabs_after_misc', $tabs );

        if ( ! empty( $settings['extensions'] ) ) {
            $tabs['extensions'] = __( 'Extensions', 'feed-them-gallery' );
        }

        if ( ! empty( $settings['licenses'] ) ) {
            $tabs['licenses'] = __( 'Licenses', 'feed-them-gallery' );
        }

        return apply_filters( 'ftg_settings_tabs', $tabs );
    } // get_settings_tabs

    /**
     * Retrieve settings tabs
     *
     * @since	1.3.4
     * @return	array		$section
     */
    public function get_settings_tab_sections( $tab = false ) {

        $tabs     = false;
        $sections = $this->get_registered_settings_sections();

        if( $tab && ! empty( $sections[ $tab ] ) ) {
            $tabs = $sections[ $tab ];
        } else if ( $tab ) {
            $tabs = false;
        }

        return $tabs;
    } // get_settings_tab_sections

    /**
     * Get the settings sections for each tab
     * Uses a static to avoid running the filters on every request to this function
     *
     * @since	1.3.4
     * @return	array		Array of tabs and sections
     */
    public function get_registered_settings_sections() {

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
				'main'       => __( 'Image Date Options', 'feed-them-gallery' ),
                'color_size' => __( 'Image Color & Size', 'feed-them-gallery' ),
                'css'        => __( 'Custom CSS', 'feed-them-gallery' )
            ) ),
            'misc'  => apply_filters( 'ftg_settings_sections_misc', array(
                'main'       => __( 'General', 'feed-them-gallery' )
            ) ),
            'extensions' => apply_filters( 'ftg_settings_sections_extensions', array(
                'main'       => __( 'Main', 'feed-them-gallery' )
            ) ),
            'licenses'   => apply_filters( 'ftg_settings_sections_licenses', array() )
        );

        $sections = apply_filters( 'ftg_settings_sections', $sections );

        return $sections;
    } // registered_settings_sections

    /**
     * Settings Sanitization.
     *
     * Adds a settings error (for the updated message)
     * At some point this will validate input.
     *
     * @since	1.3.4
     * @param	array	$input	The value inputted in the field.
     * @return	string	$input	Sanitizied value.
     */
    public function settings_sanitize( $input = array() ) {

        global $ftg_options;

        if ( empty( $_POST['_wp_http_referer'] ) ) {
            return $input;
        }

        parse_str( $_POST['_wp_http_referer'], $referrer );

        $settings = $this->get_registered_settings();
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

        add_settings_error( 'ftg-notices', esc_attr( 'settings_updated' ), __( 'Settings updated.', 'feed-them-gallery' ), 'updated' );

        return $output;
    } // settings_sanitize

    /**
     * Settings Page
     *
     * Feed Them Gallery Settings Page
     *
     * @since   1.3.4
     */
    public function display_settings_page()  {
        if ( ! current_user_can( 'manage_options' ) )	{
            wp_die(
                '<h1>' . __( 'Cheatin&#8217; uh?', 'feed-them-gallery' ) . '</h1>' .
                '<p>'  . __( 'You do not have permission to access this page.', 'feed-them-gallery' ) . '</p>',
                403
            );
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        $settings_tabs = $this->get_settings_tabs();
        $settings_tabs = empty( $settings_tabs ) ? array() : $settings_tabs;
        $active_tab    = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
        $active_tab    = array_key_exists( $active_tab, $settings_tabs ) ? $active_tab : 'general';
        $sections      = $this->get_settings_tab_sections( $active_tab );
        $key           = 'main';

        if ( is_array( $sections ) ) {
            $key = key( $sections );
        }

        $registered_sections = $this->get_settings_tab_sections( $active_tab );
        $section             = isset( $_GET['section'] ) && ! empty( $registered_sections ) && array_key_exists( $_GET['section'], $registered_sections ) ? sanitize_text_field( $_GET['section'] ) : $key;

        // Unset 'main' if it's empty and default to the first non-empty if it's the chosen section
        $all_settings = $this->get_registered_settings();

        // Let's verify we have a 'main' section to show
        $has_main_settings = true;
        if ( empty( $all_settings[ $active_tab ]['main'] ) )	{
            $has_main_settings = false;
        }

        // Check for old non-sectioned settings
        if ( ! $has_main_settings )	{
            foreach( $all_settings[ $active_tab ] as $sid => $stitle )	{
                if ( is_string( $sid ) && is_array( $sections ) && array_key_exists( $sid, $sections ) )	{
                    continue;
                } else	{
                    $has_main_settings = true;
                    break;
                }
            }
        }

        $override = false;
        if ( false === $has_main_settings ) {
            unset( $sections['main'] );

            if ( 'main' === $section ) {
                foreach ( $sections as $section_key => $section_title ) {
                    if ( ! empty( $all_settings[ $active_tab ][ $section_key ] ) ) {
                        $section  = $section_key;
                        $override = true;
                        break;
                    }
                }
            }
        }

        ob_start();
        
        ?>
        <script>
            jQuery(document).ready(function ($) {
                var ftg_color_picker = $('.ftg-color-picker');

                if( ftg_color_picker.length ) {
                    ftg_color_picker.wpColorPicker();
                }
            });
        </script>
        <div class="wrap <?php echo 'wrap-' . $active_tab; ?>">
            <h1 class="wp-heading-inline"><?php _e( 'Settings', 'feed-them-gallery' ); ?></h1>
            <h1 class="nav-tab-wrapper">
                <?php
                foreach( $this->get_settings_tabs() as $tab_id => $tab_name ) {

                    $tab_url = add_query_arg( array(
                        'post_type'        => 'ft_gallery',
                        'page'             => 'ft-gallery-settings-page',
                        'settings-updated' => false,
                        'tab'              => $tab_id
                    ), admin_url( 'edit.php' ) );

                    // Remove the section from the tabs so we always end up at the main section
                    $tab_url = remove_query_arg( 'section', $tab_url );

                    $active = $active_tab == $tab_id ? ' nav-tab-active' : '';

                    echo '<a href="' . esc_url( $tab_url ) . '" class="nav-tab' . $active . '">';
                        echo esc_html( $tab_name );
                    echo '</a>';
                }
                ?>
            </h1>
            <?php

            $number_of_sections = is_array( $sections ) ? count( $sections ) : 0;
            $number = 0;
            if ( $number_of_sections > 1 ) {
                echo '<div><ul class="subsubsub">';
                foreach( $sections as $section_id => $section_name ) {
                    echo '<li>';
                    $number++;
                    $tab_url = add_query_arg( array(
                        'post_type'        => 'ft_gallery',
                        'page'             => 'ft-gallery-settings-page',
                        'settings-updated' => false,
                        'tab'              => $active_tab,
                        'section'          => $section_id
                    ), admin_url( 'edit.php' ) );

                    /**
                     * Allow filtering of the section URL.
                     *
                     * Enables plugin authors to insert links to non-setting pages as sections.
                     *
                     * @since	1.1.10
                     * @param	str		The section URL
                     * @param	str		The section ID (array key)
                     * @param	str		The current active tab
                     * @return	str
                     */
                    $tab_url = apply_filters( 'ftg_options_page_section_url', $tab_url, $section_id, $active_tab );

                    $class = '';
                    if ( $section == $section_id ) {
                        $class = 'current';
                    }
                    echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';

                    if ( $number != $number_of_sections ) {
                        echo ' | ';
                    }
                    echo '</li>';
                }
                echo '</ul></div>';
            }
            ?>
            <div id="tab_container">
                <form method="post" action="options.php">
                    <table class="form-table">
                    <?php

                    settings_fields( 'ftg_settings' );

                    if ( 'main' === $section ) {
                        do_action( 'ftg_settings_tab_top', $active_tab );
                    }

                    do_action( 'ftg_settings_tab_top_' . $active_tab . '_' . $section );

                    do_settings_sections( 'ftg_settings_' . $active_tab . '_' . $section );

                    do_action( 'ftg_settings_tab_bottom_' . $active_tab . '_' . $section  );

                    // If the main section was empty and we overrode the view with the next subsection, prepare the section for saving
                    if ( true === $override ) {
                        ?><input type="hidden" name="ftg_section_override" value="<?php echo $section; ?>" /><?php
                    }
                    ?>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div><!-- #tab_container-->
            <?php do_action( 'ftg_settings_bottom' ); ?>
        </div><!-- .wrap -->
        <?php
        echo ob_get_clean();
    }

	/**
	 * Adds the translation fields to the image date setting field.
	 *
	 * @since	1.3.4
	 * @param	string	$html	HTML output
	 * @param	array	$args	Array of arguments passed to setting
	 * @return	string	HTML output
	 */
	public function date_translate_fields( $html, $args )	{
		if ( 'date_time_format' == $args['id'] )	{
			ob_start();

            $style = 'one-day-ago' != ftg_get_option( 'date_time_format' ) ? ' style="display: none;"' : '';
			?>

			<tr class="custom_time_ago_wrap"<?php echo $style; ?>>
				<th scope="row"><?php _e( 'Translations for 1 day ago', 'feed-them-gallery' ); ?></th>
				<td>&nbsp;</td>
			</tr>

			<?php
			foreach( $this->get_translation_fields() as $field => $value ) : ?>
				<tr class="custom_time_ago_wrap"<?php echo $style; ?>>
					<th scope="row"><?php echo str_replace( 'language_', '', esc_html( $field ) ); ?></th>
					<td>
					<?php ftg_text_callback( array(
						'id'          => $field,
						'std'         => $value,
                        'readonly'    => 'false',
                        'field_class' => '',
                        'desc'        => ''
					) ); ?>
					</td>
				</tr>

			<?php endforeach;

			$html .= ob_get_clean();
		}

		return $html;
	} // date_translate_fields

    /**
	 * Adds the custom date/time fields to the image date setting field.
	 *
	 * @since	1.3.4
	 * @param	string	$html	HTML output
	 * @param	array	$args	Array of arguments passed to setting
	 * @return	string	HTML output
	 */
	public function custom_date_time_fields( $html, $args )	{
		if ( 'date_time_format' == $args['id'] )	{
			ob_start();

            $style = 'fts-custom-date' != ftg_get_option( 'date_time_format' ) ? ' style="display: none;"' : '';
			?>

			<tr class="custom_time_ago_wrap"<?php echo $style; ?>>
				<th scope="row"><?php _e( 'Translations for 1 day ago', 'feed-them-gallery' ); ?></th>
				<td>&nbsp;</td>
			</tr>

			<?php
			foreach( $this->get_translation_fields() as $field => $value ) : ?>
				<tr class="custom_time_ago_wrap"<?php echo $style; ?>>
					<th scope="row"><?php echo str_replace( 'language_', '', esc_html( $field ) ); ?></th>
					<td>
					<?php ftg_text_callback( array(
						'id'          => $field,
						'std'         => $value,
                        'readonly'    => 'false',
                        'field_class' => '',
                        'desc'        => ''
					) ); ?>
					</td>
				</tr>

			<?php endforeach;

			$html .= ob_get_clean();
		}

		return $html;
	} // custom_date_time_fields

	/**
	 * Retrieve the translation fields.
	 *
	 * @since	1.3.4
	 * @return	array	Array of fields and defaults
	 */
	public function get_translation_fields()	{
		$fields = array(
			'language_second'  => ftg_get_option( 'language_second', __( 'second', 'feed-them-gallery' ) ),
			'language_seconds' => ftg_get_option( 'language_seconds', __( 'seconds', 'feed-them-gallery' ) ),
			'language_minute'  => ftg_get_option( 'language_minute', __( 'minute', 'feed-them-gallery' ) ),
			'language_minutes' => ftg_get_option( 'language_minutes', __( 'minutes', 'feed-them-gallery' ) ),
			'language_hour'    => ftg_get_option( 'language_hour', __( 'hour', 'feed-them-gallery' ) ),
			'language_hours'   => ftg_get_option( 'language_hours', __( 'hours', 'feed-them-gallery' ) ),
			'language_day'     => ftg_get_option( 'language_day', __( 'day', 'feed-them-gallery' ) ),
			'language_days'    => ftg_get_option( 'language_days', __( 'days', 'feed-them-gallery' ) ),
			'language_week'    => ftg_get_option( 'language_week', __( 'week', 'feed-them-gallery' ) ),
			'language_weeks'   => ftg_get_option( 'language_weeks', __( 'weeks', 'feed-them-gallery' ) ),
			'language_month'   => ftg_get_option( 'language_month', __( 'month', 'feed-them-gallery' ) ),
			'language_months'  => ftg_get_option( 'language_months', __( 'months', 'feed-them-gallery' ) ),
			'language_year'    => ftg_get_option( 'language_year', __( 'year', 'feed-them-gallery' ) ),
			'language_years'   => ftg_get_option( 'language_years', __( 'years', 'feed-them-gallery' ) ),
			'language_ago'     => ftg_get_option( 'language_ago', __( 'ago', 'feed-them-gallery' ) ),
		);

		return $fields;
	} // get_translation_fields

	/**
	 * Adds the renaming notes to the top of the Attachments Renaming/Titles sections.
	 *
	 * @since	1.3.4
	 * @return	void
	 */
	public function attach_rename_note()	{
		ob_start(); ?>
			<div class="clear"></div>
			<p>
			<?php
				_e( 'Use attachment renaming when importing/uploading attachments. This will overwrite original Filename.', 'feed-them-gallery' ); ?>
			<br>
			<?php
				_e( '<strong>Below are examples of what the attachment filenames and titles will look like after uploading</strong>: (Click "Save All Changes" to view Examples)', 'feed-them-gallery' ); ?>
			</p>
		<?php echo ob_get_clean();
	} // attach_rename_note

    /**
	 * Adds the notes to the bottom of the Title Options sections.
	 *
	 * @since	1.3.4
	 * @return	void
	 */
	public function title_options_note()	{
		ob_start(); ?>
			<div class="clear"></div>
			<p>
			<?php
				_e( '<strong>Below is an example of what the attachment titles will look like after uploading</strong>: (Click "Save All Changes" to view Examples)', 'feed-them-gallery' ); ?>
            <br>
            <span style="font-color: #666; font-style: italic;"><?php _e( 'Note: Title will come from filename of uploaded attachment. You may still set a custom name for each photo after uploaded.', 'feed-them-gallery' ); ?></span>
			</p>
		<?php echo ob_get_clean();
	} // title_options_note

	/**
	 * Outputs the file name and title examples.
	 *
	 * @since	1.3.4
	 * @return	void
	 */
	public function file_title_examples()	{
		global $name_example, $title_example;

		ob_start(); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Example Filename:', 'feed-them-gallery' ); ?></th>
				<td><code><em><?php echo implode( '-', $name_example ); ?>.jpg</em></code></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Example Title:', 'feed-them-gallery' ); ?></th>
				<td><code><em><?php echo implode( ' ', $title_example ); ?></em></code></td>
			</tr>
		</table>
		<?php echo ob_get_clean();
	} // file_title_examples

	/**
	 * Outputs the title format example.
	 *
	 * @since	1.3.4
	 * @return	void
	 */
	public function title_format_example()	{
		$gallery = new Gallery();

		ob_start(); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Example Title:', 'feed-them-gallery' ); ?></th>
				<td><code><em><?php echo $gallery->ft_gallery_format_attachment_title( 'Gallery Image Title' ); ?></em></code></td>
			</tr>
		</table>
		<?php echo ob_get_clean();
	} // title_format_example

    /**
     * Adds the authors note to the bottom of all FTG settings pages.
     *
     * @since   1.3.4
     * @return  string
     */
    public function authors_note()  {
        ob_start(); ?>

        <h1 class="plugin-author-note"><?php esc_html_e( 'Plugin Authors Note', 'feed-them-gallery' ); ?></h1>
		<div class="fts-plugin-reviews">
			<div class="fts-plugin-reviews-rate">
                <?php printf(
                    __( 'Feed Them Gallery was created by 2 Brothers, Spencer and Justin Labadie. That\'s it, 2 people! We spend all our time creating and supporting our plugins. Show us some love if you like our plugin and leave a quick review for us, it will make our day! <a href="%s" target="_blank">Leave us a Review ★★★★★</a>', 'feed-them-gallery' ),
                    'https://www.facebook.com/pg/SlickRemix/reviews/?ref=page_internal'
                ); ?>
                        
			</div>
			<div class="fts-plugin-reviews-support">
                <?php printf(
                    __( 'If you\'re having troubles getting setup please contact us. We will respond within 24hrs, but usually within 1-6hrs. <a href="%s" target="_blank">Create Support Ticket</a>', 'feed-them-gallery' ),
                    'https://www.slickremix.com/support/'
                ); ?>
				<div class="fts-text-align-center">
					<a class="feed-them-gallery-admin-slick-logo" href="https://www.slickremix.com" target="_blank"></a>
				</div>
			</div>
		</div>

        <?php echo ob_get_clean();
    } // authors_note

}//end class
