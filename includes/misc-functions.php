<?php
/**
 * Misc Functions
 *
 * @package     FTG
 * @subpackage  Functions/Miscellaneous
 * @copyright   Copyright (c) 2021, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Premium plugins data
 *
 * @since	1.3.4
 * @return	array	Array of premium plugin data
 */
function ft_gallery_premium_plugins()	{
	$premium = array(
		'feed_them_gallery_premium' => array(
			'title'        => 'Feed Them Gallery Premium',
			'plugin_url'   => 'feed-them-gallery-premium/feed-them-gallery-premium.php',
			'demo_url'     => 'https://feedthemgallery.com/',
			'purchase_url' => 'https://www.slickremix.com/downloads/feed-them-gallery/',
			'main_class'   => 'Feed_Them_Gallery_Premium'
		),
		'feed_them_gallery_clients_manager' => array(
			'title'        => 'Feed Them Gallery Clients Manager',
			'plugin_url'   => 'feed-them-gallery-premium/feed-them-gallery-clients-manager.php',
			'demo_url'     => 'https://feedthemgallery.com/',
			'purchase_url' => 'https://www.slickremix.com/downloads/feed-them-gallery/',
			'main_class'   => 'FTG_CLIENTS_MANAGER'
		)
	);

	foreach( $premium as $plugin => $data )	{
		$premium[ $plugin ]['active'] = class_exists( $data['main_class'] );
	}

	return $premium;
} // ft_gallery_premium_plugins

/**
 * Whether or not a premium plugin is active.
 *
 * @since	1.3.5
 * @param	string	$plugin		The plugin slug
 * @return	bool	True if active, otherwise false
 */
function ft_gallery_premium_plugin_is_active( $slug )	{
	$plugins = ft_gallery_premium_plugins();

	$active = false;

	if ( isset( $plugins[ $slug ], $plugins[ $slug ]['active'] ) )	{
		$active = $plugins[ $slug ]['active'];
	}

	return $active;
} // ft_gallery_premium_plugin_is_active

