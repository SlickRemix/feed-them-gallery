<?php
class Feed_Them_Gallery {

	/**
	 * Create Instance of Feed Them Gallery
	 *
	 * @since 1.0.0
	 */


	public static function load_plugin() {

			$plugin_loaded = new self();

			$plugin_loaded->pre_plugin_checks();

			$gallery_main_post_type = 'ft_gallery';

			$albums_main_post_type = 'ft_gallery_albums';

			// Add Actions and Filters.
			$plugin_loaded->add_actions_filters();

			// Setup Constants for FT Gallery.
			self::setup_constants();

			// Include the files.
			self::includes();

			// Gallery Options.
			$gallery_options = feed_them_gallery\Gallery_Options::get_all_options();

			// Settings Page.
			feed_them_gallery\Settings_Page::load();

			// System Info.
			feed_them_gallery\System_Info::load();

			// Setup Plugin functions.
			feed_them_gallery\Setup_Functions::load();

			// Core.
			feed_them_gallery\Core_Functions::load();

			// Display Gallery.
			feed_them_gallery\Display_Gallery::load();

			// Galleries.
			feed_them_gallery\Gallery::load( $gallery_options, $gallery_main_post_type );

		// Load in Premium Gallery glasses if premium is loaded.
		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {

            if ( FTGP_CURRENT_VERSION > '1.0.5' ) {
                // Template Settings Options.
                $template_settings_options = feed_them_gallery\Template_Settings_Options::get_all_options();

                // Template Settings Page.
                feed_them_gallery\Template_Settings_Page::load( $template_settings_options, $gallery_main_post_type );

                // Media Taxonomies.
                feed_them_gallery\Media_Taxonomies::load();

                // Album Options.
                $gallery_options = feed_them_gallery\Album_Options::get_all_options();

                // Albums.
                feed_them_gallery\Albums::load( $gallery_options, $albums_main_post_type );

            }
			// Gallery to Woocommerce.
			new feed_them_gallery\Gallery_to_Woocommerce();

			// Zip Gallery.
			new feed_them_gallery\Zip_Gallery();
		}
			// Shortcode Button for Admin page, posts and cpt's.
			feed_them_gallery\Shortcode_Button::load();

			// Shortcodes.
			new feed_them_gallery\Shortcodes();

			// Updater Init.
			new feed_them_gallery\updater_init();

		// Variables to define specific terms!
		$transient = 'ftg_slick_rating_notice_waiting';
		$option    = 'ftg_slick_rating_notice';
		$nag       = 'ftg_slick_ignore_rating_notice_nag';

		$plugin_loaded->ftg_check_nag_get( $_GET, $nag, $option, $transient );

		$plugin_loaded->ftg_maybe_set_transient( $transient, $option );

		$plugin_loaded->set_review_status( $option, $transient );
	}

    /**
     * FT Gallery Premium Plugin required Check
     *
     * Is the Free Plugin installed and active?
     *
     * @since 1.1.6
     */
    public function ft_gallery_premium_version_required() {
        echo sprintf(
            esc_html('%1$sWarning: %2$sFeed Them Gallery Premium%3$s has been deactivated because it needs to be running Premium version 1.0.6 to work with the latest version of Feed Them Gallery Free to function properly. Please update the Premium version 1.0.6. If you are not getting an update notice you can always manually download the latest zip from the %4$sMy Account%5$s page of our website.%6$s', 'feed-them-gallery') ,
            '<div class="error"><p>',
            '<strong>',
            '</strong>',
            '<a href="' . esc_url( 'https://www.slickremix.com/my-account' ) . '" target="_blank"><strong>',
            '</strong></a>',
            '</p></div>'
        );
    }

	public function add_actions_filters() {
		register_activation_hook( __FILE__, array( $this, 'ftg_activate' ) );
			add_action( 'admin_notices', array( $this, 'ft_gallery_display_install_notice' ) );
			add_action( 'admin_notices', array( $this, 'ft_gallery_display_update_notice' ) );
			add_action( 'upgrader_process_complete', array( $this, 'ft_gallery_upgrade_completed', 10, 2 ) );

			// Include our own Settings link to plugin activation and update page.
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ft_gallery_free_plugin_actions' ), 10, 4 );

			// Include Leave feedback, Get support and Plugin info links to plugin activation and update page.
			add_filter( 'plugin_row_meta', array( $this, 'ft_gallery_leave_feedback_link' ), 10, 2 );

		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			/* AJAX add to cart variable  */
			add_action( 'wp_ajax_woocommerce_add_to_cart_variable_rc', array( $this, 'woocommerce_add_to_cart_variable_rc_callback_ftg' ) );
			add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', array( $this, 'woocommerce_add_to_cart_variable_rc_callback_ftg' ) );
		}

