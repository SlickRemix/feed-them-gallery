<?php
/**
 * Feed Them Gallery Class (Main Class)
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * Plugin Name: Feed Them Gallery
 * Plugin URI: https://slickremix.com/
 * Description: Create Beautiful Responsive Galleries in Minutes. Choose the number of columns a loadmore button, popup and more!  Sell your galleries or individual images, watermark them and even zip galleries with our premium version.
 * Version: 1.1.5.1
 * Author: SlickRemix
 * Author URI: https://www.slickremix.com/
 * Text Domain: feed-them-gallery
 * Domain Path: /languages
 * Requires at least: Wordpress 4.7.0
 * Tested up to: WordPress 5.1
 * Stable tag: 1.1.5.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.1
 *
 * @version  1.1.5.1
 * @package  FeedThemSocial/Core
 * @copyright  	Copyright (c) 2012-2019 SlickRemix
 *
 * Need Support? http://www.slickremix.com/my-account
 */
// Makes sure any js or css changes are reloaded properly. Added to enqued css and js files throughout
define('FTG_CURRENT_VERSION', '1.1.5.1');

final class Feed_Them_Gallery {

    /**
     * Main Instance of Display Posts Feed
     * @var
     */
    private static $instance;

    /**
     * Create Instance of Feed Them Gallery
     *
     * @since 1.0.0
     */
    public static function instance() {
        if (!isset(self::$instance) && !(self::$instance instanceof Feed_Them_Gallery)) {
            self::$instance = new Feed_Them_Gallery;

            if (!function_exists('is_plugin_active'))
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');

            // Third check the php version is not less than 5.2.9
            // Make sure php version is greater than 5.3
            if (function_exists('phpversion'))
                $phpversion = phpversion();
            $phpcheck = '5.2.9';
            if ($phpversion > $phpcheck) {
                // Add actions
                add_action('init', array(self::$instance, 'ft_gallery_action_init'));
            } // end if php version check
            else {
                // if the php version is not at least 5.3 do action
                deactivate_plugins('feed-them-gallery/feed-them-gallery.php');
                if ($phpversion < $phpcheck) {
                    add_action('admin_notices', array(self::$instance, 'ft_gallery_required_php_check1'));

                }
            } // end ftg_required_php_check

            register_activation_hook(__FILE__, array(self::$instance, 'ftg_activate'));
            add_action('admin_notices', array(self::$instance, 'ft_gallery_display_install_notice'));
            add_action('admin_notices', array(self::$instance, 'ft_gallery_display_update_notice'));
            add_action('upgrader_process_complete', array(self::$instance, 'ft_gallery_upgrade_completed', 10, 2));

            // Include our own Settings link to plugin activation and update page.
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(self::$instance, 'ft_gallery_free_plugin_actions'), 10, 4);

            // Include Leave feedback, Get support and Plugin info links to plugin activation and update page.
            add_filter('plugin_row_meta', array(self::$instance, 'ft_gallery_leave_feedback_link'), 10, 2);

            if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php')) {
                /* AJAX add to cart variable  */
                add_action('wp_ajax_woocommerce_add_to_cart_variable_rc', array(self::$instance, 'woocommerce_add_to_cart_variable_rc_callback_ftg'));
                add_action('wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', array(self::$instance, 'woocommerce_add_to_cart_variable_rc_callback_ftg'));
            }
            //Setup Constants for FT Gallery
            self::$instance->setup_constants();
            //add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

            //Include the files
            self::$instance->includes();

            //Admin
            self::$instance->system_info = new feed_them_gallery\System_Info();
            self::$instance->settings_page = new feed_them_gallery\Settings_Page();

            //Media Taxonomies
            // self::$instance->media_taxonomies = new feed_them_gallery\Media_Taxonomies();

            //Setup Plugin functions
            self::$instance->setup_functions = new feed_them_gallery\Setup_Functions();

            //Core
            self::$instance->core_functions = new feed_them_gallery\Core_Functions();

            self::$instance->display_list = new feed_them_gallery\Display_Gallery();

