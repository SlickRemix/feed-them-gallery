<?php
/**
 * Gallery Class
 *
 * This class is what initiates the Feed Them Gallery class
 *
 * @version  1.0.0
 * @package  FeedThemSocial/Core
 * @author   SlickRemix
 */

namespace feed_them_gallery;
// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Gallery
 * @package FeedThemSocial/Core
 */
class Gallery {

    /**
     * Parent Post ID
     * used to set Gallery ID
     *
     * @var string
     */
    public $parent_post_id = '';

    /**
     * Saved Settings Array
     * an array of settings to save when saving page
     *
     * @var string
     */
    public $saved_settings_array = array();

    /**
     * Global Prefix
     * Sets Prefix for global options
     *
     * @var string
     */
    public $global_prefix = 'global_';

    /**
     * ZIP Gallery Class
     * initiates ZIP Gallery Class
     *
     * @var \feed_them_gallery\Zip_Gallery|string
     */
    public $zip_gallery_class = '';

    /**
     * Gallery Options
     * initiates Gallery Options Class
     *
     * @var \feed_them_gallery\Zip_Gallery|string
     */
    public $gallery_options_class = '';


    /**
     * Gallery constructor.
     */
    public function __construct() {
        // Globalize:
        global $wp_version;

        $required_plugins = array();

        //Scripts
        add_action('admin_enqueue_scripts', array($this, 'ft_gallery_scripts'));
        //******************************************
        // Gallery Layout Opyions
        //******************************************
        $this->gallery_options_class = new Gallery_Options();

        $this->saved_settings_array = $this->gallery_options_class->all_gallery_options();

        //If Premium add Functionality
        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
            $this->zip_gallery_class = new Zip_Gallery();
        }

        // Define constants:
        if (!defined('MY_TEXTDOMAIN')) {
            define('MY_TEXTDOMAIN', 'feed-them-gallery');
        }

        //Register Gallery CPT
        add_action('init', array($this, 'ft_gallery_cpt'));

        //Register Gallery Categories
        //add_action( 'init', array($this, 'ft_gallery_categories') );

        //Add Gallery Categories to attachments
        //add_action( 'init', array($this, 'ft_gallery_add_cats_to_attachments') , 15);

        //Response Messages
        add_filter('post_updated_messages', array($this, 'ft_gallery_updated_messages'));

        //Gallery List function
        add_filter('manage_ft_gallery_posts_columns', array($this, 'ft_gallery_set_custom_edit_columns'));
        add_action('manage_ft_gallery_posts_custom_column', array($this, 'ft_gallery_custom_edit_column'), 10, 2);

        //Change Button Text
        add_filter('gettext', array($this, 'ft_gallery_set_button_text'), 20, 3);
        //Add Meta Boxes
        add_action('add_meta_boxes', array($this, 'ft_gallery_add_metaboxes'));

        //Rename Submenu Item to Galleries
        add_filter('attribute_escape', array($this, 'ft_gallery_rename_submenu_name'), 10, 2);
        //Add Shortcode
        add_shortcode('ft_gallery_list', array($this, 'ft_gallery_display_list'));

        // Drag and Drop, buttons etc for media
        add_action('wp_ajax_plupload_action', array($this, 'ft_gallery_plupload_action'));

        // Set local variables:
        $this->plugin_locale = MY_TEXTDOMAIN;
        // Set WordPress version:
        $this->wordpress_version = substr(str_replace('.', '', $wp_version), 0, 2);

        add_action('current_screen', array($this, 'ft_gallery_check_page'));

        //Save Meta Box Info
        add_action('save_post', array($this, 'ft_gallery_save_custom_meta_box'), 10, 2);

        //Add API Endpoint
        add_action('rest_api_init', array($this, 'ft_galley_register_gallery_options_route'));

        add_action('wp_ajax_list_update_order', array($this, 'ft_gallery_order_list'));

        // Create another image size for our gallery edit pages
        add_image_size('ft_gallery_thumb', 150, 150, true);
        // Add the image name to the media library so we can get a clean version when showing thumbnail on the page for the first time
        add_filter('image_size_names_choose', array($this, 'ft_gallery_custom_thumb_sizes'));

