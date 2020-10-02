<?php
/**
 * Upgrade Class
 *
 * @package     FTG
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2020, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.4
 */

namespace feed_them_gallery;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Upgrades Class
 *
 * @class    FTG_Upgrades
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

/**
 * Class Settings_Page
 */
class FTG_Upgrades {
    /**
	 * Load Function
	 *
	 * Load up all our actions and filters.
	 *
	 * @since 1.3.4
	 */
	public static function load() {
		$instance = new self();

		// Add Actions and Filters.
		$instance->add_actions_filters();
	}

	/**
	 * Add Action Filters
	 *
	 * Add Settings to our menu.
	 *
	 * @since 1.3.4
	 */
	public function add_actions_filters() {
        // Process upgrades sent via POST/GET
        add_action( 'admin_init', array( $this, 'process_upgrade_actions' ) );

        // Automatic upgrades
        add_action( 'admin_init', array( $this, 'do_automatic_upgrades' ) );
	}

    /**
     * Processes all FTG upgrade actions sent via POST and GET by looking for the 'ftg-upgrade-action'
     * request and running do_action() to call the function
     *
     * @since   1.3.4
     * @return  void
     */
    public function process_upgrade_actions() {
        if ( isset( $_POST['ftg-upgrade-action'] ) ) {
            do_action( 'ftg-upgrade-' . $_POST['ftg-upgrade-action'], $_POST );
        }

        if ( isset( $_GET['ftg-upgrade-action'] ) ) {
            do_action( 'ftg-upgrade-' . $_GET['ftg-upgrade-action'], $_GET );
        }

    } // process_upgrade_actions

    /**
     * Perform automatic database upgrades when necessary
     *
     * @since	1.3.4
     * @return	void
    */
    public function do_automatic_upgrades() {
        $did_upgrade = false;
        $ftg_version = preg_replace( '/[^0-9.].*/', '', get_option( 'ftg_version', '1.0' ) );

        if ( version_compare( $ftg_version, '1.3.4', '<' ) ) {
            $this->v134_upgrades();
        }

        if ( version_compare( $ftg_version, FTG_CURRENT_VERSION, '<' ) )	{
            // Let us know that an upgrade has happened
            $did_upgrade = true;
        }

        if ( $did_upgrade )	{
            update_option( 'ftg_version_upgraded_from', get_option( 'ftg_version' ) );
            update_option( 'ftg_version', preg_replace( '/[^0-9.].*/', '', FTG_CURRENT_VERSION ) );
        }
    } // do_automatic_upgrades