            //Galleries (Custom Post Type)
            self::$instance->gallery = new feed_them_gallery\Gallery();

            //Albums
            self::$instance->albums = new feed_them_gallery\Albums();

            if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                //Gallery to Woocommerce
                self::$instance->gallery_to_woo = new feed_them_gallery\Gallery_to_Woocommerce();

                //Zip Gallery
                self::$instance->zip_gallery = new feed_them_gallery\Zip_Gallery();
            }

            //Shortcode Button for Admin page, posts and cpt's
            self::$instance->shortcode_button = new feed_them_gallery\Shortcode_Button();

            //Shortcodes
            self::$instance->shortcodes = new feed_them_gallery\Shortcodes();

            //Updater Init
            self::$instance->plugin_license_page = new feed_them_gallery\updater_init();
        }

        return self::$instance;
    }

    function woocommerce_add_to_cart_variable_rc_callback_ftg() {
        ob_start();

        $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
        $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
        $variation_id = $_POST['variation_id'];
        $variation  = $_POST['variation'];
        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

        if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
                wc_add_to_cart_message( $product_id );
            }
            // Return fragments
            WC_AJAX::get_refreshed_fragments();
        }
        elseif ( WC()->cart->add_to_cart( $product_id, $quantity) && $variation == '' ) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
                wc_add_to_cart_message( $product_id );
            }
            // Return fragments
            WC_AJAX::get_refreshed_fragments();
        }
        else {
            echo 'Not on our watch';
            // If there was an error adding to the cart, redirect to the product page to show any errors
            $data = array(
                'error' => true,
                'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
            );
            echo json_encode( $data );
        }
        die();
    }

    /**
     * This function runs when WordPress completes its upgrade process
     * It iterates through each plugin updated to see if ours is included
     *
     * @param $upgrader_object Array
     * @param $options Array
     * @since 1.0.0
     */
    function ft_gallery_upgrade_completed($upgrader_object, $options) {
        // The path to our plugin's main file
        $our_plugin = plugin_basename(__FILE__);
        // If an update has taken place and the updated type is plugins and the plugins element exists
        if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
            // Iterate through the plugins being updated and check if ours is there
            foreach ($options['plugins'] as $plugin) {
                if ($plugin == $our_plugin) {
                    // Set a transient to record that our plugin has just been updated
                    set_transient('ftgallery_updated', 1);
                }
            }
        }
    }

    /**
     * Show a notice to anyone who has just updated this plugin
     * This notice shouldn't display to anyone who has just installed the plugin for the first time
     * @since 1.0.0
     */
    function ft_gallery_display_update_notice() {
        // Check the transient to see if we've just updated the plugin
        if (get_transient('ftgallery_updated')) {
            echo sprintf(__('%1$sThanks for updating Feed Them Social. We have deleted the cache in our plugin so you can view any changes we have made.%2$s', 'feed-them-gallery'),
                '<div class="notice notice-success updated is-dismissible"><p>',
                '</p></div>'
            );
            delete_transient('ftgallery_updated');
        }
    }

    /**
     * Show a notice to anyone who has just installed the plugin for the first time
     * This notice shouldn't display to anyone who has just updated this plugin
     * @since 1.0.0
     */
    function ft_gallery_display_install_notice() {
        // Check the transient to see if we've just activated the plugin
        if (get_transient('ftgallery_activated')) {

            echo sprintf(__('%1$sThanks for installing Feed Them Gallery. To get started please view our %2$sSettings%3$s page.%4$s', 'feed-them-gallery'),
                '<div class="notice notice-success updated is-dismissible"><p>',
                '<a href="'.esc_url('edit.php?post_type=ft_gallery&page=ft-gallery-settings-page').'">',
                '</a>',
                '</p></div>'
            );
            // Delete the transient so we don't keep displaying the activation message
            delete_transient('ftgallery_activated');
        }
    }

    /**
     * Run this on activation
     * Set a transient so that we know we've just activated the plugin
     *
     * @since 1.0.0
     */
    function ftg_activate() {
        set_transient('ftgallery_activated', 1);
    }


    /**
     * Setup Constants
     *
     * Setup plugin constants for plugin
     *
     * @since 1.0.0
     */
    private function setup_constants() {
        // Makes sure the plugin is defined before trying to use it
        if (!function_exists('is_plugin_active'))
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');

        $plugin_data = get_plugin_data(__FILE__);
        $plugin_version = $plugin_data['Version'];
        // Plugin version
        if (!defined('FEED_THEM_GALLERY_VERSION')) {
            define('FEED_THEM_GALLERY_VERSION', $plugin_version);
        }
        // Plugin Folder Path
        if (!defined('FEED_THEM_GALLERY_PLUGIN_PATH')) {
            define('FEED_THEM_GALLERY_PLUGIN_PATH', plugins_url());
        }
        // Plugin Directoy Path
        if (!defined('FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR')) {
            define('FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR', plugin_dir_path(__FILE__));
        }

        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {

            // Plugin Directoy Path
            if (!defined('FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR')) {
                define('FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR', WP_PLUGIN_DIR . '/feed-them-gallery-premium/feed-them-gallery-premium.php');
            }

        }
    }

    /**
     * Includes Files
     *
     * Include files needed for Feed Them Gallery
     *
     * @since 1.0.0
     */
    private function includes() {

        //Admin Pages
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'admin/system-info.php');
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'admin/settings-page.php');

        //Tags/Taxonomies for images
        // include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/taxonomies/media-taxonomies.php');

        //Setup Functions Class
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/setup-functions-class.php');

        //Core Functions Class
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/core-functions-class.php');

        //Gallery Options
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/galleries/gallery-options.php');

        //Galleries (Custom Post Type)
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/galleries/gallery-class.php');

        //Display Gallery
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/display-gallery/display-gallery-class.php');

        //Album Options
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/albums/album-options.php');

        //Albums
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/albums/albums-class.php');

        // Create Image
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/galleries/create-image.php');

        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
            //Zip Gallery
            include(FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/galleries/download.php');
            include(FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/galleries/zip-gallery-class.php');

            //Gallery to Woocommerce
            include(FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/woocommerce/gallery_to_woo.php');

            //Watermark
            include(FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/watermark/ajax.php');
        }

        //Shortcode Button
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/shortcode-button/shortcode-button.php');


        //Include Shortcodes
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . '/shortcodes.php');

        //Updater Classes
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'updater/updater-license-page.php');
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'updater/updater-check-class.php');
        include(FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'updater/updater-check-init.php');
    }

    /**
     * FT Gallery Action Init
     *
     * Loads language files
     *
     * @since 1.0.0
     */
    function ft_gallery_action_init() {
        // Localization
        load_plugin_textdomain('feed-them-gallery', false, basename(dirname(__FILE__)) . '/languages');
    }

    /**
     * FT Gallery Required php Check
     *
     * Are they running proper PHP version
     *
     * @since 1.0.0
     */
    function ft_gallery_required_php_check1() {
        echo sprintf(__('%1$sWarning:%2$s Your php version is %3$s. You need to be running at least 5.3 or greater to use this plugin. Please upgrade the php by contacting your host provider. Some host providers will allow you to change this yourself in the hosting control panel too.%4$sIf you are hosting with BlueHost or Godaddy and the php version above is saying you are running 5.2.17 but you are really running something higher please %5$sclick here for the fix%6$s. If you cannot get it to work using the method described in the link please contact your host provider and explain the problem so they can fix it.%7$s', 'feed-them-gallery'),
            '<div class="error"><p><strong>',
            '</strong>',
            phpversion(),
            '<br/><br/>',
            '<a href="'.esc_url('https://wordpress.org/support/topic/php-version-difference-after-changing-it-at-bluehost-php-config?replies=4').'" target="_blank">',
            '</a>',
            '</p></div>'
        );
    }

    /**
     * FT Gallery Plugin Actions
     *
     * Loads links in the Plugins page in Wordpress Dashboard
     *
     * @param $actions
     * @param $plugin_file
     * @param $plugin_data
     * @param $context
     * @return mixed
     * @since 1.0.0
     */
    function ft_gallery_free_plugin_actions($actions, $plugin_file, $plugin_data, $context) {
        array_unshift(
            $actions,
            sprintf(__('%1$sSettings%2$s | %3$sSupport%4$s', 'feed-them-gallery'),
                '<a href="'.esc_url('edit.php?post_type=ft_gallery&page=ft-gallery-settings-page').'">',
                '</a>',
                '<a href="'.esc_url('https://www.slickremix.com/support/').'">',
                '</a>'
            )
        );
        return $actions;
    }

    /**
     * FT Gallery Leave Feedback Link
     *
     * Link to add feedback for plugin
     *
     * @param $links
     * @param $file
     * @return mixed
     * @since 1.0.0
     */
    function ft_gallery_leave_feedback_link($links, $file) {
        if ($file === plugin_basename(__FILE__)) {
            $links['feedback'] = sprintf(
                __('%1$sRate Plugin%2$s', 'feed-them-social'),
                '<a href="'.esc_url('https://wordpress.org/support/plugin/feed-them-gallery/reviews/').'" target="_blank">',
                '</a>'
            );

            // $links['support'] = '<a href="http://www.slickremix.com/support-forum/forum/feed-them-gallery-2/" target="_blank">' . __('Get support', 'feed-them-premium') . '</a>';
            //  $links['plugininfo']  = '<a href="plugin-install.php?tab=plugin-information&plugin=feed-them-premium&section=changelog&TB_iframe=true&width=640&height=423" class="thickbox">' . __( 'Plugin info', 'gd_quicksetup' ) . '</a>';
        }
        return $links;
    }

}

