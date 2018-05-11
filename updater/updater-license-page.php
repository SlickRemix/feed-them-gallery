<?php

namespace feed_them_gallery;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Class Updater License Page
 *
 * @package feedthemsocial
 * @version 1.0.2
 */
class updater_license_page {

    // static variables
    private static $instance = false;

    /**
     * Construct
     *
     * FTS_settings_page constructor.
     *
     * @since 1.0.2
     */
    function __construct($updater_options_info, $prem_plugins_list) {

        //Set License Page Variables
        $this->store_url = $updater_options_info['store_url'];
        $this->main_menu_slug = $updater_options_info['main_menu_slug'];
        $this->license_page_slug = $updater_options_info['license_page_slug'];
        $this->setting_section_name = $updater_options_info['setting_section_name'];
        $this->setting_option_name = $updater_options_info['setting_option_name'];

        $this->prem_plugins = $prem_plugins_list;

        //Add the License Page
        $this->add_license_page();
    }

    /**
     * Add the License Page
     *
     * @since 1.0.2
     */
    function add_license_page() {
        if (!function_exists('is_plugin_active'))
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');

        $prem_active = false;
        foreach ($this->prem_plugins as $plugin) {
            if (is_plugin_active($plugin['plugin_url'])) {
                $prem_active = true;
            }
        }

        add_action('admin_menu', array($this, 'license_menu'));
        add_action('admin_init', array($this, 'register_options'));
    }

    /**
     * Register Plugin License Page Options (overrides options from prem extensions updater files
     *
     * @since 1.0.2
     */
    function register_options() {
        //Create settings section
        add_settings_section($this->setting_section_name, '', null, $this->license_page_slug);

        //Register Option for settings array
        register_setting($this->license_page_slug, $this->setting_option_name, array($this, 'updater_sanitize_license'));

        //Add settings fields for each plugin/extension
        foreach ($this->prem_plugins as $key => $plugin) {
            //For plugins/extensions that are active
            if (is_plugin_active($plugin['plugin_url'])) {
                $args = array(
                    'key' => $key,
                    'plugin_name' => $plugin['title'],
                );

                add_settings_field($this->setting_option_name . '[' . $key . '][license_key]', '', array($this, 'add_option_setting'), $this->license_page_slug, $this->setting_section_name, $args);
            } //Show Special Box for non actives plugins/extensions!
            else {
                //Set Variables
                $args = array(
                    'plugin_name' => $plugin['title'],
                    'demo_url' => $plugin['demo_url'],
                    'purchase_url' => $plugin['purchase_url'],
                );
                //show Premium needed box
                add_settings_field($this->setting_option_name . '[' . $key . '][license_key]', '', array($this, 'display_premium_needed_license'), $this->license_page_slug, $this->setting_section_name, $args);
            }
        }
    }

    /**
     * Add Options to Plugin License page
     *
     * @param $args
     * @since 1.0.2
     */
    function add_option_setting($args) {
        $key = $args['key'];
        $plugin_name = $args['plugin_name'];

        //License Key Array Option
        $settings_array = get_option($this->setting_option_name);

        $license = isset($settings_array[$key]['license_key']) ? $settings_array[$key]['license_key'] : '';
        $status = isset($settings_array[$key]['license_status']) ? $settings_array[$key]['license_status'] : '';
        $license_error = isset($settings_array[$key]['license_error']) ? $settings_array[$key]['license_error'] : '';

        ?>
        <tr valign="top" class="ftg-license-wrap">
            <th scope="row" valign="top">
                <?php _e($plugin_name, 'feed-them-gallery'); ?>
            </th>
            <td>
                <input id="<?php echo $this->setting_option_name ?>[<?php echo $key ?>][license_key]" name="<?php echo $this->setting_option_name ?>[<?php echo $key ?>][license_key]" type="text" placeholder="<?php _e('Enter your license key', 'feed-them-gallery'); ?>" class="regular-text" value="<?php esc_attr_e($license); ?>"/>
                <label class="description" for="<?php echo $this->setting_option_name ?>[<?php echo $key ?>][license_key]"><?php if ($status !== false && $status == 'valid') { ?>

                        <?php wp_nonce_field('license_page_nonce', 'license_page_nonce'); ?>
                        <input type="submit" class="button-secondary" name="<?php echo $key ?>_license_deactivate" value="<?php _e('Deactivate License', 'feed-them-gallery'); ?>"/>

                        <div class="edd-license-data"><p><?php _e('License Key Active.', 'feed-them-gallery'); ?></p></div>

                        <?php
                    } else {
                        wp_nonce_field('license_page_nonce', 'license_page_nonce'); ?>
                        <div class="edd-license-data edd-license-msg-error">
                            <p><?php echo $license_error ?><?php $this->update_admin_notices();
                                _e('To receive updates notifications, please enter your valid license key.', 'feed-them-gallery'); ?></p>
                        </div>
                    <?php } ?></label>

                <?php
                //Create Upgrade Button
                if (isset($license) && !empty($license) && $status !== false && $status == 'valid') {
                    echo sprintf(__('%1$sUpgrade License%2$s', 'feed-them-gallery'),
                        '<a class="edd-upgrade-license-btn button-secondary" target="_blank" href="'.esc_url('https://www.slickremix.com/my-account/?&view=upgrades&license_key=' . $license ).'">',
                        '</a>'
                    );
                }
                ?>
            </td>
        </tr> <?php
    }