		// FT Gallery Activation Function.
		register_activation_hook( __FILE__, array( $this, 'ft_gallery_plugin_activation' ) );

		// Load plugin options.
		add_action( 'admin_init', array( $this, 'set_plugin_timezone' ) );
	}

	/**
	 * Create Instance of Feed Them Gallery
	 *
	 * @since 1.0.0
	 */
	public function pre_plugin_checks() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// Third check the php version is not less than 5.2.9
		// Make sure php version is greater than 5.3!
		if ( function_exists( 'phpversion' ) ) {
			$phpversion = PHP_VERSION;
		}
		$phpcheck = '5.2.9';
		if ( $phpversion > $phpcheck ) {
			// Add actions.
			add_action( 'init', array( $this, 'ft_gallery_action_init' ) );
		} // end if php version check.
		else {
			// if the php version is not at least 5.3 do action.
			deactivate_plugins( 'feed-them-gallery/feed-them-gallery.php' );
			if ( $phpversion < $phpcheck ) {
				add_action( 'admin_notices', array( $this, 'ft_gallery_required_php_check1' ) );

			}
		}

	}

	public function woocommerce_add_to_cart_variable_rc_callback_ftg() {
		ob_start();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
		$variation_id      = $_POST['variation_id'];
		$variation         = $_POST['variation'];
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) ) {
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( $product_id );
			}
			// Return fragments.
			WC_AJAX::get_refreshed_fragments();
		} elseif ( WC()->cart->add_to_cart( $product_id, $quantity ) && '' === $variation ) {
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( $product_id );
			}
			// Return fragments.
			WC_AJAX::get_refreshed_fragments();
		} else {
			echo 'Not on our watch';
			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);
			echo json_encode( $data );
		}
		die();
	}

	/**
	 * This function runs when WordPress completes its upgrade process
	 *
	 * It iterates through each plugin updated to see if ours is included
	 *
	 * @param $upgrader_object Array
	 * @param $options Array
	 * @since 1.0.0
	 */
	public function ft_gallery_upgrade_completed( $upgrader_object, $options ) {
		// The path to our plugin's main file.
		$our_plugin = plugin_basename( __FILE__ );
		// If an update has taken place and the updated type is plugins and the plugins element exists.
		if ( 'update' === $options['action'] && 'plugin' === $options['type'] && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there.
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin == $our_plugin ) {
					// Set a transient to record that our plugin has just been updated.
					set_transient( 'ftgallery_updated', 1 );
				}
			}
		}
	}

	/**
	 * Show a notice to anyone who has just updated this plugin
	 *
	 * This notice shouldn't display to anyone who has just installed the plugin for the first time
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_display_update_notice() {
		// Check the transient to see if we've just updated the plugin.
		if ( get_transient( 'ftgallery_updated' ) ) {
			echo sprintf(
				__( '%1$sThanks for updating Feed Them Social. We have deleted the cache in our plugin so you can view any changes we have made.%2$s', 'feed-them-gallery' ),
				'<div class="notice notice-success updated is-dismissible"><p>',
				'</p></div>'
			);
			delete_transient( 'ftgallery_updated' );
		}
	}

	/**
	 * Show a notice to anyone who has just installed the plugin for the first time
	 *
	 * This notice shouldn't display to anyone who has just updated this plugin
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_display_install_notice() {
		// Check the transient to see if we've just activated the plugin.
		if ( get_transient( 'ftgallery_activated' ) ) {

			echo sprintf(
				__( '%1$sThanks for installing Feed Them Gallery. To get started please view our %2$sSettings%3$s page.%4$s', 'feed-them-gallery' ),
				'<div class="notice notice-success updated is-dismissible"><p>',
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ) . '">',
				'</a>',
				'</p></div>'
			);
			// Delete the transient so we don't keep displaying the activation message.
			delete_transient( 'ftgallery_activated' );
		}
	}

	/**
	 * Run this on activation
	 *
	 * Set a transient so that we know we've just activated the plugin
	 *
	 * @since 1.0.0
	 */
	public function ftg_activate() {
		set_transient( 'ftgallery_activated', 1 );
	}


	/**
	 * Setup Constants
	 *
	 * Setup plugin constants for plugin
	 *
	 * @since 1.0.0
	 */
	private static function setup_constants() {
		// Makes sure the plugin is defined before trying to use it.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugin_data    = get_plugin_data( __FILE__ );
		$plugin_version = $plugin_data['Version'];
		// Plugin version.
		if ( ! defined( 'FEED_THEM_GALLERY_VERSION' ) ) {
			define( 'FEED_THEM_GALLERY_VERSION', $plugin_version );
		}
		// Plugin Folder Path.
		if ( ! defined( 'FEED_THEM_GALLERY_PLUGIN_PATH' ) ) {
			define( 'FEED_THEM_GALLERY_PLUGIN_PATH', plugins_url() );
		}
		// Plugin Directoy Path.
		if ( ! defined( 'FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR' ) ) {
			define( 'FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Directoy Path.
		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) && ! defined( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR' ) ) {
			define( 'FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR', WP_PLUGIN_DIR . '/feed-them-gallery-premium/feed-them-gallery-premium.php' );
		}
	}

	/**
	 * Includes Files
	 *
	 * Include files needed for Feed Them Gallery
	 *
	 * @since 1.0.0
	 */
	private static function includes() {

		// Admin Pages.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'admin/system-info.php';
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'metabox-settings/metabox-settings-class.php';
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'admin/settings-page.php';


		// Setup Functions Class.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/setup-functions-class.php';

		// Core Functions Class.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/core-functions-class.php';

		// Gallery Options.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/galleries/gallery-options.php';

		// Galleries (Custom Post Type).
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/galleries/gallery-class.php';

		// Display Gallery.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/display-gallery/display-gallery-class.php';


		// Create Image.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/galleries/create-image.php';

		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {

            if ( FTGP_CURRENT_VERSION > '1.0.5' ) {
                // Tags/Taxonomies for images.
                include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/taxonomies/media-taxonomies.php';
                // Album Options.
                include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/albums/album-options.php';

                // Albums.
                include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/albums/albums-class.php';

                // Template Settings Options.
                include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'admin/template-settings-options.php';

                // Template Settings Page.
                include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'admin/template-settings-page-class.php';
            }

			// Zip Gallery.
			include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/galleries/download.php';
			include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/galleries/zip-gallery-class.php';

			// Gallery to Woocommerce.
			include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/woocommerce/gallery_to_woo.php';

			// Watermark.
			include FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/watermark/ajax.php';

		}

		// Shortcode Button.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'includes/shortcode-button/shortcode-button.php';

		// Include Shortcodes.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . '/shortcodes.php';

		// Updater Classes.
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'updater/updater-license-page.php';
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'updater/updater-check-class.php';
		include FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'updater/updater-check-init.php';
	}

	/**
	 * FT Gallery Action Init
	 *
	 * Loads language files
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_action_init() {
		// Localization.
		load_plugin_textdomain( 'feed-them-gallery', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * FT Gallery Required php Check
	 *
	 * Are they running proper PHP version
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_required_php_check1() {
		echo sprintf(
			__( '%1$sWarning:%2$s Your php version is %3$s. You need to be running at least 5.3 or greater to use this plugin. Please upgrade the php by contacting your host provider. Some host providers will allow you to change this yourself in the hosting control panel too.%4$sIf you are hosting with BlueHost or Godaddy and the php version above is saying you are running 5.2.17 but you are really running something higher please %5$sclick here for the fix%6$s. If you cannot get it to work using the method described in the link please contact your host provider and explain the problem so they can fix it.%7$s', 'feed-them-gallery' ),
			'<div class="error"><p><strong>',
			'</strong>',
			PHP_VERSION,
			'<br/><br/>',
			'<a href="' . esc_url( 'https://wordpress.org/support/topic/php-version-difference-after-changing-it-at-bluehost-php-config?replies=4' ) . '" target="_blank">',
			'</a>',
			'</p></div>'
		);
	}

	/**
	 * FT Gallery Plugin Actions
	 *
	 * Loads links in the Plugins page in WordPress Dashboard
	 *
	 * @param $actions
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_free_plugin_actions( $actions ) {
		array_unshift(
			$actions,
			sprintf(
				__( '%1$sSettings%2$s | %3$sSupport%4$s', 'feed-them-gallery' ),
				'<a href="' . esc_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ) . '">',
				'</a>',
				'<a href="' . esc_url( 'https://www.slickremix.com/support/' ) . '">',
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
	public function ft_gallery_leave_feedback_link( $links, $file ) {
		if ( $file === plugin_basename( __FILE__ ) ) {
			$links['feedback'] = sprintf(
				__( '%1$sRate Plugin%2$s', 'feed-them-social' ),
				'<a href="' . esc_url( 'https://wordpress.org/support/plugin/feed-them-gallery/reviews/' ) . '" target="_blank">',
				'</a>'
			);

			// $links['support'] = '<a href="http://www.slickremix.com/support-forum/forum/feed-them-gallery-2/" target="_blank">' . __('Get support', 'feed-them-premium') . '</a>';
			// $links['plugininfo']  = '<a href="plugin-install.php?tab=plugin-information&plugin=feed-them-premium&section=changelog&TB_iframe=true&width=640&height=423" class="thickbox">' . __( 'Plugin info', 'gd_quicksetup' ) . '</a>';
		}
		return $links;
	}

	/**
	 * FT Gallery Plugin Activation
	 *
	 * Loads options upon FT Gallery Activation
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_plugin_activation() {
		// we add an db option to check then delete the db option after activation and the cache has emptied.
		// the delete_option is on the feed-them-functions.php file at the bottom of the function ftg_clear_cache_script.
		add_option( 'Feed_Them_Gallery_Activated_Plugin', 'feed-them-gallery' );

	}

	/**
	 * Set Plugin TimeZone
	 *
	 * Load plugin options on activation check
	 *
	 * @since 1.0.0
	 */
	public function set_plugin_timezone() {

		if ( is_admin() && 'feed-them-gallery' === get_option( 'Feed_Them_Gallery_Activated_Plugin' ) ) {

			// Options List.
			$activation_options = array(
				'ft-gallery-date-and-time-format' => 'one-day-ago',
				'ft-gallery-timezone'             => 'America/New_York',
			);

			foreach ( $activation_options as $option_key => $option_value ) {
				// We don't use update_option because we only want this to run for options that have not already been set by the user.
				add_option( $option_key, $option_value );
			}
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
	public function ftg_maybe_set_transient( $transient, $option ) {
		$ftg_rating_notice_waiting = get_transient( $transient );
		$notice_status             = get_option( $option, false );

		if ( ! $ftg_rating_notice_waiting && ! ( 'dismissed' === $notice_status || 'pending' === $notice_status ) ) {
			$time = 2 * WEEK_IN_SECONDS;
			set_transient( $transient, 'ftg-review-waiting', $time );
			update_option( $option, 'pending' );
		}
	}


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
	public function ftg_check_nag_get( $get, $nag, $option, $transient ) {

		if ( isset( $_GET[ $nag ] ) ) {
			if ( 1 === $get[ $nag ] ) {
				update_option( $option, 'dismissed' );
			} elseif ( 'later' === $get[ $nag ] ) {
				$time = 2 * WEEK_IN_SECONDS;
				set_transient( $transient, 'ftg-review-waiting', $time );
				update_option( $option, 'pending' );
			}
		}

	}

	public function set_review_status( $option, $transient ) {
		$notice_status = get_option( $option, false );

		// Only display the notice if the time offset has passed and the user hasn't already dismissed it!.
		if ( 'ftg-review-waiting' !== get_transient( $transient ) && 'dismissed' !== $notice_status ) {
			add_action( 'admin_notices', 'ftg_rating_notice_html' );
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
	public function ftg_rating_notice_html() {

		// Only show to admins.
		if ( current_user_can( 'manage_options' ) ) {

			global $current_user;
			$user_id = $current_user->ID;

			/* Has the user already clicked to ignore the message? */
			if ( ! get_user_meta( $user_id, 'ftg_slick_ignore_rating_notice' ) ) {
				$output  = '<div class="ftg_notice ftg_review_notice">';
				$output .= "<img src='" . plugins_url( 'feed-them-gallery/admin/css/ft-gallery-logo.png' ) . "' alt='Feed Them Gallery'>";
				$output .= "<div class='ftg-notice-text'>";
				$output .= '<p>' . __( 'It\'s great to see that you\'ve been using our Feed Them Gallery plugin for a while now. Hopefully you\'re happy with it!  If so, would you consider leaving a positive review? It really helps support the plugin and helps others discover it too!', 'feed-them-social' ) . '</p>';
				$output .= '<p class="ftg-links">';
				$output .= '<a class="ftg_notice_dismiss" href="https://wordpress.org/support/plugin/feed-them-gallery/reviews/#new-post" target="_blank">' . __( 'Sure, I\'de love to', 'feed-them-social' ) . '</a>';
				$output .= '<a class="ftg_notice_dismiss" href="' . esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', '1' ) ) . '">' . __( 'I\'ve already given a review', 'feed-them-social' ) . '</a>';
				$output .= '<a class="ftg_notice_dismiss" href="' . esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', 'later' ) ) . '">' . __( 'Ask me later', 'feed-them-social' ) . '</a>';
				$output .= '<a class="ftg_notice_dismiss" href="https://wordpress.org/support/plugin/feed-them-gallery/#new-post" target="_blank">' . __( 'Not working, I need support', 'feed-them-social' ) . '</a>';
				$output .= '<a class="ftg_notice_dismiss" href="' . esc_url( add_query_arg( 'ftg_slick_ignore_rating_notice_nag', '1' ) ) . '">' . __( 'No thanks', 'feed-them-social' ) . '</a>';
				$output .= '</p>';
				$output .= '</div>';
				$output .= '</div>';
				echo $output;
			}
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
	public static function ft_gallery_check_version() {

		$plugin_basename = plugin_dir_path( __DIR__ ) . plugin_basename( __DIR__ ) . '/feed-them-gallery.php';
		$plugin_data     = get_plugin_data( $plugin_basename );
		return $plugin_data['Version'];
	}

}