    /**
     * Upgrade routine to migrate settings to new format.
     *
     * @since	1.3.4
     * @return	void
     */
    public function v134_upgrades()	{
        $ftg_options = array();

		/**
		 * Remove the filters that alter the values returned for old options
		 * so we can retrieve their values and migrate them.
		 */
		$old_options = array(
			'ft-gallery-use-attachment-naming',
            'ft_gallery_attch_name_gallery_name',
            'ft_gallery_attch_name_post_id',
            'ft_gallery_attch_name_date',
            'ft_gallery_attch_name_file_name',
            'ft_gallery_attch_name_attch_id',
            'ft_gallery_attch_title_gallery_name',
            'ft_gallery_attch_title_post_id',
            'ft_gallery_attch_title_date',
            'ft_gallery_attch_title_file_name',
            'ft_gallery_attch_title_attch_id',
			'ft_gallery_format_attachment_titles_options',
			'ft-gallery-timezone',
			'ft-gallery-date-and-time-format',
			'ft_gallery_text_color',
            'ft_gallery_text_size',
            'ft_gallery_description_color',
            'ft_gallery_description_size',
            'ft_gallery_link_color',
            'ft_gallery_link_color_hover',
            'ft_gallery_post_time',
            'ft-gallery-options-settings-custom-css-second',
            'ft-gallery-settings-admin-textarea-css',
			'ft_gallery_fix_magnific',
            'ft_gallery_duplicate_post_show',
            'ft-gallery-admin-bar-menu',
            'ft-gallery-powered-text-options-settings',
			'ft_gallery_language_second',
			'ft_gallery_language_seconds',
			'ft_gallery_language_minute',
			'ft_gallery_language_minutes',
			'ft_gallery_language_hour',
			'ft_gallery_language_hours',
			'ft_gallery_language_day',
			'ft_gallery_language_days',
			'ft_gallery_language_week',
			'ft_gallery_language_weeks',
			'ft_gallery_language_month',
			'ft_gallery_language_months',
			'ft_gallery_language_year',
			'ft_gallery_language_years',
			'ft_gallery_language_ago'
		);

		foreach( $old_options as $option )	{
			remove_filter( "pre_option_{$option}", array( 'FTG_Backwards_Compat', 'filter_option_values' ), 10, 3 );
		}

        // Attachment naming
        $current                              = get_option( 'ft-gallery-use-attachment-naming' );
        $ftg_options['use_attachment_naming'] = $current;
        delete_option( 'ft-gallery-use-attachment-naming' );

        /**
         * Loop through file name options and migrate.
         */
        $file_naming_options = array(
            // These are using the file_naming option array
            'ft_gallery_attch_name_gallery_name'          => 'attch_name_gallery_name',
            'ft_gallery_attch_name_post_id'               => 'attch_name_post_id',
            'ft_gallery_attch_name_date'                  => 'attch_name_date',
            'ft_gallery_attch_name_file_name'             => 'attch_name_file_name',
            'ft_gallery_attch_name_attch_id'              => 'attch_name_attch_id',
            'ft_gallery_attch_title_gallery_name'         => 'attch_title_gallery_name',
            'ft_gallery_attch_title_post_id'              => 'attch_title_post_id',
            'ft_gallery_attch_title_date'                 => 'attch_title_date',
            'ft_gallery_attch_title_file_name'            => 'attch_title_file_name',
            'ft_gallery_attch_title_attch_id'             => 'attch_title_attch_id'
        );

        foreach( $file_naming_options as $old_option => $new_option )    {
            $ftg_options['file_naming'][ $new_option ] = get_option( $old_option, 0 );
            delete_option( $old_option );
        }

        /**
         * Loop through title options and migrate.
         *
         * We move out the Misc. options to new settings here
         */
        $title_options = get_option( 'ft_gallery_format_attachment_titles_options' );

        // Deal with Misc options
        $misc_options = array(
            'ft_gallery_fat_alt'         => 'fat_alt',
            'ft_gallery_fat_caption'     => 'fat_caption',
            'ft_gallery_fat_description' => 'fat_description'
        );

        foreach( $misc_options as $old_option => $new_option )    {
            $current                    = ! empty( $title_options[ $old_option ] ) ? $title_options[ $old_option ] : 0;
            $ftg_options[ $new_option ] = $current;
        }

        // The rest of the title options
        $old_title_options = array(
            'ft_gallery_fat_hyphen'            => 'fat_hyphen',
            'ft_gallery_fat_underscore'        => 'fat_underscore',
            'ft_gallery_fat_period'            => 'fat_period',
            'ft_gallery_fat_tilde'             => 'fat_tilde',
            'ft_gallery_fat_plus'              => 'fat_plus',
            'ft_gallery_cap_options'           => 'cap_options'
        );

        foreach( $old_title_options as $old_option => $new_option )  {
            $current                    = ! empty( $title_options[ $old_option ] ) ? $title_options[ $old_option ] : 0;
            $ftg_options[ $new_option ] = $current;
        }

        delete_option( 'ft_gallery_format_attachment_titles_options' );

        /**
         * Migrate the gallery style and sizing settings.
         */
        $style_options = array(
            'ft_gallery_language_second'                    => 'language_second',
			'ft_gallery_language_seconds'                   => 'language_seconds',
			'ft_gallery_language_minute'                    => 'language_minute',
			'ft_gallery_language_minutes'                   => 'language_minutes',
			'ft_gallery_language_hour'                      => 'language_hour',
			'ft_gallery_language_hours'                     => 'language_hours',
			'ft_gallery_language_day'                       => 'language_day',
			'ft_gallery_language_days'                      => 'language_days',
			'ft_gallery_language_week'                      => 'language_week',
			'ft_gallery_language_weeks'                     => 'language_weeks',
			'ft_gallery_language_month'                     => 'language_month',
			'ft_gallery_language_months'                    => 'language_months',
			'ft_gallery_language_year'                      => 'language_year',
			'ft_gallery_language_years'                     => 'language_years',
			'ft_gallery_language_ago'                       => 'language_ago',
            'ft-gallery-custom-date'                        => 'custom_date',
            'ft-gallery-custom-time'                        => 'custom_time',
            'ft_gallery_text_color'                         => 'text_color',
            'ft_gallery_text_size'                          => 'text_size',
            'ft_gallery_description_color'                  => 'description_color',
            'ft_gallery_description_size'                   => 'description_size',
            'ft_gallery_link_color'                         => 'link_color',
            'ft_gallery_link_color_hover'                   => 'link_color_hover',
            'ft_gallery_post_time'                          => 'post_time',
            'ft-gallery-options-settings-custom-css-second' => 'use_custom_css',
            'ft-gallery-settings-admin-textarea-css'        => 'custom_css'
        );

        foreach( $style_options as $old_option => $new_option ) {
            $current                    = get_option( $old_option, '' );
            $ftg_options[ $new_option ] = $current;
            delete_option( $old_option );
        }

        /**
         * Migrate miscellaneous settings.
         */
        $misc_options = array(
            'ft_gallery_fix_magnific'                  => 'fix_magnific',
            'ft_gallery_duplicate_post_show'           => 'duplicate_post_show',
            'ft-gallery-admin-bar-menu'                => 'show_admin_bar',
            'ft-gallery-powered-text-options-settings' => 'show_powered_by'
        );

        foreach( $misc_options as $old_option => $new_option )  {
            $current                    = get_option( $old_option, '' );

            // We're switching a true/false value here
            if ( 'ft-gallery-admin-bar-menu' == $old_option )   {
                $current = 'show-admin-bar-menu' == $current ? 1 : 0;
            }

            $ftg_options[ $new_option ] = $current;
            delete_option( $old_option );
        }

        /**
         * Migrate WooCommerce settings.
         */
        $woo_options = array(
            'ft_gallery_enable_right_click'        => 'woo_enable_right_click',
            'ft_gallery_attch_prod_to_gallery_cat' => 'woo_attch_prod_to_gallery_cat',
            'ft_gallery_woo_add_to_cart'           => 'woo_add_to_cart'
        );

        foreach( $woo_options as $old_option => $new_option )   {
            $current     = get_option( $old_option );
            if ( 'ft_gallery_woo_add_to_cart' != $old_option )  {
                $current = 'true' == $current ? 1 : 0;
            }
            $ftg_options[ $new_option ] = $current;
            delete_option( $old_option );
        }

        update_option( 'ftg_settings', $ftg_options );
    } // v134_upgrades

} // FTG_Upgrades
