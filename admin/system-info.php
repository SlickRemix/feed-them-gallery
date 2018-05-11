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
    function __construct() {

        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'ft_gallery_sys_info_scripts_styles'));
        }
    }

    /**
     * FT Gallery System Info Scripts & Styles
     *
     * Load up the scrips and styles for system info page
     *
     * @since 1.0.0
     */
    public function ft_gallery_sys_info_scripts_styles() {
        if (isset($_GET['page']) && $_GET['page'] == 'ft-gallery-system-info-submenu-page') {
            //Settings Page CSS
            wp_register_style('ft_gallery_sinfo_css', plugins_url('css/admin-pages.css', __FILE__));
            wp_enqueue_style('ft_gallery_sinfo_css');
        }
    }

    /**
     * System Info Page
     *
     * system info page html
     *
     * @since 1.0.0
     */
    function ft_gallery_system_info_page() {
        ?>
        <div class="ft-gallery-main-template-wrapper-all">

        <div class="ft-gallery-settings-admin-wrap" id="theme-settings-wrap">
            <h2>
                <?php _e('System Info', 'ft-gallery'); ?>
            </h2>
            <p>
                <?php _e('Please click the box below and copy the report. You will need to paste this information along with your question when creating a', 'ft-gallery'); ?>
                <a href="https://www.slickremix.com/my-account/#tab-support" target="_blank">
                    <?php _e('Support Ticket', 'ft-gallery'); ?></a>.</p>
            <p>
                <?php _e('To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'ft-gallery'); ?>
            </p>
            <form action="<?php echo esc_url(admin_url('admin.php?page=ft-gallery-system-info-submenu-page')); ?>" method="post" dir="ltr">
		<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="ft-gallery-sysinfo" title="<?php _e('To copy the system info, click here then press Ctrl + C (PC) or Cmd + C (Mac).', 'ft-gallery'); ?>">
### Begin System Info ###
            <?php
            $theme_data = wp_get_theme();
            $theme = $theme_data->Name . ' ' . $theme_data->Version; ?>

            SITE_URL: <?php echo site_url() . "\n"; ?>
            Feed Them Gallery Version: <?php echo ft_gallery_check_version() . "\n"; ?>

            -- Wordpress Configuration

WordPress Version: <?php echo get_bloginfo('version') . "\n"; ?>
            Multisite: <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>
            Permalink Structure: <?php echo get_option('permalink_structure') . "\n"; ?>
            Active Theme: <?php echo $theme . "\n"; ?>
            PHP Memory Limit: <?php echo ini_get('memory_limit') . "\n"; ?>
            WP_DEBUG: <?php echo defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

            -- Webserver Configuration

PHP Version: <?php echo PHP_VERSION . "\n"; ?>
            Web Server Info: <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

            -- PHP Configuration:

Safe Mode: <?php echo ini_get('safe_mode') ? "Yes" : "No\n"; ?>
            Upload Max Size: <?php echo ini_get('upload_max_filesize') . "\n"; ?>
            Post Max Size: <?php echo ini_get('post_max_size') . "\n"; ?>
            Upload Max Filesize: <?php echo ini_get('upload_max_filesize') . "\n"; ?>
            Time Limit: <?php echo ini_get('max_execution_time') . "\n"; ?>
            Max Input Vars: <?php echo ini_get('max_input_vars') . "\n"; ?>
            Allow URL File Open: <?php echo (ini_get('allow_url_fopen')) ? 'On (' . ini_get('display_errors') . ')' : 'N/A'; ?><?php echo "\n"; ?>
            Display Erros: <?php echo (ini_get('display_errors')) ? 'On (' . ini_get('display_errors') . ')' : 'N/A'; ?><?php echo "\n"; ?>

            -- PHP Extensions:

FSOCKOPEN: <?php echo (function_exists('fsockopen')) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
            cURL: <?php echo (function_exists('curl_init')) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>

            -- Active Plugins:

            <?php $plugins = get_plugins();
            $active_plugins = get_option('active_plugins', array());
            foreach ($plugins as $plugin_path => $plugin) {
                // If the plugin isn't active, don't show it.
                if (!in_array($plugin_path, $active_plugins))
                    continue;
                echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
            }
            if (is_multisite()) :
                ?>
                -- Network Active Plugins:

                <?php
                $plugins = wp_get_active_network_plugins();
                $active_plugins = get_site_option('active_sitewide_plugins', array());

                foreach ($plugins as $plugin_path) {
                    $plugin_base = plugin_basename($plugin_path);

                    // If the plugin isn't active, don't show it.
                    if (!array_key_exists($plugin_base, $active_plugins))
                        continue;

                    $plugin = get_plugin_data($plugin_path);

                    echo $plugin['Name'] . ' :' . $plugin['Version'] . "\n";
                }

            endif;

            if (is_plugin_active('feed-them-gallery/feed-them-gallery.php')) {
                $feed_them_gallery_license_key = get_option('feed_them_gallery_license_key');
                ?>
                -- License

                License Active:           <?php echo isset($feed_them_gallery_license_key) && $feed_them_gallery_license_key !== '' ? 'Yes' . "\n" : 'No' . "\n";
            } ?>

            ### End System Info ###</textarea>
            </form>
        </div>
        </div>

        <?php
    }
}//End Class