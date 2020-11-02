<?php
/**
 * Misc Functions
 *
 * @package     FTG
 * @subpackage  Functions/Miscellaneous
 * @copyright   Copyright (c) 2020, SlickRemix
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
		),
		'feed_them_gallery_clients_manager' => array(
			'title'        => 'Feed Them Gallery Clients Manager',
			'plugin_url'   => 'feed-them-gallery-clients-manager/feed-them-gallery-clients-manager.php',
			'demo_url'     => 'https://feedthemgallery.com/',
			'purchase_url' => 'https://www.slickremix.com/downloads/feed-them-gallery/',
		)
	);

	return $premium;
} // ft_gallery_premium_plugins
