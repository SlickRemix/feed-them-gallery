<?php

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

/**
 * Uninstall Feed Them Gallery.
 *
 * Removes all settings.
 *
 * @package     Feed Them Gallery
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2020, SlickRemix
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 *
 */

/**
 * Determine whether to run multisite uninstall or standard.
 *
 * @since   1.0
 */
if ( is_multisite() )   {
    global $wpdb;

    foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id )  {
        switch_to_blog( $blog_id );
        ftg_uninstall();
        restore_current_blog();
    }

} else  {
    ftg_uninstall();
}

/**
 * The main uninstallation function.
 *
 * The uninstall will only execute if the user has explicity
 * enabled the option for data to be removed.
 *
 * @since   1.0
 */
function ftg_uninstall()    {
	$ftg_settings = get_option( 'ftg_settings' );

	if ( empty( $ftg_settings ) || empty( $ftg_settings['remove_on_uninstall'] ) )	{
		return;
	}

	$ftg_all_options = array(
		'ftg_version',
		'ftg_settings'
	);

	foreach( $ftg_all_options as $ftg_all_option )	{
		delete_option( $ftg_all_option );
	}
} // ftg_uninstall