    /**
     * Add Plugin License Menu
     *
     * @since 1.0.2
     */
    function license_menu() {
        global $submenu;

        add_submenu_page($this->main_menu_slug, __('Plugin License', 'feed-them-gallery'), __('Plugin License', 'feed-them-gallery'), 'manage_options', $this->license_page_slug, array($this, 'license_page'));
    }

    /**
     * Add FREE Plugin License Page for displaying what is available to extend ftg
     *
     * @since 1.0.2
     */
    function license_page() {

        ?>
        <div class="wrap">
            <h2><?php _e('Plugin License Options', 'feed-them-gallery'); ?></h2>
            <div class="license-note"> <?php
                echo sprintf(__('If you need more licenses or your key has expired, please go to the %1$sMY ACCOUNT%2$s page on our website to upgrade or renew your license.%3$sTo get started follow the instructions below.', 'feed-them-gallery'),
                    '<a href="'.esc_url('https://www.slickremix.com/my-account/').'" target="_blank">',
                    '</a>',
                    '<br/>'
                ); ?>
            </div>

            <div class="ftg-activation-msg">
                <ol>
                    <li><?php
                       echo sprintf(__('Install the zip file of the plugin you should have received after purchase on the %1$splugins page%2$s and leave the free version active too.', 'feed-them-gallery'),
                           '<a href="'.esc_url('plugin-install.php').'">',
                           '</a>'
                       ); ?>
                    </li>
                    <li><?php
                        echo sprintf(__('Now Enter your License Key and Click the %1$Save Changes button%2$s', 'feed-them-gallery'),
                            '<strong>',
                            '</strong>'
                        ); ?>
                    </li>
                </ol>
            </div>
            <form method="post" action="options.php" class="ftg-license-master-form">
                <?php settings_fields($this->license_page_slug); ?>
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
                        do_settings_fields($this->license_page_slug, $this->setting_section_name);
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
                <?php
                if ($prem_active === true) {
                    submit_button();
                }
                ?>
            </form>
            <div style="margin-top:0px;">
<!--                <a href="https://www.slickremix.com/downloads/feed-them-gallery/" target="_blank"><img style="max-width: 100%;" src="--><?php //echo plugins_url('feed-them-social/admin/images/ft-gallery-promo.jpg'); ?><!--"/></a>-->
            </div>
        </div>
        <?php
    }

    /**
     * Display Premium Needed boxes for plugins not active/installed for ftg
     *
     * @param $args Passed by function or add_settings_field
     * @since 1.0.2
     */
    function display_premium_needed_license($args) {
        $this->plugin_title = $args['plugin_name'];
        $this->demo_url = $args['demo_url'];
        $this->purchase_url = $args['purchase_url'];
        ?>

        <tr valign="top" class="ftg-license-wrap">
            <th scope="row" valign="top"><?php _e($this->plugin_title, 'feed-them-gallery'); ?></th>
            <td>
                <div class="ftg-no-license-overlay">
                    <div class="ftg-no-license-button-wrap">
                        <?php echo sprintf(__('%1$sDemo%2$s', 'feed-them-gallery'),
                        '<a class="ftg-no-license-button-purchase-btn" href="'.esc_url($this->demo_url).'" target="_blank">',
                            '</a>'
                        );  ?>

                        <?php echo sprintf(__('%1$sBuy Extension%2$s', 'feed-them-gallery'),
                            '<a class="ftg-no-license-button-demo-btn" href="'.esc_url($this->purchase_url).'" target="_blank">',
                            '</a>'
                        );  ?>
                    </div>
                </div>
                <input id="no_license_key" name="no_license_key" type="text" placeholder="<?php _e('Enter your license key', 'feed-them-gallery'); ?>" class="regular-text" value="">
                <label class="description" for="no_license_key">
                    <div class="edd-license-data edd-license-msg-error"><p><?php _e('To receive updates notifications, please enter your valid license key.', 'feed-them-gallery'); ?></p></div>
                </label>
            </td>
        </tr>
        <?php return;
    }

    /**
     * Generates an Upgrade license button based on information from SlickRemix's license keys
     *
     * @param $license_key
     * @since 1.0.2
     */
    function upgrade_license_btn($plugin_key, $license_key, $status) {
        if (isset($license_key) && !empty($license_key) && $status !== false && $status == 'valid') {
            //$api_params = array();
            //$response = wp_remote_get('https://www.slickremix.com/wp-json/slick-license/v2/get-license-info?license_key=' . $license_key, array('timeout' => 60, 'sslverify' => false, 'body' => $api_params));

            $response[$plugin_key] = 'https://www.slickremix.com/wp-json/slick-license/v2/get-license-info?license_key=' . $license_key;

            $fts_functions = new feed_them_social_functions();

            $response = $fts_functions->fts_get_feed_json($response);

            $license_data = json_decode($response[$plugin_key]);

            if (isset($license_data->payment_id) && !empty($license_data->payment_id) && isset($license_data->payment_id) && !empty($license_data->payment_id)) {
                echo sprintf(__('%1$sUpgrade License%2$s', 'feed-them-gallery'),
                    '<a class="edd-upgrade-license-btn button-secondary" href="'.esc_url('https://www.slickremix.com/my-account/?&view=upgrades&license_key=' . $license_data->license_id).'" target="_blank">',
                    '</a>'
                );
            }
            return;
        }
        return;
    }

