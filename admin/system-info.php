<?php
/**
 * System Info
 *
 * This class is for loading up the System Info Page for debugging issues
 *
 * @class    System_Info
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

namespace feed_them_gallery;

/**
 * Class System_Info
 */
class System_Info {
	/**
	 * System_Info constructor.
	 */
	public function __construct() { }

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
		$instance->add_actions_filters();
	}

	/**
	 * Add Action Filters
	 *
	 * Add System Info to our menu.
	 *
	 * @since 1.0.0
	 */
	public function add_actions_filters() {
		if ( is_admin() ) {
			// Adds setting page to Feed Them Gallery menu.
			add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

		}
	}

	/**
	 * FT Gallery Submenu Pages
	 *
	 * Admin Submenu buttons
	 *
	 * @since 1.0.0
	 */
	public function add_submenu_page() {
		// System Info.
		add_submenu_page(
			'edit.php?post_type=ft_gallery',
			__( 'System Info', 'feed-them-gallery' ),
			__( 'System Info', 'feed-them-gallery' ),
			'manage_options',
			'ft-gallery-system-info-submenu-page',
			array( $this, 'ft_gallery_system_info_page' )
		);
	}

	/**
	 * System Info Page
	 *
	 * System info page html.
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_system_info_page() {
		?>
		<div class="ft-gallery-main-template-wrapper-all">

		<div class="ft-gallery-settings-admin-wrap" id="theme-settings-wrap">
			<h2>
				<?php esc_html_e( 'System Info', 'feed-them-gallery' ); ?>
			</h2>
			<p>
				<?php esc_html_e( 'Please click the box below and copy the report. You will need to paste this information along with your question when creating a', 'feed-them-gallery' ); ?>
				<a href="https://www.slickremix.com/my-account/#tab-support" target="_blank">
					<?php esc_html_e( 'Support Ticket', 'feed-them-gallery' ); ?></a>.</p>
			<p>
				<?php esc_html_e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'feed-them-gallery' ); ?>
			</p>
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=ft-gallery-system-info-submenu-page' ) ); ?>" method="post" dir="ltr">
		<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="ft-gallery-sysinfo" title="<?php esc_html_e( 'To copy the system info, click here then press Ctrl + C (PC) or Cmd + C (Mac).', 'feed-them-gallery' ); ?>">
### Begin System Info ###
			<?php
			$theme_data = wp_get_theme();
			$theme      = $theme_data->name . ' ' . $theme_data->version;
			?>

SITE_URL: <?php echo esc_url( site_url() ) . "\n"; ?>
Feed Them Gallery Version: <?php echo esc_html( \Feed_Them_Gallery::ft_gallery_check_version() ) . "\n"; ?>

-- WordPress Configuration:

WordPress Version: <?php echo esc_html( get_bloginfo( 'version' ) ) . "\n"; ?>
Multisite: <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n"; ?>
Permalink Structure: <?php echo esc_html( get_option( 'permalink_structure' ) ) . "\n"; ?>
Active Theme: <?php echo esc_html( $theme ) . "\n"; ?>
PHP Memory Limit: <?php echo esc_html( ini_get( 'memory_limit' ) ) . "\n"; ?>
WP_DEBUG: <?php echo esc_html( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ); ?>

-- Webserver Configuration:

PHP Version: <?php
		echo PHP_VERSION . "\n";
		$my_request = stripslashes_deep( $_SERVER );
		?>
Web Server Info: <?php echo esc_html( $my_request['SERVER_SOFTWARE'] ) . "\n"; ?>

-- PHP Configuration:

Safe Mode: <?php echo esc_html( ini_get( 'safe_mode' ) ? 'Yes' : "No\n" ); ?>
Upload Max Size: <?php echo esc_html( ini_get( 'upload_max_filesize' ) . "\n" ); ?>
Post Max Size: <?php echo esc_html( ini_get( 'post_max_size' ) . "\n" ); ?>
Upload Max Filesize: <?php echo esc_html( ini_get( 'upload_max_filesize' ) . "\n" ); ?>
Time Limit: <?php echo esc_html( ini_get( 'max_execution_time' ) . "\n" ); ?>
Max Input Vars: <?php echo esc_html( ini_get( 'max_input_vars' ) . "\n" ); ?>
Allow URL File Open: <?php echo esc_html( ini_get( 'allow_url_fopen' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ); ?><?php echo "\n"; ?>
Display Erros: <?php echo esc_html( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ); ?><?php echo "\n"; ?>

 -- PHP Extensions:

FSOCKOPEN: <?php echo function_exists( 'fsockopen' ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL: <?php echo function_exists( 'curl_init' ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>

-- Active Plugins:

<?php
		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		foreach ( $plugins as $plugin_path => $plugin ) {
				// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
					continue;
			}
			echo esc_html( $plugin['Name'] . ': ' . $plugin['Version'] . "\n" );
		}
		if ( is_multisite() ) :
			?>
-- Network Active Plugins:

<?php
				$plugins        = wp_get_active_network_plugins();
				$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );

				// If the plugin isn't active, don't show it.
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}

				$plugin = get_plugin_data( $plugin_path );

				echo esc_html( $plugin['Name'] . ' :' . $plugin['Version'] . "\n" );

			}
			endif;

		if ( is_plugin_active( 'feed-them-gallery/feed-them-gallery.php' ) ) {
			$feed_them_gallery_license_key = get_option( 'feed_them_gallery_license_key' );
			?>

-- License

License Active: <?php
			echo isset( $feed_them_gallery_license_key ) && '' !== $feed_them_gallery_license_key ? 'Yes' . "\n" : 'No' . "\n";
		}
		?>

### End System Info ###</textarea>
			</form>
		</div>
		</div>

		<?php
	}
}//end class