/**
 * FT Gallery System Version
 *
 * Returns current plugin version (Must be outside the final class to work)
 *
 * @return mixed
 * @since 1.0.0
 */
function ft_gallery_check_version() {
    $plugin_data = get_plugin_data(__FILE__);
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}

/**
 * FT Gallery Plugin Activation
 *
 * Loads options upon FT Gallery Activation
 *
 * @since 1.0.0
 */
function ft_gallery_plugin_activation() {
    // we add an db option to check then delete the db option after activation and the cache has emptied.
    // the delete_option is on the feed-them-functions.php file at the bottom of the function ftg_clear_cache_script
    add_option('Feed_Them_Gallery_Activated_Plugin', 'feed-them-gallery');

}

//FT Gallery Activation Function
register_activation_hook(__FILE__, 'ft_gallery_plugin_activation');

/**
 * FT Gallery Load Plugin
 *
 * Load plugin options on activation check
 *
 * @since 1.0.0
 */
function feed_them_gallery_load_plugin() {

    if (is_admin() && get_option('Feed_Them_Gallery_Activated_Plugin') == 'feed-them-gallery') {

        //Options List
        $activation_options = array(
            'ft-gallery-date-and-time-format' => 'one-day-ago',
            'ft-gallery-timezone' => 'America/New_York',
        );

        foreach ($activation_options as $option_key => $option_value) {
            // We don't use update_option because we only want this to run for options that have not already been set by the user
            add_option($option_key, $option_value);
        }
    }
}

