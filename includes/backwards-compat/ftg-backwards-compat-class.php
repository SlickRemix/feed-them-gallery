<?php
/**
 * Backwards Compatibility Class
 *
 * @package     FTG
 * @subpackage  FTG
 * @copyright   Copyright (c) 2020, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4
 */

namespace feed_them_gallery;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Backwards Compat Class
 *
 * @class    FTG_Backwards_Compat
 * @version  1.4
 * @package  FeedThemGallery
 * @category Class
 * @author   SlickRemix
 */
class FTG_Backwards_Compat {
	/**
	 * Old option names.
	 *
	 * @var	array
	 */
	public $old_options;

    /**
	 * Load Function
	 *
	 * Load up all our actions and filters.
	 *
	 * @since 1.0.0
	 */
	public static function load() {
		$instance = new self();

		$instance->old_options = array(
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
			'ft_gallery_text_color',
            'ft_gallery_text_size',
            'ft_gallery_description_color',
            'ft_gallery_description_size',
            'ft_gallery_link_color',
            'ft_gallery_link_color_hover',
            'ft_gallery_post_time',
            'ft-gallery-options-settings-custom-css-second',
            'ft-gallery-settings-admin-textarea-css',
			'ft-gallery-timezone',
			'ft-gallery-date-and-time-format',
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

		// Add Actions and Filters.
		$instance->add_actions_filters();
	} // load

	/**
	 * Add Action Filters
	 *
	 * Add Settings to our menu.
	 *
	 * @since 1.0.0
	 */
	public function add_actions_filters() {
		/**
		 * Hook into option retrieval to ensure we provide values for old options.
		 *
		 * This users with extensions who no longer have support are not impacted
		 * by the new settings API.
		 */
		add_action( 'init', array( $this, 'setup_option_filters' ) );
	} // add_actions_filters

	/**
	 * Loop through the old setting options and add filters for correct value retrieval.
	 *
	 * @since	1.4
	 */
	public function setup_option_filters()	{
		foreach( $this->old_options as $option )	{
			add_filter( "pre_option_{$option}", array( $this, 'filter_option_values' ), 10, 3 );
		}
	} // setup_option_filters

	/**
	 * Filter the values of old FTG options.
	 *
	 * @since	1.4
	 * @param	mixed	$value		The required value of the option
	 * @param	string	$option		The option name
	 * @param	mixed	$default	Default value if the option does not exist
	 * @return	mixed	The required value of the option
	 */
	public function filter_option_values( $value, $option, $default )	{
		switch( $option )	{
			case 'ft-gallery-use-attachment-naming':
				$value = ftg_get_option( 'use_attachment_naming' );
				break;
			case 'ft_gallery_attch_name_gallery_name':
			case 'ft_gallery_attch_name_post_id':
			case 'ft_gallery_attch_name_date':
			case 'ft_gallery_attch_name_file_name':
			case 'ft_gallery_attch_name_attch_id':
			case 'ft_gallery_attch_title_gallery_name':
			case 'ft_gallery_attch_title_post_id':
			case 'ft_gallery_attch_title_date':
			case 'ft_gallery_attch_title_file_name':
			case 'ft_gallery_attch_title_attch_id':
				$get_option = ftg_get_option( 'file_naming' );
				$key        = str_replace( 'ft_gallery_', '', $option );
				$value      = ! empty( $get_option[ $key ] ) ? $get_option[ $key ] : false;
				break;
			case 'ft_gallery_format_attachment_titles_options':
				$value = ftg_get_option( 'attachment_titles' );
				break;
			case 'ft_gallery_text_color':
			case 'ft_gallery_text_size':
			case 'ft_gallery_description_color':
            case 'ft_gallery_description_size':
			case 'ft_gallery_link_color':
            case 'ft_gallery_link_color_hover':
			case 'ft_gallery_post_time':
				$key   = str_replace( 'ft_gallery_', '', $option );
				$value = ftg_get_option( $key );
				break;
			case 'ft-gallery-options-settings-custom-css-second':
				$value = ftg_get_option( 'custom_css_second' );
				break;
			case 'ft-gallery-settings-admin-textarea-css':
				$value = ftg_get_option( 'custom_css' );
				break;
			case 'ft-gallery-timezone':
				$value = ftg_get_option( 'timezone' );
				break;
			case 'ft-gallery-date-and-time-format':
				$value = ftg_get_opton( 'date_time_format' );
				break;
			case 'ft_gallery_fix_magnific':
			case 'ft_gallery_duplicate_post_show':
				$key   = str_replace( 'ft_gallery_', '', $option );
				$value = ftg_get_option( $key );
				break;
			case 'ft-gallery-admin-bar-menu':
				$value = ftg_get_option( 'show_admin_bar' );
				break;
			case 'ft-gallery-powered-text-options-settings':
				$value = ftg_get_option( 'show_powered_by' );
				break;
			case 'ft_gallery_language_second':
			case 'ft_gallery_language_seconds':
			case 'ft_gallery_language_minute':
			case 'ft_gallery_language_minutes':
			case 'ft_gallery_language_hour':
			case 'ft_gallery_language_hours':
			case 'ft_gallery_language_day':
			case 'ft_gallery_language_days':
			case 'ft_gallery_language_week':
			case 'ft_gallery_language_weeks':
			case 'ft_gallery_language_month':
			case 'ft_gallery_language_months':
			case 'ft_gallery_language_year':
			case 'ft_gallery_language_years':
			case 'ft_gallery_language_ago':
				$key   = str_replace( 'ft_gallery_', '', $option );
				$value = ftg_get_option( $key );
				break;
			default:
				$value = $value;
		}

		return $value;
	} // filter_option_values
} // FTG_Backwards_Compat
