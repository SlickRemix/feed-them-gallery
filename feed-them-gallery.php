<?php
/**
 * Feed Them Gallery Class (Main Class)
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * Plugin Name: Feed Them Gallery
 * Plugin URI: https://www.slickremix.com/
 * Description: Create Beautiful Responsive Galleries in Minutes. Choose the number of columns, loadmore button, popup and more! Sell your Galleries or individual Images with WooCommerce, watermark them, zip galleries, create Albums, create Tags for Images and Galleries, search Galleries and Images with tags, and pagination in our premium version.
 * Version: 1.4.5
 * Author: SlickRemix
 * Author URI: https://www.slickremix.com/
 * Text Domain: feed-them-gallery
 * Domain Path: /languages
 * Requires at least: WordPress 4.7.0
 * Tested up to: WordPress 6.0
 * Stable tag: 1.4.5
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 6.6.1
 *
 * @version  1.4.5
 * @package  FeedThemSocial/Core
 * @copyright   Copyright (c) 2012-2022 SlickRemix
 *
 * Need Support? https://www.slickremix.com/my-account
 */

// Doing this to ensure any js or css changes are reloaded properly. Added to enqueued css and js files throughout.
define( 'FTG_CURRENT_VERSION', '1.4.5' );

if ( ! defined( 'FTG_PLUGIN_FILE' ) )	{
	define( 'FTG_PLUGIN_FILE', __FILE__ );
}

// Require file for plugin loading.
require_once __DIR__ . '/class-load-plugin.php';

// Load the Plugin!
Feed_Them_Gallery::load_plugin();