add_action('admin_init', 'feed_them_gallery_load_plugin');


/**
 * FTG Review Check
 *
 * Checks $_GET to see if the nag variable is set and what it's value is
 *
 * @param $get
 * @param $nag
 * @param $option
 * @param $transient
 * @return mixed
 * @since 1.0.8
 */
function ftg_check_nag_get( $get, $nag, $option, $transient ) {
    if ( isset( $_GET[$nag] ) && $get[$nag] == 1 ) {
        update_option( $option, 'dismissed' );
    } elseif ( isset( $_GET[$nag] ) && $get[$nag] == 'later' ) {
        $time = 2 * WEEK_IN_SECONDS;
        set_transient( $transient, 'ftg-review-waiting', $time );
        update_option( $option, 'pending' );
    }
}

/**
 * FTG Set Review Transient
 *
 * Set a transient if the notice has not been dismissed or has not been set yet
 *
 * @param $transient
 * @param $option
 * @return mixed
 * @since 1.0.8
 */
function ftg_maybe_set_transient( $transient, $option ) {
    $ftg_rating_notice_waiting = get_transient( $transient );
    $notice_status = get_option( $option, false );

    if ( ! $ftg_rating_notice_waiting && !( $notice_status === 'dismissed' || $notice_status === 'pending' ) ) {
        $time = 2 * WEEK_IN_SECONDS;
        set_transient( $transient, 'ftg-review-waiting', $time );
        update_option( $option, 'pending' );
    }
}

