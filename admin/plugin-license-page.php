<?php
/**
 * Plugin License Page Class
 *
 * This Class is for the Plugin License Page for users to add their license key
 *
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @author   SlickRemix
 */

namespace feed_them_gallery;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * FT_Gallery_Plugin_License_Page
 * @package feed_them_gallery
 */
class FT_Gallery_Plugin_License_Page {

    /**
     * Premium Plugins
     *
     * A List of Premium plugins
     *
     * @var array|string
     */
    public $prem_plugins = '';

    /**
     * Main Menu Slug
     *
     * Slug for adding to WordPress Dashboard main menu
     *
     * @var string
     */
    public $main_menu_slug = 'edit.php?post_type=ft_gallery';

    /**
     * License Page Slug
     *
     * For setting the License Page slug
     *
     * @var string
     */
    public $license_page_slug = 'ft-gallery-license-page';

    /**
     * Plugin Identifier
     *
     * @var string
     */
    public $plugin_identifier = '';

    // static variables
    /**
     * @var bool
     */
    private static $instance = false;

    /**
     * Construct
     *
     * FTS_settings_page constructor
     *
     * @since 1.0.0
     */
    function __construct() {
        //List of Plugins! Keep this up to date in order for showing what is available for FTS on Plugin License page.
        $this->prem_plugins = array(
            'feed_them_gallery_premium' => array(
                'title' => 'Feed Them Gallery Premium',
                'plugin_url' => 'feed-them-gallery-premium/feed-them-gallery-premium.php',
                'demo_url' => 'http://feedthemgallery.com/',
                'purchase_url' => 'https://www.slickremix.com/downloads/feed-them-gallery/',
            ),
        );
        $this->install();
    }

    /**
     * Install
     *
     * Install Updater
     *
     * @since 1.0.0
     */
    function install() {
        if (!function_exists('is_plugin_active'))
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');

        $prem_active = false;
        foreach ($this->prem_plugins as $plugin) {
            if (is_plugin_active($plugin['plugin_url'])) {
                $prem_active = true;
            }

        }
        //No Premium plugins Active make Plugin License page.
        if ($prem_active == false) {
            if (!self::$instance) {
                self::$instance = true;
                add_action('admin_menu', array($this, 'license_menu'));
            }
        }
        //Premium Active: Add boxes to plugin licence page they don't have.
        //Rgister new override options
        if (isset($_GET['page']) && $_GET['page'] == $this->license_page_slug) {
            add_action('current_screen', array($this, 'register_options'));
        }
    }

    /**
     * Register Options
     *
     * Register Plugin License Page Options (overrides options from premium extensions updater files
     *
     * @since 1.0.0
     */
    function register_options() {
        add_settings_section('main_section', '', null, $this->license_page_slug);

        foreach ($this->prem_plugins as $key => $plugin) {
            if (is_plugin_active($plugin['plugin_url'])) {
                $this->plugin_identifier = $key;
                register_setting($this->license_page_slug . '_license_manager_page', $key . '_license_key', array($this, 'edd_sanitize_license'));
                $args = array(
                    'key' => $key,
                    'plugin_name' => $plugin['title'],
                    'demo_url' => $plugin['demo_url'],
                    'purchase_url' => $plugin['purchase_url'],
                );
                //Show Active Premium Plugins
                add_settings_field($key . '_license_key', '', array($this, 'add_option_setting'), $this->license_page_slug, 'main_section', $args);
            } else {
                register_setting($this->license_page_slug . '_license_manager_page', $key . '_license_key');
                //Show Special Box for non actives plugins!
                //Set Variables
                $args = array(
                    'plugin_name' => $plugin['title'],
                    'demo_url' => $plugin['demo_url'],
                    'purchase_url' => $plugin['purchase_url'],
                );

                //show Premium needed box
                add_settings_field($key . '_license_key', '', array($this, 'display_premium_needed_license'), $this->license_page_slug, 'main_section', $args);
            }
        }
    }