        if (get_option('ft_gallery_duplicate_post_show') == '') {

            add_action('admin_action_ft_gallery_duplicate_post_as_draft', array($this, 'ft_gallery_duplicate_post_as_draft'));
            add_filter('page_row_actions', array($this, 'ft_gallery_duplicate_post_link'), 10, 2);
            add_filter('ft_gallery_row_actions', array($this, 'ft_gallery_duplicate_post_link'), 10, 2);
            add_action('post_submitbox_start', array($this, 'ft_gallery_duplicate_post_add_duplicate_post_button'));

        }
    }

    /**
     * FT Gallery Tab Notice HTML
     *
     * creates notice html for return
     *
     * @since 1.0.0
     */
    function ft_gallery_tab_premium_msg() {
        echo sprintf(__('%1$sPlease purchase, install and activate %2$sFeed Them Gallery Premium%3$s for these additional awesome features!%4$s', 'feed-them-gallery'),
            '<div class="ft-gallery-premium-mesg">',
            '<a href="'.esc_url('https://www.slickremix.com/downloads/feed-them-gallery/').'">',
            '</a>',
            '</div>'
        );
    }

    /**
     * FT Gallery Custom Thumb Sizes
     *
     * Adds Custom sizes too
     *
     * @param array $sizes
     * @return array
     * @since
     */
    function ft_gallery_custom_thumb_sizes($sizes) {
        return array_merge($sizes, array(
            'ft_gallery_thumb' => __('Feed Them Gallery Thumb'),
        ));
    }

    /**
     * FT Gallery Order List
     *
     * Attachment order list
     *
     * @since 1.0.0
     */
    function ft_gallery_order_list() {
        // we use the list_item (id="list_item_23880") which then finds the ID right after list_item and we use the id from there.
        $attachment_ID = $_POST['list_item'];

        foreach ($attachment_ID as $img_index => $img_id) {
            $a = array(
                'ID' => $img_id,
                'menu_order' => $img_index
            );
            wp_update_post($a);
        }
        // return $attachment_ID;
        // error_log('wtf is going on with the $_POST[\'list_item\']  '.print_r(get_post_meta(18240, 'wp_logo_slider_images', true), true));
    }

    /**
     * FT Gallery Check Page
     *
     * What page are we on?
     *
     * @since 1.0.0
     */
    function ft_gallery_check_page() {
        $current_screen = get_current_screen();

        if (is_admin() && $current_screen->post_type == 'ft_gallery' && $current_screen->base == 'post') {

            if (isset($_GET['post'])) {
                $this->parent_post_id = $_GET['post'];
            }
            if (isset($_POST['post'])) {
                $this->parent_post_id = $_POST['post'];
            }
        }
    }

    /**
     * FT Gallery Register Gallery Options (REST API)
     *
     * Register the gallery options via REST API
     *
     * @since 1.0.0
     */
    public function ft_galley_register_gallery_options_route() {
        register_rest_route('ftgallery/v2', '/gallery-options', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array($this, 'ft_gallery_get_gallery_options'),
        ));
    }

    /**
     * FT Gallery Get Gallery Options (REST API)
     *
     * Get options using WordPress's REST API
     *
     * @param $gallery_id
     * @return string
     * @since 1.0.0
     */
    public function ft_gallery_get_gallery_options_rest($gallery_id) {

        $request = new \WP_REST_Request('GET', '/ftgallery/v2/gallery-options');

        $request->set_param('gallery_id', $gallery_id);

        $response = rest_do_request($request);

        // Check for error
        if (is_wp_error($response)) {
            return __('oops something isn\'t right.', 'feed-them-gallery');
        }

        $final_response = isset($response->data) ? $response->data : __('No Images attached to this post.', 'feed-them-gallery');

        return $final_response;
    }

    /**
     * FT Gallery Get Gallery Options
     *
     * Get options set for a gallery
     *
     * @param $gallery_id
     * @return array
     * @since 1.0.0
     */
    public function ft_gallery_get_gallery_options($gallery_id) {

        $post_info = get_post($gallery_id['gallery_id']);

        // echo '<pre>';
        // print_r($post_info);
        // echo '</pre>';

        $options_array = array();

        //Basic Post Info
        $options_array['ft_gallery_image_id'] = isset($post_info->ID) ? $post_info->ID : __('This ID does not exist anymore', 'feed-them-gallery');
        $options_array['ft_gallery_author'] = isset($post_info->post_author) ? $post_info->post_author : '';
        //   $options_array['ft_gallery_post_date'] = $post_info->post_date_gmt;
        $options_array['ft_gallery_post_title'] = isset($post_info->post_title) ? $post_info->post_title : '';
        //   $options_array['ft_gallery_post_alttext'] = $post_info->post_title;
        //   $options_array['ft_gallery_comment_status'] = $post_info->comment_status;


        foreach ($this->saved_settings_array as $box_array) {
            foreach ($box_array as $box_key => $settings) {
                if ($box_key == 'main_options') {
                    //Gallery Settings
                    foreach ($settings as $option) {
                        $option_name = !empty($option['name']) ? $option['name'] : '';
                        $option_default_value = !empty($option['default_value']) ? $option['default_value'] : '';

                        if (!empty($option_name)) {
                            $option_value = get_post_meta($gallery_id['gallery_id'], $option_name, true);
                            //Set value or use Default_value
                            $options_array[$option_name] = !empty($option_value) ? $option_value : $option_default_value;
                        }

                    }
                }
            }
        }

        return $options_array;
    }

    /**
     * FT Gallery Custom Post Type
     *
     * Create FT Gallery custom post type
     *
     * @since 1.0.0
     */
    public function ft_gallery_cpt() {
        $responses_cpt_args = array(
            'label' => __('Feed Them Gallery', 'feed-them-gallery'),
            'labels' => array(
                'menu_name' => __('Galleries', 'feed-them-gallery'),
                'name' => __('Galleries', 'feed-them-gallery'),
                'singular_name' => __('Gallery', 'feed-them-gallery'),
                'add_new' => __('Add Gallery', 'feed-them-gallery'),
                'add_new_item' => __('Add New Gallery', 'feed-them-gallery'),
                'edit_item' => __('Edit Gallery', 'feed-them-gallery'),
                'new_item' => __('New Gallery', 'feed-them-gallery'),
                'view_item' => __('View Gallery', 'feed-them-gallery'),
                'search_items' => __('Search Galleries', 'feed-them-gallery'),
                'not_found' => __('No Galleries Found', 'feed-them-gallery'),
                'not_found_in_trash' => __('No Galleries Found In Trash', 'feed-them-gallery'),
            ),

            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'exclude_from_search' => true,

            'capabilities' => array(
                'create_posts' => true, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
            ),
            'map_meta_cap' => true, //Allows Users to still edit Payments
            'has_archive' => true,
            'hierarchical' => true,
            'query_var' => 'ft_gallery',

            'menu_icon' => '',
            'supports' => array('title', 'revisions'),
            'order' => 'DESC',
            // Set the available taxonomies here
            'taxonomies' => array('ft_gallery_topics')
        );
        register_post_type('ft_gallery', $responses_cpt_args);
    }

    /**
     * FT Gallery Categories (Custom Taxonomy)
     *
     * Create FT Gallery Custom Taxonomy
     *
     * @since 1.0.2
     */
    public function ft_gallery_categories() {

        $labels = array(
            'name' => _x( 'Categories', 'feed-them-gallery'),
            'singular_name' => _x( 'Category', 'feed-them-gallery'),
            'search_items' =>  __( 'Search Categories', 'feed-them-gallery'),
            'all_items' => __( 'All Categories', 'feed-them-gallery'),
            'parent_item' => __( 'Parent Category', 'feed-them-gallery'),
            'parent_item_colon' => __( 'Parent Category:', 'feed-them-gallery'),
            'edit_item' => __( 'Edit Category', 'feed-them-gallery'),
            'update_item' => __( 'Update Category', 'feed-them-gallery'),
            'add_new_item' => __( 'Add New Category', 'feed-them-gallery'),
            'new_item_name' => __( 'New Category Name', 'feed-them-gallery'),
            'menu_name' => __( 'Categories', 'feed-them-gallery'),
        );

        register_taxonomy('ft_gallery_cats', array('ft_gallery'), array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => true,
            'update_count_callback' => '_update_generic_term_count'
        ));
    }

    /**
     * FT Gallery Register Taxonomy for Attachments
     *
     * Registers
     *
     * @since 1.0.2
     */
    function ft_gallery_add_cats_to_attachments() {
        register_taxonomy_for_object_type('ft_gallery_cats', 'attachment');
        // add_post_type_support('attachment', 'ft_gallery_cats');
    }

    /**
     * FT Gallery Rename Submenu Name
     * Renames the submenu item in the Wordpress dashboard's menu
     *
     * @param $safe_text
     * @param $text
     * @return string
     * @since 1.0.0
     */
    function ft_gallery_rename_submenu_name($safe_text, $text) {
        if ('Galleries' !== $text) {
            return $safe_text;
        }
        // We are on the main menu item now. The filter is not needed anymore.
        remove_filter('attribute_escape', array($this, 'ft_gallery_rename_submenu_name'));

        return 'FT Gallery';
    }

    /**
     * FT Gallery Updated Messages
     * Updates the messages in the admin area so they match plugin
     *
     * @param $messages
     * @return mixed
     * @since 1.0.0
     */
    public function ft_gallery_updated_messages($messages) {
        global $post, $post_ID;
        $messages['ft_gallery'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Gallery updated.', 'feed-them-gallery'),
            2 => __('Custom field updated.', 'feed-them-gallery'),
            3 => __('Custom field deleted.', 'feed-them-gallery'),
            4 => __('Gallery updated.', 'feed-them-gallery'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf(__('Response restored to revision from %s', 'feed-them-gallery'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
            6 => __('Gallery created.', 'feed-them-gallery'),
            7 => __('Gallery saved.', 'feed-them-gallery'),
            8 => __('Gallery submitted.', 'feed-them-gallery'),
            9 => __('Gallery scheduled for: <strong>%1$s</strong>.', 'feed-them-gallery'),
            // translators: Publish box date format, see http://php.net/date
            // date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
            10 => __('Gallery draft updated.', 'feed-them-gallery'),
        );

        return $messages;
    }

    /**
     * FT Gallery Set Custom Edit Columns
     *
     * Sets the custom admin columns for gallery list page
     *
     * @param $columns
     * @return array
     * @since 1.0.0
     */
    function ft_gallery_set_custom_edit_columns($columns) {

        $new = array();

        foreach ($columns as $key => $value) {

            if ($key == 'title') {  // when we find the date column
                $new[$key] = $value;
                $new['gallery_thumb'] = __('', 'feed-them-gallery');  // put the tags column before it
                $new['gallery_shortcode'] = __('Gallery Shortcode', 'feed-them-gallery');

                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                    $text = __('Gallery ZIP', 'feed-them-gallery');
                } else {
                    $text = '';
                }

                $new['gallery_zip'] = $text;

            } else {
                $new[$key] = $value;
            }
        }

        return $new;
    }

    /**
     * FT Gallery Count Post Images
     * Return a count of images for our gallery list column.
     *
     * @return mixed
     * @since 1.0.0
     */
    public
    function ft_gallery_count_post_images($post_id) {
        $attachments = get_children(array(
            'post_parent' => $post_id,
            'post_mime_type' => 'image'
        ));

        $count = count($attachments);

        return $count;
    }


    /**
     * FT Galley Custom Edit Column
     * Put info in matching coloumns we set
     *
     * @param $column
     * @param $post_id
     * @since 1.0.0
     */
    function ft_gallery_custom_edit_column($column, $post_id) {
        switch ($column) {
            case 'gallery_thumb' :
                $display_gallery = new Display_Gallery();
                $image_list = $display_gallery->ft_gallery_get_media_rest($post_id, '1');

                if ($image_list) {
                    echo '<a href="' . get_edit_post_link($post_id) . '"><img src="' . $image_list[0]['media_details']['sizes']['thumbnail']['source_url'] . '" alt="" />';
                    echo $this->ft_gallery_count_post_images($post_id) . ' '.__('Images', 'feed-them-gallery').'</a>';
                }
                break;
            // display a thumbnail photo
            case 'gallery_shortcode' :
                echo '<input value="[feed-them-gallery id=' . $post_id . ']" onclick="this.select()"/>';
                break;

            case 'gallery_zip' :

                //Add Premium Coloumns
                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                    $newest_zip = get_post_meta($post_id, 'ft_gallery_newest_zip_id', true);

                    if ($newest_zip) {
                        $newest_zip_check = $this->ft_gallery_zip_exists_check($newest_zip);

                        if ($newest_zip_check == 'true') {
                            $ft_gallery_get_attachment_info = $this->ft_gallery_get_attachment_info($newest_zip);
                            echo '<a class="ft_gallery_download_button_icon" href="' . $ft_gallery_get_attachment_info['download_url'] . '"><span class="dashicons dashicons-download"></span></a>';
                        } else {
                            echo _e('No ZIP created.', 'feed-them-gallery');
                        }
                    } else {
                        echo _e('No ZIP created.', 'feed-them-gallery');
                    }

                }
                break;

        }
    }

    /**
     * FT Gallery Set Button Text
     * Set Edit Post buttons for Galleries custom post type
     *
     * @param $translated_text
     * @param $text
     * @param $domain
     * @return mixed
     * @since 1.0.0
     */
    public
    function ft_gallery_set_button_text($translated_text, $text, $domain) {
        $post_id = isset($_GET['post']) ? $_GET['post'] : '';
        $custom_post_type = get_post_type($post_id);
        if (!empty($post_id) && $custom_post_type == 'ft_gallery_responses') {
            switch ($translated_text) {
                case 'Publish' :
                    $translated_text = __('Save Gallery', 'feed-them-gallery');
                    break;
                case 'Update' :
                    $translated_text = __('Update Gallery', 'feed-them-gallery');
                    break;
                case 'Save Draft' :
                    $translated_text = __('Save Gallery Draft', 'feed-them-gallery');
                    break;
                case 'Edit Payment' :
                    $translated_text = __('Edit Gallery', 'feed-them-gallery');
                    break;
            }
        }

        return $translated_text;
    }

    /**
     * FT Gallery Scripts
     *
     * Create Gallery custom post type
     *
     * @since 1.0.0
     */
    public
    function ft_gallery_scripts() {

        global $id, $post;

        // Get current screen.
        $current_screen = get_current_screen();

        if (is_admin() && $current_screen->post_type == 'ft_gallery' && $current_screen->base == 'post') {

            // Set the post_id for localization.
            $post_id = isset($post->ID) ? $post->ID : (int)$id;

            // Image Uploader
            wp_enqueue_media(array(
                'post' => $post_id,
            ));
            add_filter('plupload_init', array($this, 'plupload_init'));
            // Updates the attachments when saving
            //  add_filter( 'wp_insert_post_data', array( $this, 'ft_gallery_sort_images_meta_save' ), 99, 2 );

            wp_enqueue_style('ft-gallery-feeds', plugins_url('feed-them-gallery/includes/feeds/css/styles.css'));
            wp_enqueue_style('ft-gallery-popup', plugins_url('feed-them-gallery/includes/feeds/css/magnific-popup.css'));
            wp_enqueue_script('ft-gallery-popup-js', plugins_url('feed-them-gallery/includes/feeds/js/magnific-popup.js'));
            wp_register_style('side_sup_settings_css', plugins_url('feed-them-gallery/admin/css/metabox.css'));
            wp_enqueue_style('side_sup_settings_css');

            wp_register_script('jquery-nested-sortable', plugins_url('feed-them-gallery/admin/js/jquery.mjs.nestedSortable.js'), array('jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-sortable, '));
            wp_enqueue_script('jquery-nested-sortable');

            wp_enqueue_style('ft-gallery-admin-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');

            wp_enqueue_script('jquery-ui-progressbar');  // the progress bar
            //  wp_register_script('Side-Sup-Sidebar-Builder', plugins_url('feed-them-gallery/admin/js/metabox.js'), 'jquery-ui-progressbar', 1.0, true);
            wp_register_script('ft-gallery-metabox', plugins_url('feed-them-gallery/admin/js/metabox.js'));
            wp_enqueue_script('ft-gallery-metabox');

            // Localize JavaScript:
            wp_localize_script('ft-gallery-metabox', 'dgd_strings', array(
                'panel' => array(
                    'title' => __('Upload Images for Feed Them Gallery'),
                    'button' => __('Save and Close Popup')
                )
            ));

            // Add buttons that appears at the bottom of pages to publish, update or go to top of page
            wp_enqueue_script('updatefrombottom-admin-script', plugins_url('feed-them-gallery/includes/js/update-from-bottom.js'), array('jquery'));

            # Translatable trings
            $js_data = array(
                'update' => __('Update', 'feed-them-gallery'),
                'publish' => __('Publish', 'feed-them-gallery'),
                'publishing' => __('Publishing...', 'feed-them-gallery'),
                'updating' => __('Updating...', 'feed-them-gallery'),
                'totop' => __('To top', 'feed-them-gallery'),
            );
            # Localize strings to javascript
            wp_localize_script('updatefrombottom-admin-script', 'updatefrombottomParams', $js_data);

        } else {
            return;
        }
    }

    /**
     * FT Gallery Sort Images Meta Save
     *
     * Sort images for meta save
     *
     * @param $post_data
     * @return mixed
     * @since 1.0.0
     */
    public
    function ft_gallery_sort_images_meta_save($post_data) {

        $attach_ID = $this->ft_gallery_get_attachment_info($post_data['ID']);

        foreach ($attach_ID as $img_index => $img_id) {
            $a = array(
                'ID' => $img_id,
                'menu_order' => $img_index
            );
            //  wp_update_post( $a );
        }
        error_log(print_r($post_data, true));

        return $post_data;
    }

    /**
     * Add Gallery Meta Boxes
     *
     * Add metaboxes to the gallery
     *
     * @since 1.0.0
     */
    public
    function ft_gallery_add_metaboxes() {
        global $post;
        // Check we are using Feed Them Gallery Custom Post type
        if ('ft_gallery' != $post->post_type) {
            return;
        }

        //Image Uploader and Gallery area in admin
        add_meta_box('ft-galleries-upload-mb', __('Feed Them Gallery Settings', 'feed-them-gallery'), array($this, 'ft_gallery_uploader_meta_box'), 'ft_gallery', 'normal', 'high', null);

        //Link Settings Meta Box
        add_meta_box('ft-galleries-shortcode-side-mb', __('Feed Them Gallery Shortcode', 'feed-them-gallery'), array($this, 'ft_gallery_shortcode_meta_box'), 'ft_gallery', 'side', 'high', null);
    }

    /**
     * FT Gallery Format Bytes
     *
     * Creates a human readable size for return
     * @param $bytes
     * @param int $precision
     * @return float
     * @since 1.0.0
     */
    public
    function ft_gallery_format_bytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision);
    }


    /**
     * FT Gallery Uploader Meta Box
     *
     * Uploading functionality trigger. (Most of the code comes from media.php and handlers.js)
     *
     * @param $object
     * @since 1.0.0
     */
    public
    function ft_gallery_uploader_meta_box($object) {
        wp_nonce_field(basename(__FILE__), 'ft-galleries-settings-meta-box-nonce'); ?>

        <?php
        echo '<div class="ft-gallery-popup-form" style="display:none">';

        echo '<label>' . __('Title of image', $this->plugin_locale) . '</label>';
        echo '<input value="" class="fts-gallery-title" />';
        echo '<label>' . __('Alt text for image', $this->plugin_locale) . '</label>';
        echo '<input value="" class="fts-gallery-alttext" />';
        echo '<label>' . __('Description of image', $this->plugin_locale) . '</label>';
        echo '<textarea class="fts-gallery-description"></textarea><br/><div class="ft-submit-wrap"><a class="ft-gallery-edit-img-ajax button button-primary button-large" id="ft-gallery-edit-img-ajax" href="javascript:;" data-nonce="' . wp_create_nonce('ft_gallery_edit_image_nonce') . '">' . __('Save', $this->plugin_locale) . '</a></div>';
        echo '</div><div class="clear"></div>';

        $gallery_id = isset($_GET['post']) ? $_GET['post'] : ''; ?>
        <div class="ft-gallery-settings-tabs-meta-wrap">
            <div class="tabs" id="tabs">
                <div class="tabs-menu-wrap" id="tabs-menu">
                    <label for="tab1" class="tab1 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_images') {
                        echo 'tab-active';
                    } elseif (!isset($_GET['tab'])) {
                        echo 'tab-active';
                    } ?>" id="ft_images">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('Images', 'feed-them-gallery') ?></span>
                    </label>

                    <label for="tab2" class="tab2 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_layout') {
                        echo 'tab-active';
                    } ?>" id="ft_layout">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('Layout', 'feed-them-gallery') ?></span>
                    </label>

                    <label for="tab3" class="tab3 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_colors') {
                        echo ' tab-active';
                    } ?>" id="ft_colors">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('Colors', 'feed-them-gallery') ?></span>

                    </label>

                    <label for="tab4" class="tab4 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_global') {
                        echo ' tab-active';
                    } ?>" id="ft_global" style="display: none;">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('Global', 'feed-them-gallery') ?></span>
                    </label>

                    <label for="tab6" class="tab6 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_zip_gallery') {
                        echo ' tab-active';
                    } ?>" id="ft_zip_gallery">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('Zips', 'feed-them-gallery') ?></span>
                    </label>

                    <label for="tab5" class="tab5 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_woo_commerce') {
                        echo ' tab-active';
                    } ?>" id="ft_woo_commerce">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('WooCommerce', 'feed-them-gallery') ?></span>

                    </label>

                    <label for="tab7" class="tab7 tabbed <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_watermark') {
                        echo ' tab-active';
                    } ?>" id="ft_watermark">
                        <div class="ft_icon"></div>
                        <span class="das-text"><?php _e('Watermark', 'feed-them-gallery') ?></span>

                    </label>
                </div>

                <div id="ftg-tab-content1" class="tab-content fts-hide-me <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_images' || !isset($_GET['tab'])) {
                    echo ' pane-active';
                } ?>">
                    <div class="ftg-section">

                        <div id="uploadContainer" style="margin-top: 10px;">

                            <!-- Current image -->
                            <div id="current-uploaded-image" class="<?php echo has_post_thumbnail() ? 'open' : 'closed'; ?>">
                                <?php if (has_post_thumbnail()): ?><?php the_post_thumbnail('ft_gallery_thumb'); ?><?php else: ?>
                                    <img class="attachment-full" src="" />
                                <?php endif; ?>



                                <?php $thumbnail_id = get_post_thumbnail_id($this->parent_post_id); ?>
                                <?php $ajax_nonce = wp_create_nonce("set_post_thumbnail-$this->parent_post_id"); ?>

                            </div>

                            <?php
                            // adjust values here
                            $id = "img1"; // this will be the name of form field. Image url(s) will be submitted in $_POST using this key. So if $id == “img1" then $_POST[“img1"] will have all the image urls
                            $svalue = ""; // this will be initial value of the above form field. Image urls.
                            $multiple = true; // allow multiple files upload
                            $width = null; // If you want to automatically resize all uploaded images then provide width here (in pixels)
                            $height = null; // If you want to automatically resize all uploaded images then provide height here (in pixels)

                            if (!isset($_GET['post'])) {
                                global $post;
                                //Getting the next post id by seeing if an autodraft has been made in our custom post type.
                                $create_next_args = array(
                                    'post_type' => 'ft_gallery',
                                    'posts_per_page' => 1,
                                    'post_status' => 'auto-draft',
                                    'ignore_sticky_posts' => 1,
                                    'orderby' => 'date',
                                    'order' => 'DSC',
                                );
                                $create_next_query = new \WP_Query($create_next_args);


                                if ($create_next_query->have_posts()) : while ($create_next_query->have_posts()) : $create_next_query->the_post();
                                    $edit_link_url = $post->ID;
                                endwhile; endif;

                                // for testing
                                // echo $edit_link_url;
                                $this->parent_post_id = $edit_link_url;
                            }


                            ?>

                            <!-- Uploader section -->
                            <div id="uploaderSection">
                                <div id="plupload-upload-ui" class="hide-if-no-js drag-drop">
                                    <div id="drag-drop-area">
                                        <div class="drag-drop-inside">
                                            <p class="drag-drop-info"><?php _e('Drop images here'); ?></p>
                                            <p><?php _ex('or', 'Uploader: Drop Images here - or - Select Images'); ?></p>
                                            <div class="drag-drop-buttons">
                                                <input id="<?php echo $id; ?>plupload-browse-button" type="button" value="<?php esc_attr_e('Select Images'); ?>" class="button"/>

                                            </div>
                                            <div class="drag-drop-buttons">
                                                <?php if ($this->wordpress_version >= 35): ?>
                                                    <!--<a href="#" id="dgd_library_button" class="button insert-media add_media" data-editor="content" title="Add Media">-->
                                                    <a href="javascript:;" id="dgd_library_button" class="button" title="Add Media">
                                                        <span class="wp-media-buttons-icon"></span><?php _e('Media Library', $this->plugin_locale); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php bloginfo('wpurl'); ?>/wp-admin/media-upload.php?post_id=<?php echo $this->parent_post_id; ?>&amp;tab=library&amp;=&amp;post_mime_type=image&amp;TB_iframe=1&amp;width=640&amp;height=353" class="thickbox add_media button-secondary" id="content-browse_library" title="Browse Media Library" onclick="return false;">
                                                        <?php _e('Media Library', 'feed-them-gallery'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="upload-max-size"><?php $bytes = wp_max_upload_size();
                                echo __('Maximum upload file size', $this->plugin_locale) . ': ' . $this->ft_gallery_format_bytes($bytes, $precision = 2) . ' MB.'; ?>
                            </div>

                            <?php
                            $display_gallery = new Display_Gallery();


                            // $image_list = $display_gallery->ft_gallery_get_media_rest($this->parent_post_id, '100');
                            $args = array(
                                'post_parent' => $object->ID,
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'posts_per_page' => -1,
                                'orderby' => 'menu_order',
                                'order' => 'asc',
                                'exclude' => 0 // Exclude featured thumbnail
                            );
                            $image_list = get_posts($args);


                            if (is_array($image_list) && $object->ID == true && isset($image_list[0])) { ?>
                                <div class="ftg-number-of-images-wrap"><?php echo $this->ft_gallery_count_post_images($object->ID) ?><?php _e(' Images', 'feed-them-gallery'); ?></div>
                            <?php } ?>

                            <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>"/>

                            <div class="plupload-upload-uic hide-if-no-js <?php if ($multiple): ?>plupload-upload-uic-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-upload-ui">
                                <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
                                <?php if ($width && $height): ?>
                                    <span class="plupload-resize"></span>
                                    <span class="plupload-width" id="plupload-width<?php echo $width; ?>"></span>
                                    <span class="plupload-height" id="plupload-height<?php echo $height; ?>"></span>
                                <?php endif; ?>
                                <div class="filelist"></div>
                            </div>
                        </div>

                        <?php


                        //Happens in JS file
                        $this->ft_gallery_tab_notice_html(); ?>

                        <script>
                            jQuery('.metabox_submit').click(function (e) {
                                e.preventDefault();
                                //  jQuery('#publish').click();
                                jQuery('#post').click();
                            });


                            jQuery(document).ready(function () {
                                jQuery('.gallery-edit-button-question-one').click(function () {
                                    jQuery('.gallery-edit-question-download-gallery').toggle();
                                    jQuery('.gallery-edit-question-digital-gallery-product, .gallery-edit-question-individual-image-product').hide();
                                });

                                jQuery('.gallery-edit-button-question-two').click(function () {
                                    jQuery('.gallery-edit-question-digital-gallery-product').toggle();
                                    jQuery('.gallery-edit-question-download-gallery, .gallery-edit-question-individual-image-product').hide();
                                });

                                jQuery('.gallery-edit-button-question-three').click(function () {
                                    jQuery('.gallery-edit-question-individual-image-product').toggle();
                                    jQuery('.gallery-edit-question-download-gallery, .gallery-edit-question-digital-gallery-product').hide();
                                });
                            });

                        </script>

                        <?php
                        // The size of the image in the popup
                        $image_size_name = get_post_meta($object->ID, 'ft_gallery_images_sizes_popup', true);


                        // $images_count = count( $attachments );

                        ?>
                        <input type="submit" class="metabox_submit" value="Submit" style="display: none;"/>

                        <?php // don't show these buttons until the page has been published with some photos in it
                        if (isset($image_list[0])) { ?>
                            <div class="ft-gallery-options-buttons-wrap">
                                <?php if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) { ?>
                                    <div class="gallery-edit-button-wrap">
                                        <button type="button" class="button" id="fts-gallery-checkAll"><?php _e('Select All', $this->plugin_locale); ?></button>
                                    </div>
                                <?php } ?>
                                <div class="gallery-edit-button-wrap">
                                    <button <?php if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                        echo 'disabled ';
                                    } ?>type="button" class="ft-gallery-download-gallery ft_gallery_download_button_icon button button-primary button-larg" onclick="ft_gallery_create_zip('<?php echo $this->parent_post_id; ?>', 'yes','false')"><?php _e('Zip Gallery & Download', 'feed-them-gallery') ?></button>
                                    <a class="gallery-edit-button-question-one" href="javascript:;" rel="gallery-edit-question-download-gallery">?</a>
                                </div>
                                <?php // if (is_plugin_active('woocommerce/woocommerce.php')) {
                                //Selected Image Product
                                $selected_zip_product = get_post_meta($gallery_id, 'ft_gallery_zip_to_woo_model_prod', true);
                                //Selected ZIP Product
                                $selected_image_product = get_post_meta($gallery_id, 'ft_gallery_image_to_woo_model_prod', true);
                                ?>
                                <div class="gallery-edit-button-wrap">
                                    <button <?php if (empty($selected_zip_product)) {
                                        echo 'disabled ';
                                    } ?>type="button" class="ft-gallery-zip-gallery ft_gallery_download_button_icon button button-primary button-larg" onclick="ft_gallery_create_zip('<?php echo $object->ID; ?>', 'no', 'yes', 'no')"><?php _e('Create Digital Gallery Product', 'feed-them-gallery') ?></button>
                                    <a class="gallery-edit-button-question-two" href="javascript:;" rel="gallery-edit-question-digital-gallery-product">?</a>
                                </div>
                                <div class="gallery-edit-button-wrap">
                                    <button <?php if (empty($selected_image_product)) {
                                        echo 'disabled ';
                                    } ?>type="button" class="ft-gallery-create-woo ft_gallery_download_button_icon button button-primary button-larg" onclick="ft_gallery_image_to_woo('<?php echo $this->parent_post_id; ?>')"><?php _e('Create individual Image Product(s)', 'feed-them-gallery') ?></button>
                                    <a class="gallery-edit-button-question-three" href="javascript:;" rel="gallery-edit-question-individual-image-product">?</a>
                                </div>
                                <?php // } ?>
                            </div>
                            <div class="clear"></div>
                        <?php } ?>
                        <div class="gallery-edit-question-message gallery-edit-question-download-gallery" style="display: none;">
                            <h3><?php _e('Zip Gallery and Download'); ?></h3>
                            <?php
                            echo sprintf(__('This button will create a zip of all the full size images in this gallery on the %1$sZIPs tab%2$s and then download a zip onto your computer. If you would like to just download a ZIP you have already made and NOT create a new ZIP of the gallery you may do so from the %1$sZIPs tab%2$s.', 'feed-them-gallery'),
                                '<a href="' . esc_url('post.php?post=' . $this->parent_post_id . '&action=edit&tab=ft_zip_gallery') . '" >',
                                '</a>'
                            );

                            if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $this->ft_gallery_tab_premium_msg();
                            } ?>
                        </div>
                        <?php //if (is_plugin_active('woocommerce/woocommerce.php')) { ?>
                        <div class="gallery-edit-question-message gallery-edit-question-digital-gallery-product" style="display: none;">
                            <h3><?php _e('Create Digital Gallery Zip and Turn into a Product'); ?></h3>
                            <?php echo sprintf(__('This button will create a zip on the  %1$sZIPs tab%2$s of all the full size images in this gallery and then create a WooCommerce Product out of that ZIP. You must have a "ZIP Model Product" selected on the %3$sWoocommerce tab%4$s for this to work.', 'feed-them-gallery'),
                                '<a href="' . esc_url('post.php?post=' . $this->parent_post_id . '&action=edit&tab=ft_zip_gallery') . '" >',
                                '</a>',
                                '<a href="' . esc_url('post.php?post=' . $this->parent_post_id . '&action=edit&tab=ft_woo_commerce') . '" >',
                                '</a>'
                            );
                            if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $this->ft_gallery_tab_premium_msg();
                            } ?>
                        </div>
                        <div class="gallery-edit-question-message gallery-edit-question-individual-image-product" style="display: none;">
                            <h3><?php _e('Create Products from Individual Images'); ?></h3>
                            <?php
                            echo sprintf(__('This button will create a WooCommerce Product for each of the images selected below. 1 image creates 1 WooCommerce product. You must have a "Global Model Product" selected on the %1$sWoocommerce tab%2$s for this to work.', 'feed-them-gallery'),
                                '<a href="' . esc_url('post.php?post=' . $this->parent_post_id . '&action=edit&tab=ft_woo_commerce') . '" >',
                                '</a>'
                            );
                            if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $this->ft_gallery_tab_premium_msg();
                            } ?>
                        </div>
                        <?php //} ?>
                        <div class="clear"></div>

                        <ul class="plupload-thumbs <?php if ($multiple): ?>plupload-thumbs-multiple<?php endif; ?>" id="<?php echo $id; ?>plupload-thumbs" data-post-id="<?php echo $object->ID; ?>">
                            <?php
                            $show_title = get_post_meta($object->ID, 'ft_gallery_show_title', true); ?><?php

                            //Display Images Gallery


                            $size = 'thumbnail';

                            $attr = array(
                                'class' => "attachment-$size wp-post-image",
                            );
                            //   && isset($image_list[0])
                            if (is_array($image_list) && isset($image_list[0])) {

                                $image_output = '';

                                foreach ($image_list as $key => $image) {
                                    // echo '<pre>';
                                    // print_r($image);
                                    // echo '</pre>';

                                    $times = $image->post_date;
                                    $image = wp_prepare_attachment_for_js($image->ID);

                                    // echo '<pre>';
                                    //   print_r($image);
                                    // echo '</pre>';
                                    $fts_final_date = $display_gallery->ft_gallery_custom_date($times, 'wp_gallery');
                                    $instagram_date = $fts_final_date;


                                    // The size of the image in the popup
                                    $image_size_name = get_post_meta($object->ID, 'ft_gallery_images_sizes_popup', true);
                                    // this is the image size in written format,ie* thumbnail, medium, large etc.
                                    $item_popup = explode(" ", $image_size_name);
                                    $item_final_popup = wp_get_attachment_image_src($attachment_id = $image['id'], $item_popup[0], false);


                                    $image_source_large = wp_get_attachment_image_src($attachment_id = $image['id'], 'large', false);
                                    $image_source_medium_large = wp_get_attachment_image_src($attachment_id = $image['id'], 'medium_large', false);
                                    $image_source_medium = wp_get_attachment_image_src($attachment_id = $image['id'], 'medium', false);
                                    $image_source_thumb = wp_get_attachment_image_src($attachment_id = $image['id'], 'thumbnail', false);


                                    if (isset($image_size_name) && $image_size_name !== 'Choose an option') {
                                        $image_source_popup = $item_final_popup[0];
                                    } elseif (isset($image_source_large)) {
                                        $image_source_popup = $image_source_large[0];
                                    } elseif (isset($image_source_medium_large)) {
                                        $image_source_popup = $image_source_medium_large[0];
                                    } elseif (isset($image_source_medium)) {
                                        $image_source_popup = $image_source_medium[0];
                                    } else {
                                        $image_source_popup = $image_source_thumb[0];
                                    }

                                    $next_img = isset($image_list[$key + 1]) ? $image_list[$key + 1] : $image_list[0];
                                    $prev_img = isset($image_list[$key - 1]) ? $image_list[$key - 1] : $image_list[count($image_list) - 1];


                                    //  echo '<pre>';
                                    //  echo 'prev:'.$prev_img->ID;
                                    //  echo 'next:'.$next_img->ID;
                                    // echo '</pre>';


                                    $meta_box = '<li class="thumb" id="list_item_' . $image['id'] . '" data-image-id="' . $image['id'] . '" data-menu-order="' . $image['menuOrder'] . '">';

                                    //Zip to WooCommerce
                                    if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {

                                        $gallery_to_woo = new Gallery_to_Woocommerce();

                                        //Check custom post meta for woo product field
                                        $image_post_meta = get_post_meta($image['id'], 'ft_gallery_woo_prod', true);

                                        //$this->ft_gallery_get_attachment_info($object->ID);
                                        $meta_box .= '<div class="ft-gallery-woo-btns-wrap-for-popup">';
                                        //If Image already has product meta check the product still exists
                                        if ($image_post_meta == true) {
                                            $product_exist = $gallery_to_woo->ft_gallery_create_woo_prod_exists_check($image_post_meta);
                                            if ($product_exist) {
                                                $meta_box .= '<div class="ft-gallery-file-delete ft-gallery-file-zip-to-woo"><a class="ft_gallery_create_woo_prod_button" target="_blank" href="' . get_edit_post_link($image_post_meta) . '" ">' . __('Edit product', 'feed-them-gallery') . '</a></div>';
                                            }
                                        }
                                        $meta_box .= '</div>';
                                        //Add In Later Version
                                        /* else{
                                             echo '<div class="ft-gallery-file-delete ft-gallery-file-zip-to-woo"><a class="ft_gallery_create_woo_prod_button" onclick="ft_gallery_image_to_woo(\'zip\',\'' . $zip_name . '\',\'' . $abs_file_url . '\')">Create product</a></div>';
                                         }*/
                                    }

                                    //  echo '<pre>';
                                    //  print_r($image['sizes']['ft_gallery_thumb']);
                                    //  echo '</pre>';

                                    if(isset($image['sizes']['ft_gallery_thumb'])){
                                        $image_url = wp_get_attachment_image_src($attachment_id = $image['id'], 'ft_gallery_thumb', false);
                                        // print_r('proper size<br/>');
                                    }
                                    else {
                                        $image_url = wp_get_attachment_image_src($attachment_id = $image['id'], 'thumbnail', false);
                                        // print_r(' not set<br/>');
                                    }

                                    $ft_custom_thumb = $image_url[0];
                                    //  $meta_box .= '<a href="' . $image['media_details']['sizes']['full']['source_url'] . '" rel="gallery-' . $image['id'] . '" class="ft-gallery-edit-img-popup">';
                                    $meta_box .= '<img src="' . $ft_custom_thumb . '"/>';
                                    //  $meta_box .= '</a>';
                                    $meta_box .= '<div class="ft-gallery-edit-thumb-btn"><a title="Edit this image." class="ft-gallery-edit-img-popup" data-id="' . $image['id'] . '" data-nonce="' . wp_create_nonce('ft_gallery_edit_image_nonce') . '" href="' . $image_source_popup . '"></a></div>';

                                    if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                        $meta_box .= '<div class="ft-gallery-select-thumbn"><label class="ft-gallery-myCheckbox"><input type="checkbox" class=“ft-gallery-img-checkbox” rel="' . $image['id'] . '" name="image-' . $image['id'] . '" id="image-' . $image['id'] . '"/><span></span></label></div>';
                                    }

                                    $meta_box .= '<div class="ft-gallery-remove-thumb-btn"><a title="Remove Image from this Gallery" class="ft-gallery-remove-img-ajax" data-ft-gallery-img-remove="true" data-id="' . $image['id'] . '" data-nonce="' . wp_create_nonce('ft_gallery_update_image_nonce') . '" href="javascript:;"></a></div>';
                                    $meta_box .= '<div class="ft-gallery-delete-thumb-btn"><a title="Delete Image Completely" class="ft-gallery-force-delete-img-ajax" data-id="' . $image['id'] . '" data-nonce="' . wp_create_nonce('ft_gallery_delete_image_nonce') . '" href="javascript:;"></a></div> <div class="clear"></div>';
                                    if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {

                                        //If Image already has product meta check the product still exists
                                        if (!empty($image_post_meta)) {
                                            $product_exist = $gallery_to_woo->ft_gallery_create_woo_prod_exists_check($image_post_meta);
                                            if ($product_exist) {
                                                $meta_box .= '<div class="ft-gallery-woo-edit-thumb-btn"><a class="ft_gallery_create_woo_prod_button" target="_blank" href="' . get_edit_post_link($image_post_meta) . '" "></a></div>';
                                            } // add empty div so we don't get a undefined in popup
                                        }
                                    }

                                    $meta_box .= '<div class="ft-image-id-for-popup"><p><strong>' . __('Uploaded:', 'feed-them-gallery') . '</strong> ' . $instagram_date . '</p><br/><input value="' . $image['id'] . '" class="fts-gallery-id" type="text" data-nonce="' . wp_create_nonce('ft_gallery_edit_image_nonce') . '"  /><input value="' . $next_img->ID . '" class="fts-gallery-id fts-next-image" type="text" data-nonce="' . wp_create_nonce('ft_gallery_edit_image_nonce') . '"  /><input value="' . $prev_img->ID . '" class="fts-gallery-id fts-prev-image" type="text" data-nonce="' . wp_create_nonce('ft_gallery_edit_image_nonce') . '"  /></div>';
                                    $meta_box .= '</li>';

                                    echo $meta_box;
                                }
                            } //Error or Empty!
                            else {

                            } ?>
                        </ul>
                        <div class="clear"></div>
                        <?php if (!isset($image_list[0])) {
                            ?>
                            <style type="text/css">
                                .slickdocit-videowrapper {
                                    max-width:100%;
                                    display: none;
                                    margin-bottom: 15px;
                                }
                                .slickdocit-fluidMedia {
                                    position: relative;
                                    padding-bottom: 53.5%; /* proportion value to aspect ratio 16:9 (9 / 16 = 0.5625 or 56.25%) */
                                    padding-top: 30px;
                                    height: 0;
                                    overflow: hidden;
                                }
                                #slickdocit-show-video, #slickdocit-hide-video {
                                    background: #FFFF;
                                    display: inline-block;
                                    border-radius: 50px;
                                    padding: 10px 15px 10px 16px;
                                    margin-bottom: 15px;
                                    cursor: pointer;
                                    font-size: 13px;
                                    float:right;
                                }
                                #slickdocit-show-video:hover, #slickdocit-hide-video {
                                    opacity:.8;
                                }
                                #slickdocit-hide-video {
                                    display:none;
                                }
                                .slickdocit-fluidMedia iframe {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                }
                                .slickdocit-play:before {
                                    font-family: FontAwesomeSlick;
                                    content: "\f04b ";
                                    display: inline-block !important;
                                    margin-left: 7px !important;
                                }
                            </style>
                            <div class="gallery-edit-question-message gallery-edit-question-download-gallery gallery-quick-guide-getting-started">
                                <div class="slickdocit-videowrapper">
                                    <div class="slickdocit-fluidMedia">
                                        <iframe id="slickdocit-iframe" src="https://www.youtube.com/embed/Fa2mjmFAGZQ?rel=0" data-autoplay-src="https://www.youtube.com/embed/Fa2mjmFAGZQ?rel=0&autoplay=1" frameborder="0" allowscriptaccess="always" allowfullscreen=""> </iframe>
                                    </div></div>
                                <div id="slickdocit-show-video" class="slickdocit-show-video"><?php _e('View Quick Setup Video', 'feed-them-gallery'); ?><span class="slickdocit-play"></span> </div>
                                <div id="slickdocit-hide-video" class="ftg-close-vid"><?php _e('Close Video', 'feed-them-gallery'); ?><span class="slickdocit-play"></div>
                                <script>
                                    jQuery( ".slickdocit-show-video" ).click(function() {
                                        var videoURL = jQuery("#slickdocit-iframe");
                                        videoURL.attr("src", videoURL.data("autoplay-src") );
                                        jQuery( ".slickdocit-videowrapper" ).slideDown();
                                        jQuery('.slickdocit-show-video').hide();
                                        jQuery('.ftg-close-vid').show();
                                    });
                                    jQuery( ".ftg-close-vid" ).click(function() {
                                        var videoURL = jQuery("#slickdocit-iframe");
                                        jQuery( ".slickdocit-videowrapper" ).slideUp();
                                        jQuery('.ftg-close-vid').hide();
                                        //Then assign the src to null, this then stops the video been playing
                                        jQuery('.slickdocit-show-video').show();
                                        videoURL.attr("src", '');
                                    });
                                </script>
                                <h3><?php _e('Quick Guide to Getting Started', 'feed-them-gallery'); ?></h3>
                                <p><?php
                                    echo sprintf(__('Please look over the options on the %1$sSettings%2$s page before creating your first gallery.%3$s1. Enter a title for your gallery at the top of the page in the "Enter title here" input. %4$s2. Add images to the gallery and sort them in the order you want. %4$s3. Publish the gallery by clicking the blue "Publish" button. %4$s4. Now you can edit your images title, description and more. %5$sView our %6$sImage Gallery Demos%7$s or %8$sFull documentation%9$s for more details.', 'feed-them-gallery'),
                                        '<a href="' . esc_url('edit.php?post_type=ft_gallery&page=ft-gallery-settings-page') . '" >',
                                        '</a>',
                                        '<p/><p>',
                                        '<br/>',
                                        '</p>',
                                        '<a href="' . esc_url('http://feedthemgallery.com/gallery-demo-one/') . '" >',
                                        '</a>',
                                        '<a href="' . esc_url('https://www.slickremix.com/feed-them-gallery/') . '" >',
                                        '</a>'

                                    );
                                    ?>
                            </div>
                            <?php
                        }

                        ?>

                        <div class="clear"></div>
                    </div>

                </div> <!-- #tab-content1 -->

                <div id="ftg-tab-content2" class="tab-content fts-hide-me <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_layout') {
                    echo ' pane-active';
                } ?>">

                    <?php echo $this->ft_gallery_settings_html_form($this->parent_post_id, $this->saved_settings_array['layout'], null); ?>
                    <div class="clear"></div>
                    <div class="ft-gallery-note">
                        <?php
                        echo sprintf(__('Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery'),
                            '<a href="' . esc_url('edit.php?post_type=ft_gallery&page=ft-gallery-settings-page') . '" >',
                            '</a>'
                        );
                        ?>
                    </div>

                </div>

                <div id="ftg-tab-content3" class="tab-content fts-hide-me <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_colors') {
                    echo ' pane-active';
                } ?>">
                    <?php
                    echo $this->ft_gallery_settings_html_form($this->parent_post_id, $this->saved_settings_array['colors'], null); ?>
                    <div class="clear"></div>

                    <div class="ft-gallery-note"> <?php
                        echo sprintf(__('Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery'),
                            '<a href="' . esc_url('edit.php?post_type=ft_gallery&page=ft-gallery-settings-page') . '" >',
                            '</a>'
                        );
                        ?>
                    </div>

                </div>

                <div id="ftg-tab-content6" class="tab-content fts-hide-me <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_zip_gallery') {
                    echo ' pane-active';
                } ?>">
                    <?php

                    //If Premium add Functionality
                    if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                        echo '<div class="ftg-section">' . $this->ft_gallery_tab_premium_msg() . '</div>';
                    }
                    ?>
                    <div class="ftg-section">

                        <div class="fts-title-description-settings-page">
                            <h3><?php _e('Gallery Digital Zip History List', 'feed-them-gallery'); ?></h3>
                        </div><?php
                        //If Premium add Functionality
                        if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                            ?>

                            <ul id="ft-gallery-zip-list" class="ftg-free-list">
                                <li class="ft-gallery-zip zip-list-item-24527">
                                    <div class="ft-gallery-file-name">
                                        <a href="javascript:;" title="Download"><?php _e('Example-Gallery-Name'); ?></a>
                                    </div>
                                    <div class="ft-gallery-file-time"><?php _e('October 14, 2020 - 2:45pm'); ?></div>
                                    <div class="ft-gallery-file-delete">
                                        <a class="ft_gallery_delete_zip_button"><?php _e('Delete'); ?></a>
                                    </div>
                                    <div class="ft-gallery-file-delete ft-gallery-file-zip-to-woo">
                                        <a class="ft_gallery_create_woo_prod_button"><?php _e('Create product'); ?></a>
                                    </div>
                                    <div class="ft-gallery-file-view">
                                        <a class="ft_gallery_view_zip_button"><?php _e('View Contents'); ?></a></div>
                                    <ol class="zipcontents_list"></ol>
                                </li>
                            </ul>
                        <?php }

                        //If Premium add Functionality
                        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                            //Happens in JS file
                            $this->ft_gallery_tab_notice_html();

                            echo $this->zip_gallery_class->ft_gallery_list_zip_files($this->parent_post_id);
                        }
                        ?>

                    </div>
                    <div class="clear"></div>
                </div>

                <div id="ftg-tab-content5" class="tab-content fts-hide-me <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_woo_commerce') {
                    echo ' pane-active';
                } ?>">

                    <?php

                    if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) { ?>
                        <div class="ftg-section">
                            <?php $this->ft_gallery_tab_premium_msg(); ?>
                        </div>
                    <?php } ?>

                    <?php
                    //  echo '<pre>';
                    //  print_r(wp_prepare_attachment_for_js('21529'));
                    //  echo '</pre>';

                    echo $this->ft_gallery_settings_html_form($this->parent_post_id, $this->saved_settings_array['woocommerce'], null); ?>

                    <div class="tab-5-extra-options">

                        <div class="feed-them-gallery-admin-input-wrap ">
                            <div class="feed-them-gallery-admin-input-label"><?php _e('Global Model Product', 'feed-them-gallery'); ?></div>
                            <?php
                            if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                echo $gallery_to_woo_class->ft_gallery_image_to_woo_model_prod_select($this->parent_post_id, 'global');
                            }
                            else {
                                $ftg_prem_not_active = '<select disabled="" class="feed-them-gallery-admin-input"><option value="" selected="selected">Premium Required</option></select>';
                                echo $ftg_prem_not_active;
                            }?>
                            <span class="tab-section-description"><small><?php _e('Select a Product that will be duplicated when creating a WooCommerce products for individual images. 1 image will turn 1 WooCommerce product. Saves time when creating variable product Example: Printable images that have different print sizes, material, etc.', 'feed-them-gallery'); ?></small></span>
                            <span class="tab-section-description"><a href="https://docs.woocommerce.com/document/variable-product/" target="_blank"><small>
                                        <?php
                                        echo sprintf(__('Learn how to create a %1$sVariable product%2$s in WooCommerce.', 'feed-them-gallery'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                        ?>
                                    </small></a> </span>

                        </div>

                        <div class="feed-them-gallery-admin-input-wrap ">

                            <div class="feed-them-gallery-admin-input-label"><?php _e('Smart Image Orientation Model Products', 'feed-them-gallery'); ?></div><br/>

                            <span class="tab-section-description"><small><?php _e('Select Model Products that will be duplicated when creating a WooCommerce products for Landscape Images (Greater width than height), Square Images (Equal width and height), and Portrait Images (Width less than height). 1 image will turn 1 WooCommerce product. You must have the "Use Smart Image Orientation" checked above for this option to work properly.', 'feed-them-gallery'); ?></small></span>
                            <span class="tab-section-description"><a href="https://docs.woocommerce.com/document/variable-product/" target="_blank"><small>
                                        <?php
                                        echo sprintf(__('Learn how to create a %1$sVariable product%2$s in WooCommerce.', 'feed-them-gallery'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                        ?>
                                    </small></a> </span>

                            <p>
                            <div class="feed-them-gallery-admin-input-label"><?php _e('Landscape Image Model Product', 'feed-them-gallery'); ?></div>

                            <?php
                            if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                echo $gallery_to_woo_class->ft_gallery_image_to_woo_model_prod_select($this->parent_post_id, 'landscape');
                            }
                            else {
                                echo $ftg_prem_not_active;
                            }?>
                            </p>
                            <p>
                            <div class="feed-them-gallery-admin-input-label"><?php _e('Square Image Model Product', 'feed-them-gallery'); ?></div>
                            <?php
                            if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                echo $gallery_to_woo_class->ft_gallery_image_to_woo_model_prod_select($this->parent_post_id, 'square');
                            }
                            else {
                                echo $ftg_prem_not_active;
                            }
                            ?>
                            </p>
                            <p>
                            <div class="feed-them-gallery-admin-input-label"><?php _e('Portrait Image Model Product', 'feed-them-gallery'); ?></div>
                            <?php
                            if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                echo $gallery_to_woo_class->ft_gallery_image_to_woo_model_prod_select($this->parent_post_id, 'portrait');
                            }
                            else {
                                echo $ftg_prem_not_active;
                            }?>
                            </p>
                        </div>

                        <div class="feed-them-gallery-admin-input-wrap ">
                            <div class="feed-them-gallery-admin-input-label"><?php _e('ZIP Model Product', 'feed-them-gallery'); ?></div>
                            <?php
                            if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                echo $gallery_to_woo_class->ft_gallery_zip_to_woo_model_prod_select($this->parent_post_id);
                            }
                            else {
                                echo $ftg_prem_not_active;
                            }?>
                            <span class="tab-section-description"><small><?php _e('Select a Product that will be duplicated when creating a WooCommerce product for Gallery Digital ZIP. (Turns all images in Gallery into a ZIP for a Simple Virtual/Downloadable WooCommerce product.)', 'feed-them-gallery'); ?></small></span>
                            <span class="tab-section-description"><a href="https://docs.woocommerce.com/document/managing-products/#section-5" target="_blank"><small>
                                         <?php
                                         echo sprintf(__('Learn how to create a %1$sSimple product%2$s in WooCommerce.', 'feed-them-gallery'),
                                             '<strong>',
                                             '</strong>'
                                         );
                                         ?>
                                    </small></a> </span>
                            <span class="tab-section-description"><small><?php echo sprintf(__('**NOTE** A Zip Model Product must have the options %1$sVirtual%2$s AND %3$sDownloadable%4$s checked to appear in ZIP Model Product select option above. No Download link is needed in product though as it will be auto-filled in when Feed Them Gallery creates a new ZIP product based on the ZIP\'s location.', 'feed-them-gallery'),
                                        '<a href="' . esc_url('https://docs.woocommerce.com/document/managing-products/#section-14') . '">',
                                        '</a>',
                                        '<a href="' . esc_url('https://docs.woocommerce.com/document/managing-products/#section-15') . '">',
                                        '</a>'
                                    ); ?></small></span>

                        </div>

                        <div class="clear"></div>

                        <div class="ft-gallery-note">
                            <?php
                            echo sprintf(__('Additional Global WooCommerce options available on the %1$sSettings Page%2$s', 'feed-them-gallery'),
                                '<a href="' . esc_url('edit.php?post_type=ft_gallery&page=ft-gallery-settings-page') . '" >',
                                '</a>'
                            );
                            ?>
                        </div>

                    </div>


                </div>

                <div id="ftg-tab-content7" class="tab-content fts-hide-me <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_watermark') {
                    echo ' pane-active';
                } ?>">

                    <?php if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) { ?>
                        <div class="ftg-section">
                            <?php $this->ft_gallery_tab_premium_msg(); ?>
                        </div>
                    <?php }

                    echo $this->ft_gallery_settings_html_form($this->parent_post_id, $this->saved_settings_array['watermark'], null); ?>

                    <div class="clear"></div>

                    <div class="ft-gallery-note">
                        <?php
                        echo sprintf(__('Please %1$screate a ticket%2$s if you are experiencing trouble and one of our team members will be happy to assist you.', 'feed-them-gallery'),
                            '<a href="' . esc_url('https://www.slickremix.com/my-account/#tab-support') . '" target="_blank">',
                            '</a>'
                        );
                        ?>
                    </div>

                </div>
                <div class="clear"></div>

            </div>
        </div>
        <script>
            jQuery(document).ready(function ($) {

                //create hash tag in url for tabs
                //  jQuery('.post-type-ft_gallery').on('click', ".button-large", function () {
                //  var myURL = document.location;
                //  document.location = myURL + "&tab=" + jQuery(this).attr('id');
                //      $("#post").attr("action", "post.php/?post=18240&action=edit&tab=ft_layout");

                //  })

                //create hash tag in url for tabs
                jQuery('.ft-gallery-settings-tabs-meta-wrap #tabs').on('click', "label.tabbed", function () {
                    var myURL = document.location;
                    document.location = myURL + "&tab=" + jQuery(this).attr('id');

                })

                // facebook Super Gallery option
                jQuery('#facebook-custom-gallery').bind('change', function (e) {
                    if (jQuery('#facebook-custom-gallery').val() == 'yes') {
                        jQuery('.fts-super-facebook-options-wrap').show();
                    }
                    else {
                        jQuery('.fts-super-facebook-options-wrap').hide();
                    }
                });

                if (jQuery('#ft_gallery_popup').val() == 'no') {
                    jQuery('.ft-images-sizes-popup').hide();
                    // jQuery('.display-comments-wrap').show();

                }
                //Facebook Display Popup option
                jQuery('#ft_gallery_popup').bind('change', function (e) {
                    if (jQuery('#ft_gallery_popup').val() == 'yes') {
                        jQuery('.ft-images-sizes-popup').show();
                        // jQuery('.display-comments-wrap').show();

                    }
                    else {
                        jQuery('.ft-images-sizes-popup').hide();
                        //  jQuery('.display-comments-wrap').hide();
                    }
                });


                if (jQuery("#ft_gallery_watermark").val() == 'imprint') {
                    jQuery('.ft-watermark-hidden-options').show();
                    jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').hide();
                }


                if (jQuery('#ft_gallery_watermark').val() == 'overlay') {
                    jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').show();
                    jQuery('.ft-watermark-hidden-options').hide();
                }

                // facebook show load more options
                jQuery('#ft_gallery_watermark').bind('change', function (e) {
                    if (jQuery('#ft_gallery_watermark').val() == 'imprint') {

                        jQuery('.ft-watermark-hidden-options').show();
                        jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').hide();
                    }
                    if (jQuery('#ft_gallery_watermark').val() == 'overlay') {
                        jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').show();
                        jQuery('.ft-watermark-hidden-options').hide();
                    }

                });

                // show the duplicate image select box for those who want to duplicate the image before watermarking
                jQuery('#ft_watermark_image_-full').change(function () {
                    this.checked ? jQuery('.ft-watermark-duplicate-image').show() : jQuery('.ft-watermark-duplicate-image').hide();
                });
                //if page is loaded and box is checked we show the select box otherwise it is hidden with CSS
                if (jQuery('input#ft_watermark_image_-full').is(':checked')) {
                    jQuery('.ft-watermark-duplicate-image').show()
                }


                // facebook show load more options
                jQuery('#ft_gallery_load_more_option').bind('change', function (e) {
                    if (jQuery('#ft_gallery_load_more_option').val() == 'yes') {

                        if (jQuery('#facebook-messages-selector').val() !== 'album_videos') {
                            jQuery('.fts-facebook-load-more-options-wrap').show();
                        }
                        jQuery('.fts-facebook-load-more-options2-wrap').show();
                    }

                    else {
                        jQuery('.fts-facebook-load-more-options-wrap, .fts-facebook-load-more-options2-wrap').hide();
                    }
                });


                if (jQuery('#ft_gallery_load_more_option').val() == 'yes') {
                    jQuery('.fts-facebook-load-more-options-wrap, .fts-facebook-load-more-options2-wrap').show();
                    jQuery('.fts-facebook-grid-options-wrap').show();
                }
                if (jQuery('#ft_gallery_grid_option').val() == 'yes') {
                    jQuery('.fts-facebook-grid-options-wrap').show();
                    jQuery(".feed-them-gallery-admin-input-label:contains('Center Facebook Container?')").parent('div').show();
                }


                if (jQuery('#ft_gallery_type').val() == 'post-in-grid' || jQuery('#ft_gallery_type').val() == 'gallery' || jQuery('#ft_gallery_type').val() == 'gallery-collage') {
                    jQuery('.fb-page-grid-option-hide').show();
                    if (jQuery('#ft_gallery_type').val() == 'gallery') {
                        jQuery('#ft_gallery_height').show();
                        jQuery('.fb-page-columns-option-hide').show();
                        jQuery('.ftg-hide-for-columns').hide();
                    }
                    else {
                        jQuery('.ft_gallery_height').hide();
                        jQuery('.fb-page-columns-option-hide').hide();
                        jQuery('.ftg-hide-for-columns').show();
                    }
                }
                else {
                    jQuery('.fb-page-grid-option-hide, .ft_gallery_height').hide();
                }

                // facebook show grid options
                jQuery('#ft_gallery_type').bind('change', function (e) {
                    if (jQuery('#ft_gallery_type').val() == 'post-in-grid' || jQuery('#ft_gallery_type').val() == 'gallery' || jQuery('#ft_gallery_type').val() == 'gallery-collage') {
                        jQuery('.fb-page-grid-option-hide').show();
                        if (jQuery('#ft_gallery_type').val() == 'gallery') {
                            jQuery('#ft_gallery_height').show();
                            jQuery('.fb-page-columns-option-hide').show();
                            jQuery('.ftg-hide-for-columns').hide();
                        }
                        else {
                            jQuery('.ft_gallery_height').hide();
                            jQuery('.fb-page-columns-option-hide').hide();
                            jQuery('.ftg-hide-for-columns').show();
                        }
                    }
                    else {
                        jQuery('.fb-page-grid-option-hide').hide();
                    }


                });

            });
        </script>

        <div class="clear"></div>
        <?php
        $plupload_init = array(
            'runtimes' => 'html5,silverlight,flash,html4',
            'browse_button' => 'plupload-browse-button', // will be adjusted per uploader
            'container' => 'plupload-upload-ui', // will be adjusted per uploader
            'drop_element' => 'drag-drop-area', // will be adjusted per uploader
            'file_data_name' => 'async-upload', // will be adjusted per uploader
            'multiple_queues' => true,
            'max_file_size' => wp_max_upload_size() . 'b',
            'url' => admin_url('admin-ajax.php'),
            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'filters' => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
            'multipart' => true,
            'urlstream_upload' => true,
            'multi_selection' => false, // will be added per uploader
            // additional post data to send to our ajax hook
            'multipart_params' => array(
                '_ajax_nonce' => "", // will be added per uploader
                'action' => 'plupload_action', // the ajax action name
                'postID' => $this->parent_post_id,
                'imgid' => 0 // will be added per uploader
            )
        );
        ?>
        <script type="text/javascript">
            var base_plupload_config =<?php echo json_encode($plupload_init); ?>;
        </script>
        <?php
    }

    /**
     * FT Gallery Tab Notice HTML
     *
     * creates notice html for return
     *
     * @since 1.0.0
     */
    function ft_gallery_tab_notice_html() {
        echo '<div class="ft-gallery-notice"></div>';
    }

    /**
     * FT Gallery Uploader Action
     *
     * File upload handler. Inserts Attachements info. Generates attachment info. May auto-generate WooCommerce Products
     *
     * @since 1.0.0
     */
    function ft_gallery_plupload_action() {

        //check ajax noonce
        $imgid = $_POST["imgid"];

        check_ajax_referer($imgid . 'pluploadan');

        // Fetch post ID:
        $post_id = $_POST['postID'];
        // $file = $_FILES['async-upload'];

        // handle file upload
        $status = wp_handle_upload($_FILES[ $imgid . 'async-upload' ], array('gallery_form' => true, 'action' => 'plupload_action'));

        // Insert uploaded file as attachment:
        $attach_id = wp_insert_attachment(array(
            'post_mime_type' => $status['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($status['url'])),
            'post_content' => '',
            'post_status' => 'inherit',
        ), $status['file'], $post_id);

        // Include the image handler library:
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate meta data and update attachment:
        $attach_data = wp_generate_attachment_metadata($attach_id, $status['file']);

        wp_update_attachment_metadata($attach_id, $attach_data);


        //Use File & Title renaming
        if (get_option('ft-gallery-use-attachment-naming') == '1') {
            $this->ft_gallery_rename_attachment($post_id, $attach_id);
            $this->ft_gallery_generate_new_attachment_name($post_id, $attach_id);
        } else {
            $this->ft_gallery_format_attachment_title(preg_replace('/\.[^.]+$/', '', basename($status['url'])), $attach_id, true);
        }

        $date = date_i18n('Y-m-d H:i:s');

        $attachment_date = array(
            'ID' => $attach_id,
            'post_date' => $date,
        );
        wp_update_post($attachment_date);

        if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                $gallery_to_woo = new Gallery_to_Woocommerce();
                $images_array = array($attach_id);
                $gallery_to_woo->ft_gallery_image_to_woo_prod($post_id, $images_array);
        }

        $pre_array = wp_get_attachment_image_src($attach_id, $size = 'ft_gallery_thumb');
        // We create an array and send the thumbnail url and also the attachment id so we can sort the gallery before the page is even refreshed with our ajax 'response' js var in the metabox.js file
        $return = array('url' => $pre_array[0], 'id' => $attach_id);
        // json_encode response so we can get the array of results and use them in our ajax 'response' js var in the metabox.js file too....ie* response['url'] response['id']
        echo json_encode($return);

        exit;
    }


    /**
     * FT Gallery Create Thumb
     *
     * Create a 150x150 thumbnail for our gallery edit page
     *
     * @param $image_source
     * @since 1.0.0
     */
    function ft_gallery_create_thumb($image_source) {
        $image = $image_source;
        // error_log($image_source . ' Full FILE NAME WITH HTTP<br/><br/>');
        $instance_common = new FTGallery_Create_Image();
        $force_overwrite = true;
        // Generate the new cropped gallery image.
        $instance_common->resize_image($image, '150', '150', false, 'c', '100', false, null, $force_overwrite);
    }

    /**
     * FT Gallery Generate new Attachment Name
     *
     * Generates a new attachment name (used in upload action)
     *
     * @param $gallery_id
     * @param $attachment_ID
     * @since 1.0.0
     */
    function ft_gallery_generate_new_attachment_name($gallery_id, $attachment_ID) {
        $final_title = '';
        //Include Gallery Title
        if (get_option('ft_gallery_attch_title_gallery_name') == '1') {
            $final_title .= get_the_title($gallery_id) . ' ';
        }
        //Include Gallery ID
        if (!empty($gallery_id) && get_option('ft_gallery_attch_title_post_id') == '1') {
            $final_title .= $gallery_id . ' ';
        }
        //include Date Uploaded
        if (isset($_POST['postID']) && get_option('ft_gallery_attch_title_date') == '1') {
            $final_title .= date_i18n('F jS, Y') . ' ';
        }

        $this->ft_gallery_format_attachment_title($final_title . $attachment_ID, $attachment_ID, 'true');
    }

    /**
     * FT Gallery Rename Attachment
     *
     * Renames attachment (used for File Renamin setting option)
     *
     * @param $gallery_id
     * @param $attachment_ID
     * @since 1.0.0
     */
    function ft_gallery_rename_attachment($gallery_id, $attachment_ID) {

        $file = get_attached_file($attachment_ID);
        $path = pathinfo($file);

        $final_filename = '';

        //Include Gallery Title
        if (get_option('ft_gallery_attch_name_gallery_name') == '1') {
            $final_filename .= get_the_title($gallery_id) . '-';
        }
        //Include Gallery ID
        if (!empty($gallery_id) && get_option('ft_gallery_attch_name_post_id') == '1') {
            $final_filename .= $gallery_id . '-';
        }
        //include Date Uploaded
        if (isset($_POST['postID']) && get_option('ft_gallery_attch_name_date') == '1') {
            $final_filename .= date_i18n('F jS, Y') . '-';
        }

        $final_filename = sanitize_file_name($final_filename . $attachment_ID);

        $newfile = $path['dirname'] . '/' . $final_filename . '.' . $path['extension'];

        rename($file, $newfile);
        update_attached_file($attachment_ID, $newfile);
    }


    /**
     * FT Gallery Shortcode Meta Box
     *
     * FT Gallery copy & paste shortcode input box
     *
     * @param $object
     * @since 1.0.0
     */
    public
    function ft_gallery_shortcode_meta_box($object) {
        $meta_box = '<div class="ft-gallery-meta-wrap">';

        $gallery_id = isset($_GET['post']) ? $_GET['post'] : '';

        $screen = get_current_screen();

        if ($screen->parent_file == 'edit.php?post_type=ft_gallery' && $screen->action == 'add') {
            $meta_box .= '<p>';
            $meta_box .= '<label> ' . __('Save or Publish this Gallery to be able to copy this Gallery\'s Shortcode.', 'feed-them-gallery') . '</label>';
            //$meta_box .= '<input readonly="readonly" disabled value="[feed-them-gallery id=' . $gallery_id . ']"/>';
            $meta_box .= '</p>';
        } else {
            //Copy Shortcode
            $meta_box .= '<p>';
            $meta_box .= '<label> ' . __('Copy and Paste this shortcode to any page, post or widget.', 'feed-them-gallery') . '</label>';
            $meta_box .= '<input readonly="readonly" value="[feed-them-gallery id=' . $gallery_id . ']" onclick="this.select();"/>';
            $meta_box .= '</p>';
        }

        $meta_box .= '</div>';
        // ECHO MetaBox
        echo $meta_box;
    }

    /**
     * FT Gallery Settings HTML Form
     *
     * Used to return settings form fields output for Gallery Options
     *
     * @param $gallery_id
     * @param $section_info
     * @param $required_plugins
     * @return string
     * @since @since 1.0.0
     */
    function ft_gallery_settings_html_form($gallery_id, $section_info, $required_plugins) {
        $output = '';

        $prem_required_plugins = $this->gallery_options_class->ft_gallery_required_plugins();

        $section_required_prem_plugin = !isset($section_info['required_prem_plugin']) || isset($section_info['required_prem_plugin']) && is_plugin_active($prem_required_plugins[ $section_info['required_prem_plugin'] ]['plugin_url']) ? 'active' : '';

        //Start creation of fields for each Feed
        $output .= '<div class="ftg-section" class="' . $section_info['section_wrap_class'] . '">';

        //Section Title
        $output .= isset($section_info['section_title']) ? '<h3>' . $section_info['section_title'] . '</h3>' : '';

        //Happens in JS file
        $this->ft_gallery_tab_notice_html();

        //Create settings fields for Feed OPTIONS
        foreach ($section_info['main_options'] as $option) if (!isset($option['no_html']) || isset($option['no_html']) && $option['no_html'] !== 'yes') {

            //Is a premium extension required?
            $required_plugin = !isset($option['req_plugin']) || isset($option['req_plugin']) && is_plugin_active($required_plugins[ $option['req_plugin'] ]['plugin_url']) ? true : false;
            $or_required_plugin = isset($option['or_req_plugin']) && is_plugin_active($required_plugins[ $option['or_req_plugin'] ]['plugin_url']) ? true : false;
            $or_required_plugin_three = isset($option['or_req_plugin_three']) && is_plugin_active($required_plugins[ $option['or_req_plugin_three'] ]['plugin_url']) ? true : false;

            //Sub option output START?
            $output .= isset($option['sub_options']) ? '<div class="' . $option['sub_options']['sub_options_wrap_class'] . (!$required_plugin ? ' not-active-premium-fields' : '') . '">' . (isset($option['sub_options']['sub_options_title']) ? '<h3>' . $option['sub_options']['sub_options_title'] . '</h3>' : '') . (isset($option['sub_options']['sub_options_instructional_txt']) ? '<div class="instructional-text">' . $option['sub_options']['sub_options_instructional_txt'] . '</div>' : '') : '';

            $output .= isset($option['grouped_options_title']) ? '<h3 class="sectioned-options-title">' . $option['grouped_options_title'] . '</h3>' : '';

            //Only on a few options generally
            $output .= isset($option['outer_wrap_class']) || isset($option['outer_wrap_display']) ? '<div ' . (isset($option['outer_wrap_class']) ? 'class="' . $option['outer_wrap_class'] . '"' : '') . ' ' . (isset($option['outer_wrap_display']) && !empty($option['outer_wrap_display']) ? 'style="display:' . $option['outer_wrap_display'] . '"' : '') . '>' : '';
            //Main Input Wrap
            $output .= '<div class="feed-them-gallery-admin-input-wrap ' . (isset($option['input_wrap_class']) ? $option['input_wrap_class'] : '') . '" ' . (isset($section_info['input_wrap_id']) ? 'id="' . $section_info['input_wrap_id'] . '"' : '') . '>';
            //Instructional Text
            $output .= !empty($option['instructional-text']) && !is_array($option['instructional-text']) ? '<div class="instructional-text ' . (isset($option['instructional-class']) ? $option['instructional-class'] : '') . '">' . $option['instructional-text'] . '</div>' : '';

            if (!empty($option['instructional-text']) && is_array($option['instructional-text'])) {
                foreach ($option['instructional-text'] as $instructional_txt) {
                    //Instructional Text
                    $output .= '<div class="instructional-text ' . (isset($instructional_txt['class']) ? $instructional_txt['class'] : '') . '">' . $instructional_txt['text'] . '</div>';
                }
            }

            //Label Text
            $output .= isset($option['label']) && !is_array($option['label']) ? '<div class="feed-them-gallery-admin-input-label ' . (isset($option['label_class']) ? $option['label_class'] : '') . '">' . $option['label'] . '</div>' : '';

            if (!empty($option['label']) && is_array($option['label'])) {
                foreach ($option['label'] as $label_txt) {
                    //Label Text
                    $output .= '<div class="feed-them-gallery-admin-input-label ' . (isset($label_txt['class']) ? $label_txt['class'] : '') . '">' . $label_txt['text'] . '</div>';
                }
            }

            //Post Meta option (non-global)
            $input_value = get_post_meta($gallery_id, $option['name'], true);
            //Post Meta Global checkbox Option
            $global_value = get_post_meta($gallery_id, $this->global_prefix . $option['name'], true);
            //Actual Global Option
            $get_global_option = get_option($this->global_prefix . $option['name']);

            if ($global_value && $global_value == 'true') {
                if (isset($get_global_option)) {
                    $final_value = !empty($get_global_option) ? $get_global_option : $option['default_value'];
                }
            } else {
                $final_value = !empty($input_value) || !isset($input_value) ? $input_value : $option['default_value'];
            }
            //Post Meta option (non-global)
            $input_value = get_post_meta($gallery_id, $option['name'], true);
            //Post Meta Global checkbox Option
            $global_value = get_post_meta($gallery_id, $this->global_prefix . $option['name'], true);
            //Actual Global Option
            $get_global_option = get_option($this->global_prefix . $option['name']);

            if ($global_value && $global_value == 'true') {
                if (isset($get_global_option)) {
                    $final_value = !empty($get_global_option) ? $get_global_option : $option['default_value'];
                }
            } else {
                $final_value = !empty($input_value) || !isset($input_value) ? $input_value : $option['default_value'];
            }
            $input_option = $option['option_type'];

            $gallery_class = new Gallery();
            $gallery_id = isset($_GET['post']) ? $_GET['post'] : '';
            $gallery_options_returned = $gallery_class->ft_gallery_get_gallery_options_rest($gallery_id);

            if (isset($input_option)) {
                switch ($input_option) {
                    //Input
                    case 'input':
                        $output .= '<input ' . (isset($section_required_prem_plugin) && $section_required_prem_plugin !== 'active' ? 'disabled ' : '') . 'type="' . $option['type'] . '" name="' . $option['name'] . '" id="' . $option['id'] . '" class="feed-them-gallery-admin-input ' . (isset($option['class']) ? $option['class'] : '') . '" placeholder="' . (isset($option['placeholder']) ? $option['placeholder'] : '') . '" value="' . $final_value . '"' . (isset($option['autocomplete']) ? ' autocomplete="' . $option['autocomplete'] . '"' : '') . ' />';
                        break;

                    //Select
                    case 'select':
                        $output .= '<select ' . (isset($section_required_prem_plugin) && $section_required_prem_plugin !== 'active' ? 'disabled ' : '') . 'name="' . $option['name'] . '" id="' . $option['id'] . '"  class="feed-them-gallery-admin-input">';
                        $i = 0;
                        foreach ($option['options'] as $select_option) {
                            $output .= '<option value="' . $select_option['value'] . '" ' . (!empty($final_value) && $final_value == $select_option['value'] || empty($input_value) && $i == 0 ? 'selected="selected"' : '') . '>' . $select_option['label'] . '</option>';
                            $i++;
                        }
                        $output .= '</select>';
                        break;

                    //Checkbox
                    case 'checkbox':
                        $output .= '<input ' . (isset($section_required_prem_plugin) && $section_required_prem_plugin !== 'active' ? 'disabled ' : '') . 'type="checkbox" name="' . $option['name'] . '" id="' . $option['id'] . '" ' . (!empty($final_value) && $final_value == 'true' ? ' checked="checked"' : '') . '/>';
                        break;

                    //Checkbox for image sizes COMMENTING OUT BUT LEAVING FOR FUTURE QUICK USE
                    //   case 'checkbox-image-sizes':
                    // $final_value_images = array('thumbnailzzz','mediummmm', 'large', 'full');
                    //Get Gallery Options via the Rest API
                    //        $final_value_images = $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'];
                    // print_r($final_value_images);
                    //array('thumbnailzzz','mediummmm', 'largeee', 'fullll');
                    //        $output .= '<label for="'. $option['id'] . '"><input type="checkbox" val="' . $option['default_value'] . '" name="ft_watermark_image_sizes[image_sizes][' . $option['default_value'] . ']" id="'.$option['id'] . '" '. ( array_key_exists($option['default_value'], $final_value_images) ? ' checked="checked"' : '') .'/>';
                    //        $output .= '' . $option['default_value'] . '</label>';
                    //        break;


                    //Checkbox for image sizes used so you can check the image sizes you want to be water marked after you save the page.
                    case
                    'checkbox-dynamic-image-sizes':

                        $final_value_images = isset($gallery_options_returned['ft_watermark_image_sizes']['image_sizes']) ? $gallery_options_returned['ft_watermark_image_sizes']['image_sizes'] : array();
                        $output .= '<div class="clear"></div>';

                        global $_wp_additional_image_sizes;

                        $sizes = array();
                        foreach (get_intermediate_image_sizes() as $_size) {
                            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                                $sizes[ $_size ]['width'] = get_option("{$_size}_size_w");
                                $sizes[ $_size ]['height'] = get_option("{$_size}_size_h");
                                $sizes[ $_size ]['crop'] = (bool)get_option("{$_size}_crop");
                            } elseif (isset($_wp_additional_image_sizes[ $_size ])) {
                                $sizes[ $_size ] = array(
                                    'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                    'crop' => $_wp_additional_image_sizes[ $_size ]['crop'],
                                );
                            }
                            $output .= '<label for="' . $_size . '"><input type="checkbox" val="' . $_size . '" name="ft_watermark_image_sizes[image_sizes][' . $_size . ']" id="' . $option['id'] . '-' . $_size . '" ' . (array_key_exists($_size, $final_value_images) ? ' checked="checked"' : '') . '/>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</label><br/>';

                        }
                        $output .= '<label for="full"><input type="checkbox" val="full" id="ft_watermark_image_-full" name="ft_watermark_image_sizes[image_sizes][full]" ' . (array_key_exists('full', $final_value_images) ? 'checked="checked"' : '') . '/>full</label><br/>';
                        $output .= '<br/><br/>';
                        // TESTING AREA
                        // echo $final_value_images;
                        // echo '<pre>';
                        // print_r($sizes);
                        // echo '</pre>';
                        break;

                    //Image sizes for page
                    case 'ft-images-sizes-page':
                        $final_value_images = $gallery_options_returned['ft_gallery_images_sizes_page'];
                        $output .= '<select name="' . $option['name'] . '" id="' . $option['id'] . '"  class="feed-them-gallery-admin-input">';

                        global $_wp_additional_image_sizes;

                        $sizes = array();
                        $output .= '<option val="Choose an option" ' . ('not_set' == $final_value_images ? 'selected="selected"' : '') . '>' . __('Choose an option', 'feed-them-gallery') . '</option>';
                        foreach (get_intermediate_image_sizes() as $_size) {
                            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                                $sizes[ $_size ]['width'] = get_option("{$_size}_size_w");
                                $sizes[ $_size ]['height'] = get_option("{$_size}_size_h");
                                $sizes[ $_size ]['crop'] = (bool)get_option("{$_size}_crop");
                            } elseif (isset($_wp_additional_image_sizes[ $_size ])) {
                                $sizes[ $_size ] = array(
                                    'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                    'crop' => $_wp_additional_image_sizes[ $_size ]['crop'],
                                );
                            }
                            $output .= '<option val="' . $_size . '" ' . ($_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] == $final_value_images ? 'selected="selected"' : '') . '>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</option>';
                        }
                        $output .= '<option val="full" ' . ('full' == $final_value_images ? 'selected="selected"' : '') . '>' . __('full', 'feed-them-gallery') . '</option>';
                        // TESTING AREA
                        // echo $final_value_images;
                        // echo '<pre>';
                        // print_r($sizes);
                        // echo '</pre>';
                        $output .= '</select>';
                        break;

                    //Image sizes for popup
                    case 'ft-images-sizes-popup':
                        $final_value_images = $gallery_options_returned['ft_gallery_images_sizes_popup'];
                        $output .= '<select name="' . $option['name'] . '" id="' . $option['id'] . '"  class="feed-them-gallery-admin-input">';

                        global $_wp_additional_image_sizes;

                        $sizes = array();

                        $output .= '<option val="Choose an option" ' . ('not_set' == $final_value_images ? 'selected="selected"' : '') . '>' . __('Choose an option', 'feed-them-gallery') . '</option>';
                        foreach (get_intermediate_image_sizes() as $_size) {
                            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                                $sizes[ $_size ]['width'] = get_option("{$_size}_size_w");
                                $sizes[ $_size ]['height'] = get_option("{$_size}_size_h");
                                $sizes[ $_size ]['crop'] = (bool)get_option("{$_size}_crop");
                            } elseif (isset($_wp_additional_image_sizes[ $_size ])) {
                                $sizes[ $_size ] = array(
                                    'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                    'crop' => $_wp_additional_image_sizes[ $_size ]['crop'],
                                );
                            }
                            $output .= '<option val="' . $_size . '" ' . ($_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] == $final_value_images ? 'selected="selected"' : '') . '>' . $_size . ' ' . $sizes[ $_size ]['width'] . ' x ' . $sizes[ $_size ]['height'] . '</option>';
                        }
                        $output .= '<option val="full" ' . ('full' == $final_value_images ? 'selected="selected"' : '') . '>' . __('full', 'feed-them-gallery') . '</option>';
                        // TESTING AREA
                        // echo $final_value_images;
                        // echo '<pre>';
                        // print_r($sizes);
                        // echo '</pre>';
                        $output .= '</select>';
                        break;


                    //Repeatable
                    case 'repeatable':
                        echo '<a class="repeatable-add button" href="#">';
                        _e('Add Another design', 'feed-them-gallery');
                        echo '</a><ul id="' . $option['id'] . '-repeatable" class="custom_repeatable">';
                        $i = 0;
                        if ($meta) {
                            foreach ($meta as $row) {
                                echo '<li><span class="sort hndle">|||</span>
											<textarea name="' . $option['id'] . '[' . $i . ']" id="' . $option['id'] . '">' . $row . '</textarea>
											<a class="repeatable-remove button" href="#">-</a>
											</li>';
                                $i++;
                            }
                        } else {
                            echo '<li><span class="sort hndle">|||</span>
										<textarea name="' . $option['id'] . '[' . $i . ']" id="' . $option['id'] . '">' . $row . '</textarea>
										<a class="repeatable-remove button" href="#">';
                            _e('Delete this design', 'design-approval-system');
                            echo '</a></li>';
                        }
                        echo '</ul>
							<span class="description">' . $option['desc'] . '</span>';
                        break;

                }
            }

            //GLOBAL checkbox
            $output .= '<div class="feed-them-gallery-admin-global-checkbox ft-global-option-wrap-' . $option['name'] . '">';
            $output .= '<input type="checkbox" name="' . $this->global_prefix . $option['name'] . '" id="' . $this->global_prefix . $option['id'] . '" ' . (!empty($global_value) && $global_value == 'true' ? ' checked="checked"' : '') . '/>';
            $output .= '<label for="' . $this->global_prefix . $option['name'] . '"> Use/Set Global Option </label>';
            $output .= '</div>';

            $output .= '<div class="clear"></div>';
            $output .= '</div><!--/feed-them-gallery-admin-input-wrap-->';

            $output .= isset($option['outer_wrap_class']) || isset($option['outer_wrap_display']) ? '</div>' : '';

            //Sub option output END?
            if (isset($option['sub_options_end'])) {
                $output .= !is_numeric($option['sub_options_end']) ? '</div>' : '';
                //Multiple Div needed?
                if (is_numeric($option['sub_options_end'])) {
                    $x = 1;
                    while ($x <= $option['sub_options_end']) {
                        $output .= '</div>';
                        $x++;
                    }
                }
            }
        }

        $output .= '</div> <!--/Section Wrap Class END -->';

        return $output;
    }

    /**
     * FT Gallery Save Custom Meta Box
     * Save Fields for Galleries
     *
     * @param $post_id
     * @param $post
     * @return string
     * @since 1.0.0
     */
    public
    function ft_gallery_save_custom_meta_box($post_id, $post) {
        if (!isset($_POST['ft-galleries-settings-meta-box-nonce']) || !wp_verify_nonce($_POST['ft-galleries-settings-meta-box-nonce'], basename(__FILE__)))
            return $post_id;
        //Can User Edit Post?
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
        //Autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        //CPT Check
        $slug = 'ft_gallery';
        if ($slug != $post->post_type)
            return $post_id;
        //Save Each Field Function
        foreach ($this->saved_settings_array as $box_array) {
            foreach ($box_array as $box_key => $settings) {
                if ($box_key == 'main_options') {
                    foreach ($settings as $option) {
                        //Global Value?
                        $global_old = get_post_meta($post_id, $this->global_prefix . $option['name'], true);

                        $get_global_option = get_option($this->global_prefix . $option['name']);


                        if ($option['option_type'] == 'checkbox') {
                            $new = isset($_POST[ $option['name'] ]) && $_POST[ $option['name'] ] !== 'false' ? 'true' : 'false';

                        } else {
                            $new = isset($_POST[ $option['name'] ]) ? $_POST[ $option['name'] ] : '';
                        }

                        if (isset($_POST[ $this->global_prefix . $option['name'] ]) && $_POST[ $this->global_prefix . $option['name'] ] !== 'false') {
                            update_post_meta($post_id, $this->global_prefix . $option['name'], 'true');
                            update_option($this->global_prefix . $option['name'], $new);
                        } elseif (isset($global_old) && !isset($_POST[ $this->global_prefix . $option['name'] ])) {
                            update_post_meta($post_id, $this->global_prefix . $option['name'], 'false');
                            update_post_meta($post_id, $option['name'], $new);

                        } else {
                            //Post Meta Field?
                            $old = get_post_meta($post_id, $option['name'], true);

                            if ($option['option_type'] !== 'checkbox') {
                                if ($new && $new != $old) {
                                    update_post_meta($post_id, $option['name'], $new);
                                }
                            } else {
                                update_post_meta($post_id, $option['name'], $new);
                            }
                        }
                    }

                }
            }
        }
        $attach_ID = $this->ft_gallery_get_gallery_attached_media_ids($post_id);
        foreach ($attach_ID as $img_index => $img_id) {
            $a = array(
                'ID' => $img_id,
                'menu_order' => $img_index
            );
            wp_update_post($a);
        }


        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
            include(FEED_THEM_GALLERY_PREMIUM_PLUGIN_FOLDER_DIR . 'includes/watermark/save.php');
        }
        // end premium

        // Return settings
        return $settings;

    }

    /**
     * FT Gallery Get Gallery Attached Media IDs
     *
     * Get an Array of ID's of attachments for this Gallery.
     *
     * @param $gallery_id
     * @param string $mime_type (leave empty for all types)
     * @return array
     * @since 1.0.0
     */
    function ft_gallery_get_gallery_attached_media_ids($gallery_id, $mime_type = '') {
        $post_attachments = get_attached_media($mime_type, $gallery_id);

        $attachment_ids_array = array();
        foreach ($post_attachments as $attachment) {
            $attachment_ids_array[] = $attachment->ID;
        }

        return $attachment_ids_array;
    }

    /**
     * Get Attachment Info
     * Combines get_post and wp_get_attachment_metadata to create some clean attachment info
     *
     * @param $attachment_id
     * @param bool $include_meta_data (True || False) Default: False
     * @return array
     * @since 1.0.0
     */
    function ft_gallery_get_attachment_info($attachment_id, $include_meta_data = false) {
        //Get all of the Attachment info!
        $attach_array = wp_prepare_attachment_for_js($attachment_id);

        $path_parts = pathinfo($attach_array['filename']);

        $attachment_info = array(
            'ID' => $attach_array['id'],
            'title' => $attach_array['title'],
            'type' => $attach_array['type'],
            'subtype' => $attach_array['type'],
            'alt' => $attach_array['alt'],
            'caption' => $attach_array['caption'],
            'description' => $attach_array['description'],
            'href' => $attach_array['link'],
            'src' => $attach_array['url'],
            'mime-type' => $attach_array['mime'],
            'file' => $attach_array['filename'],
            'slug' => $path_parts['filename'],
            'download_url' => get_permalink($attach_array['uploadedTo']) . '?attachment_name=' . $attach_array['id'] . '&download_file=1',
        );

        //IF Exif data is set to return and is set in Meta Data.
        //  if($include_meta_data){
        $meta_data = wp_get_attachment_metadata($attachment_id);

        $attachment_info['meta_data'] = isset($meta_data) ? $meta_data : '';

        //  }

        return $attachment_info;
    }

    /**
     * FT Gallery Format Attachment Title
     * Format the title for attachments to ensure awesome titles (options on settings page)
     *
     * @param $title
     * @param null $attachment_id
     * @param null $update_post
     * @return mixed|string
     * @since 1.0.0
     */
    function ft_gallery_format_attachment_title($title, $attachment_id = NULL, $update_post = NULL) {

        $options = get_option('ft_gallery_format_attachment_titles_options');
        $cap_options = isset($options['ft_gallery_cap_options']) ? $options['ft_gallery_cap_options'] : 'dont_alter';

        if (!empty($attachment_id)) {
            $uploaded_post_id = get_post($attachment_id);
            //$title = $uploaded_post_id->post_title;
        }

        /* Update post. */
        $char_array = array();
        if (isset($options['ft_gallery_fat_hyphen']) && $options['ft_gallery_fat_hyphen']) {
            $char_array[] = '-';
        }
        if (isset($options['ft_gallery_fat_underscore']) && $options['ft_gallery_fat_underscore']) {
            $char_array[] = '_';
        }
        if (isset($options['ft_gallery_fat_period']) && $options['ft_gallery_fat_period']) {
            $char_array[] = '.';
        }
        if (isset($options['ft_gallery_fat_tilde']) && $options['ft_gallery_fat_tilde']) {
            $char_array[] = '~';
        }
        if (isset($options['ft_gallery_fat_plus']) && $options['ft_gallery_fat_plus']) {
            $char_array[] = '+';
        }

        /* Replace chars with spaces, if any selected. */
        if (!empty($char_array)) {
            $title = str_replace($char_array, ' ', $title);
        }

        /* Trim multiple spaces between words. */
        $title = preg_replace("/\s+/", " ", $title);

        /* Capitalize Title. */
        switch ($cap_options) {
            case 'cap_all':
                $title = ucwords($title);
                break;
            case 'cap_first':
                $title = ucfirst(strtolower($title));
                break;
            case 'all_lower':
                $title = strtolower($title);
                break;
            case 'all_upper':
                $title = strtoupper($title);
                break;
            case 'dont_alter':
                /* Leave title as it is. */
                break;
        }

        //Return Clean Title otherwise update post!
        if ($update_post !== 'true') {
            return $title;
        }

        // add formatted title to the alt meta field
        if (isset($options['ft_gallery_fat_alt']) && $options['ft_gallery_fat_alt']) {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', $title);
        }

        // update the post
        $uploaded_post = array(
            'ID' => $attachment_id,
            'post_title' => $title,
        );

        // add formatted title to the description meta field
        if (isset($options['ft_gallery_fat_description']) && $options['ft_gallery_fat_description']) {
            $uploaded_post['post_content'] = $title;
        }

        // add formatted title to the caption meta field
        if (isset($options['ft_gallery_fat_caption']) && $options['ft_gallery_fat_caption']) {
            $uploaded_post['post_excerpt'] = $title;
        }

        wp_update_post($uploaded_post);

        return $title;
    }

    /**
     * FT Gallery ZIP exists check
     * Check if ZIP still exists
     *
     * @param $id_to_check
     * @return bool
     * @since 1.0.0
     */
    public
    function ft_gallery_zip_exists_check($id_to_check) {
        $ft_gallery_zip_status = get_post_status($id_to_check);

        //Check the Status if False or in Trash return false
        return $ft_gallery_zip_status == false || $ft_gallery_zip_status == 'trash' ? 'false' : 'true';
    }


    /**
     * FT Gallery Duplicate Post As Draft
     * Function creates post duplicate as a draft and redirects then to the edit post screen
     *
     * @since 1.0.0
     */
    function ft_gallery_duplicate_post_as_draft() {
        global $wpdb;
        if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'ft_gallery_duplicate_post_as_draft' == $_REQUEST['action']))) {
            wp_die(__('No post to duplicate has been supplied!', 'feed-them-gallery'));
        }

        /*
         * Nonce verification
         */
        if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__)))
            return;

        /*
         * get the original post id
         */
        $post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
        /*
         * and all the original post data then
         */
        $post = get_post($post_id);

        /*
         * if you don't want current user to be the new post author,
         * then change next couple of lines to this: $new_post_author = $post->post_author;
         */
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;

        /*
         * if post data exists, create the post duplicate
         */
        if (isset($post) && $post != null) {

            /*
             * new post data array
             */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status' => $post->ping_status,
                'post_author' => $new_post_author,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_name' => $post->post_name,
                'post_parent' => $post->post_parent,
                'post_password' => $post->post_password,
                'post_status' => 'draft',
                'post_title' => $post->post_title,
                'post_type' => $post->post_type,
                'to_ping' => $post->to_ping,
                'menu_order' => $post->menu_order
            );

            /*
             * insert the post by wp_insert_post() function
             */
            $new_post_id = wp_insert_post($args);

            /*
             * get all current post terms ad set them to the new post draft
             */
            $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
            }

            /*
             * duplicate all post meta just in two SQL queries
             */
            $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
            if (count($post_meta_infos) != 0) {
                $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($post_meta_infos as $meta_info) {
                    $meta_key = $meta_info->meta_key;
                    if ($meta_key == '_wp_old_slug') continue;
                    $meta_value = addslashes($meta_info->meta_value);
                    $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
                }
                $sql_query .= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query($sql_query);
            }


            /*
             * finally, redirect to the edit post screen for the new draft
             */
            wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
            exit;
        } else {
            wp_die('Post creation failed, could not find original post: ' . $post_id);
        }
    }

    /**
     * FT Gallery Duplicate Post Link
     * Add the duplicate link to action list for post_row_actions
     *
     * @param $actions
     * @param $post
     * @return mixed
     * @since 1.0.0
     */
    function ft_gallery_duplicate_post_link($actions, $post) {
        if (current_user_can('edit_posts')) {
            $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=ft_gallery_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="Duplicate this item" rel="permalink">' . __('Duplicate', 'feed-them-gallery') . '</a>';
        }

        return $actions;
    }


    /**
     * FT Gallery Duplicate Post ADD Duplicate Post Button
     * Add a button in the post/page edit screen to create a clone
     *
     * @since 1.0.0
     */
    function ft_gallery_duplicate_post_add_duplicate_post_button() {
        if (isset($_GET['post'])) {
            $id = $_GET['post'];
            ?>
            <div id="ht-gallery-duplicate-action">
                <a href="<?php echo wp_nonce_url('admin.php?action=ft_gallery_duplicate_post_as_draft&post=' . $id, basename(__FILE__), 'duplicate_nonce') ?>" title="Duplicate this item" rel="permalink"><?php _e('Duplicate Gallery', 'feed-them-gallery'); ?></a>
            </div>
            <?php
        }
    }
} ?>