/**
 * FTG Ratings Notice
 *
 * Generates the html for the admin notice
 *
 * @return mixed
 * @since 1.0.8
 */
function ftg_rating_notice_html() {

    //Only show to admins
    if ( current_user_can( 'manage_options' ) ){

        global $current_user;
        $user_id = $current_user->ID;

        /* Has the user already clicked to ignore the message? */
        if ( ! get_user_meta( $user_id, 'ftg_slick_ignore_rating_notice') ) {
            $output =  '<div class="ftg_notice ftg_review_notice">';
            $output .=  "<img src='". plugins_url( 'feed-them-gallery/admin/css/ft-gallery-logo.png' ) ."' alt='Feed Them Gallery'>";
            $output .=  "<div class='ftg-notice-text'>";
            $output .=  '<p>'. __('It\'s great to see that you\'ve been using our Feed Them Gallery plugin for a while now. Hopefully you\'re happy with it!  If so, would you consider leaving a positive review? It really helps support the plugin and helps others discover it too!' , 'feed-them-social').'</p>';
            $output .=  '<p class="ftg-links">';
            $output .=  '<a class="ftg_notice_dismiss" href="https://wordpress.org/support/plugin/feed-them-gallery/reviews/#new-post" target="_blank">'. __('Sure, I\'de love to' , 'feed-them-social').'</a>';
            $output .=  '<a class="ftg_notice_dismiss" href="' .esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', '1' ) ). '">'. __('I\'ve already given a review' , 'feed-them-social').'</a>';
            $output .=  '<a class="ftg_notice_dismiss" href="'.esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', 'later' ) ).'">'. __('Ask me later' , 'feed-them-social').'</a>';
            $output .=  '<a class="ftg_notice_dismiss" href="https://wordpress.org/support/plugin/feed-them-gallery/#new-post" target="_blank">'. __('Not working, I need support' , 'feed-them-social').'</a>';
            $output .=  '<a class="ftg_notice_dismiss" href="'. esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', '1' ) ).'">'. __('No thanks' , 'feed-them-social').'</a>';
            $output .=  '</p>';
            $output .=  '</div>';
            $output .=  '</div>';
            echo $output;
        }
    }
}

// Variables to define specific terms
$transient = 'ftg_slick_rating_notice_waiting';
$option = 'ftg_slick_rating_notice';
$nag = 'ftg_slick_ignore_rating_notice_nag';

ftg_check_nag_get( $_GET, $nag, $option, $transient );
ftg_maybe_set_transient( $transient, $option );
$notice_status = get_option( $option, false );

// only display the notice if the time offset has passed and the user hasn't already dismissed it
if ( get_transient( $transient ) !== 'ftg-review-waiting' && $notice_status !== 'dismissed' ) {
    add_action( 'admin_notices', 'ftg_rating_notice_html' );
}
//print get_transient( $transient );
//print  ' & ';
//print  $notice_status;
/* END ftg Ratings Notice */

/**
 * Feed Them Gallery
 *
 * Start it up!
 *
 * @return feed_them_gallery
 * @since 1.0.0
 */
function feed_them_gallery() {
    return Feed_Them_Gallery::instance();
}

// Get FTG Running
feed_them_gallery();
?>