    /**
     * Add Option Settings
     *
     * Add Options to Plugin License page
     *
     * @param $args
     * @since 1.0.0
     */
    function add_option_setting($args) {
        $key = $args['key'];
        $plugin_name = $args['plugin_name'];

        $license = get_option($key . '_license_key');
        $status = get_option($key . '_license_status');
        ?>
        <tr valign="top" class="ft-gallery-license-wrap">
            <th scope="row" valign="top">
                <?php _e($plugin_name); ?>
            </th>
            <td>
                <input id="<?php echo $key ?>_license_key" name="<?php echo $key ?>_license_key" type="text" placeholder="<?php _e('Enter your license key'); ?>" class="regular-text" value="<?php esc_attr_e($license); ?>"/>
                <label class="description" for="<?php print $key ?>_license_key"><?php if ($status !== false && $status == 'valid') { ?>

                        <?php wp_nonce_field($key . 'license_page_nonce', $key . 'license_page_nonce'); ?>
                        <input type="submit" class="button-secondary" name="<?php echo $key ?>_edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>

                        <div class="edd-license-data"><p><?php _e('License Key Active.'); ?></p></div>

                        <?php
                    } else {
                        wp_nonce_field($key . 'license_page_nonce', $key . 'license_page_nonce'); ?>
                        <input type="submit" class="button-secondary" name="<?php echo $key ?>_edd_license_activate" value="<?php _e('Activate License'); ?>"/>
                        <div class="edd-license-data edd-license-msg-error"><p><?php $this->update_admin_notices();
                                _e('To receive updates notifications, please enter your valid license key.'); ?></p>
                        </div>
                    <?php } ?></label>


                <?php
                //Create Upgrade Button
                if (isset($license) && !empty($license) && $status !== false && $status == 'valid') {
                    echo '<a class="edd-upgrade-license-btn button-secondary" target="_blank" href="https://www.slickremix.com/my-account/?&view=upgrades&license_key=' . $license . '">Upgrade License</a>';
                }
                ?>
            </td>
        </tr> <?php
    }

    /**
     * License Menu
     *
     * Add Plugin License Menu
     *
     * @since 1.0.0
     */
    function license_menu() {
        global $submenu;
        //Override submenu page if needed
        if (isset($submenu[$this->main_menu_slug]) && in_array($this->license_page_slug, wp_list_pluck($submenu[$this->main_menu_slug], 2))) {
            remove_submenu_page($this->main_menu_slug, $this->license_page_slug);
        }
        if (isset($submenu[$this->main_menu_slug]) && !in_array($this->license_page_slug, wp_list_pluck($submenu[$this->main_menu_slug], 2))) {
            add_submenu_page($this->main_menu_slug, __('Plugin License', 'feed-them-gallery'), __('Plugin License', 'feed-them-gallery'), 'manage_options', $this->license_page_slug, array($this, 'license_page'));
        }
    }

    /**
     * License Page
     *
     * Add FREE Plugin License Page for displaying what is available to extend FTS
     *
     * @since 1.0.0
     */
    function license_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('Plugin License Options'); ?></h2>
            <div class="license-note"> <?php _e("If you need more licenses or your key has expired, please go to the <a href='https://www.slickremix.com/my-account/' target='_blank'>MY ACCOUNT</a> page on our website to upgrade or renew your license.<br/>To get started follow the instructions below.", "feed-them-social") ?> </div>

            <div class="ft-gallery-activation-msg">
                <ol>
                    <li><?php _e('Install the zip file of the plugin you should have received after purchase on the <a href="https://www.slickremix.com/betablog/wp-admin/plugin-install.php">plugins page</a> and leave the free version active too.', 'feed-them-gallery') ?></li>
                    <li><?php _e('Now Enter your License Key and Click the <strong>Save Changes button</strong>.', 'feed-them-gallery') ?></li>
                    <li><?php _e('Finally, Click the <strong>Activate License button</strong>.', 'feed-them-gallery') ?></li>
                </ol>
            </div>
            <form method="post" action="options.php" class="ft-gallery-license-master-form">
                <?php settings_fields($this->license_page_slug . '_license_manager_page'); ?>
                <table class="form-table">
                    <tbody>

                    <?php
                    $prem_active = false;
                    foreach ($this->prem_plugins as $plugin) {
                        if (is_plugin_active($plugin['plugin_url'])) {
                            $prem_active = true;
                        }
                    }
                    //No Premium plugins Active make Plugin License page.
                    if ($prem_active === true) {
                        do_settings_fields($this->license_page_slug, 'main_section');
                    } else {
                        //Each Premium Plugin wrap
                        foreach ($this->prem_plugins as $plugin) {
                            //Set Variables
                            $args = array(
                                'plugin_name' => $plugin['title'],
                                'demo_url' => $plugin['demo_url'],
                                'purchase_url' => $plugin['purchase_url'],
                            );

                            //show Premium needed box
                            $this->display_premium_needed_license($args);
                        }
                    } ?>

