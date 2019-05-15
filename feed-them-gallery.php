<?php
/**
 * Feed Them Gallery Class (Main Class)
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * Plugin Name: Feed Them Gallery
 * Plugin URI: https://www.slickremix.com/
 * Description: Create Beautiful Responsive Galleries in Minutes. Choose the number of columns a loadmore button, popup and more!  Sell your galleries or individual images, watermark them and even zip galleries with our premium version.
 * Version: 1.1.7
 * Author: SlickRemix
 * Author URI: https://www.slickremix.com/
 * Text Domain: feed-them-gallery
 * Domain Path: /languages
 * Requires at least: WordPress 4.7.0
 * Tested up to: WordPress 5.2
 * Stable tag: 1.1.7
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6.2
 *
 * @version  1.1.7
 * @package  FeedThemSocial/Core
 * @copyright   Copyright (c) 2012-2018 SlickRemix
 *
 * Need Support? http://www.slickremix.com/my-account
 */

// Doing this ensure's any js or css changes are reloaded properly. Added to enqued css and js files throughout.
define( 'FTG_CURRENT_VERSION', '1.1.7' );

// Require file for plugin loading.
require_once __DIR__ . '/class-load-plugin.php';

// Load the Plugin!
Feed_Them_Gallery::load_plugin();