    /**
     * Sanitize License Keys
     *
     * @param $new
     * @return mixed
     * @since 1.5.6
     */
    function updater_sanitize_license($new) {

        $settings_array = get_option($this->setting_option_name);

        if (!$settings_array) {
            $settings_array = $new;
        } else {
            $settings_array = array_merge($settings_array, $new);
        }

        foreach ($this->prem_plugins as $key => $plugin) {
            if (is_plugin_active($plugin['plugin_url'])) {

                // listen for our activate button to be clicked
                if (isset($_POST[$key . '_license_deactivate'])) {
                    $settings_array = $this->deactivate_license($key, $settings_array[$key]['license_key'], $settings_array);
                } else {
                    //Clean Up old options if they exist
                    $old_license = get_option($key . '_license_key');
                    $old_status = get_option($key . '_license_status');

                    if (!empty($old_license)) {
                        delete_option($key . '_license_key');
                    }
                    if (!empty($old_status)) {
                        delete_option($key . '_license_status');
                    }
                    $settings_array = $this->activate_license($key, $new[$key]['license_key'], $settings_array);
                }
            }
        }

        return $settings_array;
    }

    /**
     * Activate License Key
     *
     * @since 1.5.6
     */
    function activate_license($key, $license, $settings_array) {

        $license = trim($license);

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'activate_license',
            'license' => $license,
            'item_name' => urlencode($this->prem_plugins[$key]['title']), // the name of our product in EDD
            'url' => home_url(),
        );

        // Call the custom API.
        $response = wp_remote_post($this->store_url, array('timeout' => 60, 'sslverify' => false, 'body' => $api_params));

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

            if (is_wp_error($response)) {
                $message = $response->get_error_message();
            } else {
                $message = __('An error occurred, please try again.', 'feed-them-gallery');
            }

        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if (false === $license_data->success) {

                switch ($license_data->error) {

                    case 'expired' :

                        $message = sprintf(
                            __('Your license key expired on %s.', 'feed-them-gallery'),
                            date_i18n(get_option('date_format'), strtotime($license_data->expires, time()))
                        );
                        break;

                    case 'revoked' :

                        $message = __('Your license key has been disabled.', 'feed-them-gallery');
                        break;

                    case 'missing' :

                        $message = __('Invalid license.', 'feed-them-gallery');
                        break;

                    case 'invalid' :
                    case 'site_inactive' :

                        $message = __('Your license is not active for this URL.', 'feed-them-gallery');
                        break;

                    case 'item_name_mismatch' :

                        $message = sprintf(__('This appears to be an invalid license key for %s.', 'feed-them-gallery'), $this->prem_plugins[$key]['title']);
                        break;

                    case 'no_activations_left':

                        $message = __('Your license key has reached its activation limit.', 'feed-them-gallery');
                        break;

                    default :

                        $message = __('An error occurred, please try again.', 'feed-them-gallery');
                        break;
                }
            }
        }

        //There is an error so set it in array
        if (!empty($message)) {
            unset($settings_array[$key]['license_status']);
            $settings_array[$key]['license_error'] = $message;

            return $settings_array;
        }

        //No errors. Set License Status in array
        unset($settings_array[$key]['license_error']);
        $settings_array[$key]['license_status'] = $license_data->license;

        return $settings_array;
    }

    /***********************************************
     * Illustrates how to deactivate a license key.
     * This will decrease the site count
     ***********************************************/
    function deactivate_license($key, $license, $settings_array) {
        // retrieve the license from the database
        $license = trim($license);

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license' => $license,
            'item_name' => urlencode($this->prem_plugins[$key]['title']), // the name of our product in EDD
            'url' => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post($this->store_url, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {

            if (is_wp_error($response)) {
                $message = $response->get_error_message();
            } else {
                $message = __('An error occurred, please try again.', 'feed-them-gallery');
            }
        }

        // decode the license data
        $license_data = json_decode(wp_remote_retrieve_body($response));

        //There is an error so set it in array
        if (!empty($message)) {
            unset($settings_array[$key]['license_status']);
            $settings_array[$key]['license_error'] = $message;

            return $settings_array;
        }

        // $license_data->license will be either "deactivated" or "failed"
        if ($license_data->license == 'deactivated') {
            //No errors. unset plugin key from main options array
            unset($settings_array[$key]);
        }

        return $settings_array;
    }

    /**
     * This is a means of catching errors from the activation method above and displaying it to the customer
     *
     * @since 1.0.2
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
                    // Developers can put a custom success message here for when activation is successful if they want.
                    break;
            }
        }
    }

}//End CLASS

?>