                    </tbody>
                </table>
                <?php if ($prem_active === true) {
                    submit_button();
                } ?>
            </form>

        </div>
        <?php
    }

    /**
     * Display Premium Need License
     *
     * Display Premium Needed boxes for plugins not active/installed for FTS
     *
     * @param string $args passed by function or add_settings_field
     * @since 1.0.0
     */
    function display_premium_needed_license($args) {
        $this->plugin_title = $args['plugin_name'];
        $this->demo_url = $args['demo_url'];
        $this->purchase_url = $args['purchase_url'];

        $key = $args['demo_url'];
        $plugin_name = $args['purchase_url'];

        ?>

        <tr valign="top" class="ft-gallery-license-wrap">
            <th scope="row" valign="top"><?php echo $this->plugin_title ?></th>
            <td>
                <div class="ft-gallery-no-license-overlay">
                    <div class="ft-gallery-no-license-button-wrap"
                    ">
                    <a class="ft-gallery-no-license-button-purchase-btn" href="<?php echo $this->demo_url ?>" target="_blank">Demo</a>
                    <a class="ft-gallery-no-license-button-demo-btn" href="<?php echo $this->purchase_url ?>" target="_blank">Buy
                        Extension</a>
                </div>
                </div>
                <input id="no_license_key" name="no_license_key" type="text" placeholder="Enter your license key" class="regular-text" value="">
                <label class="description" for="no_license_key">
                    <div class="edd-license-data edd-license-msg-error"><p>To receive updates notifications, please
                            enter your valid license key.</p></div>

                </label>
            </td>
        </tr>
        <?php return;
    }

    /**
     * Upgrade License Button
     *
     * Generates an Upgrade license button based on information from SlickRemix's license keys
     *
     * @param $plugin_key
     * @param $license_key
     * @param $status
     * @since 1.0.0
     */
    function upgrade_license_btn($plugin_key , $license_key, $status) {
        if (isset($license_key) && !empty($license_key) && $status !== false && $status == 'valid') {
            //$api_params = array();
            //$response = wp_remote_get('https://www.slickremix.com/wp-json/slick-license/v2/get-license-info?license_key=' . $license_key, array('timeout' => 60, 'sslverify' => false, 'body' => $api_params));

            $response[$plugin_key] = 'https://www.slickremix.com/wp-json/slick-license/v2/get-license-info?license_key=' . $license_key;

            $fts_functions = new feed_them_social_functions();

            $response = $fts_functions->fts_get_feed_json($response);

            $license_data = json_decode($response[$plugin_key]);

            if(isset($license_data->payment_id) && !empty($license_data->payment_id) && isset($license_data->payment_id ) && !empty($license_data->payment_id)){
                echo '<a class="edd-upgrade-license-btn button-secondary" target="_blank" href="https://www.slickremix.com/my-account/?&view=upgrades&license_key=' . $license_data->license_id . '">Upgrade License</a>';
            }
            return;
        }
        return;
    }

    /**
     * Sanitize License Keys
     *
     * Sanitize the license keys
     *
     * @param $new
     * @return mixed
     * @since 1.0.0
     */
    function edd_sanitize_license($new) {
        $old = get_option($this->plugin_identifier . '_license_key');
        if ($old && $old != $new) {
            delete_option($this->plugin_identifier . '_license_status'); // new license has been entered, so must reactivate
        }
        return $new;
    }

    /**
     * Update Admin Notices
     * This is a means of catching errors from the activation method above and displaying it to the customer
     *
     * @since 1.0.0
     */
    function update_admin_notices() {
        if (isset($_GET['sl_activation']) && !empty($_GET['message'])) {

            switch ($_GET['sl_activation']) {

                case 'false':
                    $message = urldecode($_GET['message']);
                    echo $message;
                    break;

                case 'true':
                default:
                    // Developers can put a custom success message here for when activation is successful if they want
                    break;
            }
        }
    }

}//End CLASS
?>