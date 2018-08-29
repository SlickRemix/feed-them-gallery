<?php
/**
 * Albums Class
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
class Albums {

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
        add_action('admin_enqueue_scripts', array($this, 'ft_gallery_albums_scripts'));
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

        //Register ALBUMS CPT
        add_action('init', array($this, 'ft_gallery_albums_cpt'));

        //Register Gallery Categories
        //add_action( 'init', array($this, 'ft_gallery_categories') );

        //Add Gallery Categories to attachments
        //add_action( 'init', array($this, 'ft_gallery_add_cats_to_attachments') , 15);

        //Response Messages
        add_filter('post_updated_messages', array($this, 'ft_gallery_albums_updated_messages'));

        //Gallery List function
        //add_filter('manage_ft_gallery_posts_columns', array($this, 'ft_gallery_set_custom_edit_columns'));
        //add_action('manage_ft_gallery_posts_custom_column', array($this, 'ft_gallery_custom_edit_column'), 10, 2);

        //Change Button Text
        add_filter('gettext', array($this, 'ft_gallery_albums_set_button_text'), 20, 3);
        //Add Meta Boxes
        add_action('add_meta_boxes', array($this, 'ft_gallery_albums_add_metaboxes'));

        //Rename Submenu Item to Galleries
        add_filter('attribute_escape', array($this, 'ft_gallery_albums_rename_submenu_name'), 10, 2);
        //Add Shortcode
        //add_shortcode('ft_gallery_albums_list', array($this, 'ft_gallery_albums_display_list'));

        // Drag and Drop, buttons etc for media
        //add_action('wp_ajax_plupload_action', array($this, 'ft_gallery_albums_plupload_action'));

        // Set local variables:
        $this->plugin_locale = MY_TEXTDOMAIN;
        // Set WordPress version:
        $this->wordpress_version = substr(str_replace('.', '', $wp_version), 0, 2);

        add_action('current_screen', array($this, 'ft_gallery_albums_check_page'));

        //Save Meta Box Info
        add_action('save_post', array($this, 'ft_gallery_albums_save_custom_meta_box'), 10, 2);

        //Add API Endpoint
        //add_action('rest_api_init', array($this, 'ft_galley_register_gallery_options_route'));

        //add_action('wp_ajax_list_update_order', array($this, 'ft_gallery_albums_order_list'));


        if (get_option('ft_gallery_albums_duplicate_post_show') == '') {

            add_action('admin_action_ft_gallery_albums_duplicate_post_as_draft', array($this, 'ft_gallery_albums_duplicate_post_as_draft'));
            add_filter('page_row_actions', array($this, 'ft_gallery_albums_duplicate_post_link'), 10, 2);
            add_filter('ft_gallery_albums_row_actions', array($this, 'ft_gallery_albums_duplicate_post_link'), 10, 2);
            add_action('post_submitbox_start', array($this, 'ft_gallery_albums_duplicate_post_add_duplicate_post_button'));

        }
        //Add Galleries to Album
        add_action('wp_ajax_ft_gallery_add_galleries_to_album', array($this, 'ft_gallery_add_galleries_to_album'));
        add_action('wp_ajax_nopriv_ft_gallery_add_galleries_to_album', array($this, 'ft_gallery_add_galleries_to_album'));

         //Delete Galleries from Album
        //add_action('wp_ajax_ft_gallery_delete_galleries_from_album', array($this, 'ft_gallery_delete_galleries_from_album'));
        //add_action('wp_ajax_nopriv_ft_gallery_delete_galleries_from_album', array($this, 'ft_gallery_delete_galleries_from_album'));


        add_filter('single_template', array($this, 'ftg_locate_albums_template'), 999);
    }

    function ftg_locate_albums_template($located) {
        global $post;

        //Set The Template name
        $template_name = 'album-template.php';

        if ($post->post_type == 'ft_gallery_albums') {
            // No file found yet
            $located = false;
            // Continue if template is empty
            if ( empty( $template_name ) )
                // Trim off any slashes from the template name
                $template_name = ltrim( $template_name, '/' );

                // Check child theme first
                if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'ft-gallery/' . $template_name ) ) {
                    $located = trailingslashit( get_stylesheet_directory() ) . 'ft-gallery/' . $template_name;
                // Check parent theme next
                } elseif ( file_exists( trailingslashit( get_template_directory() ) . 'ft-gallery/' . $template_name ) ) {
                    $located = trailingslashit( get_template_directory() ) . 'ft-gallery/' . $template_name;
                // Check theme compatibility last
                } elseif ( file_exists( trailingslashit( FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'templates/' . $template_name ) )) {
                    $located = trailingslashit( FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'templates/' . $template_name);
                }
                //Use Plugins Album template
                if(empty($located )){

                    $plugin_location = FEED_THEM_GALLERY_PLUGIN_FOLDER_DIR . 'templates/'. $template_name;

                    load_template( $plugin_location);

                    return $plugin_location;
                }
            }

            if (!empty( $located ) ){
                load_template( $located);

            return $located;
        }
    }

    /**
     * FT Gallery Tab Notice HTML
     *
     * creates notice html for return
     *
     * @since 1.0.0
     */
    function ft_gallery_albums_tab_premium_msg() {
        echo sprintf(__('%1$sPlease purchase, install and activate %2$sFeed Them Gallery Premium%3$s for these additional awesome features!%4$s', 'feed-them-gallery'),
            '<div class="ft-gallery-premium-mesg">',
            '<a href="' . esc_url('https://www.slickremix.com/downloads/feed-them-gallery/') . '">',
            '</a>',
            '</div>'
        );
    }

    /**
     * FT Gallery Image To WooCommerce Product
     * Creates a Woocommerce product from a Feed Them Gallery Image attachment
     *
     * @param null $postID
     * @param null $galleries_array
     * @param null $ignore_echos
     * @since 1.0.0
     */
    public function ft_gallery_add_galleries_to_album($AlbumID = NULL, $galleries_array = NULL, $ignore_echos = NULL) {

        $album_id = empty($AlbumID) ? intval(sanitize_text_field($_REQUEST['AlbumID'])) : $AlbumID;

        $album_gallery_ids = get_post_meta($album_id, 'ft_gallery_album_gallery_ids', true);

        //Check and set $album_gallery_ids
        $album_gallery_ids = isset($album_gallery_ids) && !empty($album_gallery_ids) ? $album_gallery_ids : array();

        //Check if we are using the AJAX or the variable set in onclick
        if(!is_array($galleries_array)){
            //Check to see if this is only Selected Images
            $selected_galleries = isset($_REQUEST['selectedGalleries']) && !empty($_REQUEST['selectedGalleries']) ? json_decode(stripslashes($_REQUEST['selectedGalleries'])) : '';
        }
        else{
            $selected_galleries = $galleries_array;
        }

        //Media was Selected so make name have attachments count.
        if (is_array($selected_galleries) && !empty($selected_galleries)) {

            //Check if wc_get_product returned an object.
            if (!empty($selected_galleries)) {

                    //Duplicate Woo Model Product and Update new product.
                    foreach ($selected_galleries as $key => $gallery_id) {

                        if(!in_array($gallery_id, $album_gallery_ids)){

                            $album_gallery_ids[] = $gallery_id;
                        }

                        echo $gallery_id;
                    }

                    //Add Gallery ID's to Album's array
                    update_post_meta($album_id, 'ft_gallery_album_gallery_ids', $album_gallery_ids);

                    if(empty($ignore_echos) || !empty($ignore_echos) && $ignore_echos !== 'true'){
                        echo  __('Galleries added to this album', 'ft-gallery');
                    }
            }
            //No Product Object returned [Let users know what to do]
            else {
                if(empty($ignore_echos) || !empty($ignore_echos) && $ignore_echos !== 'true') {
                    _e('No Module Product Found or selected. Please select a "<strong>Single Image Model Product</strong>" on the ', 'ft-gallery');
                    echo '<a href="post.php?post=' . $gallery_id . '&action=edit&tab=ft_woo_commerce">'.__('Woocommerce Tab.', 'ft-gallery').'</a>';
                }
            }
        }
        else {
            if(empty($ignore_echos) || !empty($ignore_echos) && $ignore_echos !== 'true') {
                _e('No Images in this Gallery. Please upload images to use this feature', 'ft-gallery');
            }
        }
        exit;
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
    function ft_gallery_albums_custom_thumb_sizes($sizes) {
        return array_merge($sizes, array(
            'ft_gallery_albums_thumb' => __('Feed Them Gallery Thumb'),
        ));
    }

    /**
     * FT Gallery Order List
     *
     * Attachment order list
     *
     * @since 1.0.0
     */
    function ft_gallery_albums_order_list() {
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
    function ft_gallery_albums_check_page() {
        $current_screen = get_current_screen();

        if (is_admin() && $current_screen->post_type == 'ft_gallery_albums' && $current_screen->base == 'post') {

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
            'callback' => array($this, 'ft_gallery_albums_get_gallery_options'),
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
    public function ft_gallery_albums_get_gallery_options_rest($gallery_id) {

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
    public function ft_gallery_albums_get_gallery_options($gallery_id) {

        $post_info = get_post($gallery_id['gallery_id']);

        // echo '<pre>';
        // print_r($post_info);
        // echo '</pre>';

        $options_array = array();

        //Basic Post Info
        $options_array['ft_gallery_albums_image_id'] = isset($post_info->ID) ? $post_info->ID : __('This ID does not exist anymore', 'feed-them-gallery');
        $options_array['ft_gallery_albums_author'] = isset($post_info->post_author) ? $post_info->post_author : '';
        //   $options_array['ft_gallery_albums_post_date'] = $post_info->post_date_gmt;
        $options_array['ft_gallery_albums_post_title'] = isset($post_info->post_title) ? $post_info->post_title : '';
        //   $options_array['ft_gallery_albums_post_alttext'] = $post_info->post_title;
        //   $options_array['ft_gallery_albums_comment_status'] = $post_info->comment_status;


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
                            $options_array[ $option_name ] = !empty($option_value) ? $option_value : $option_default_value;
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
     * Create FT Gallery Albums custom post type
     *
     * @since 1.0.0
     */
    public function ft_gallery_albums_cpt() {
        $responses_cpt_args = array(
            'label' => __('Albums', 'feed-them-gallery'),
            'labels' => array(
                'menu_name' => __('Albums', 'feed-them-gallery'),
                'name' => __('Albums', 'feed-them-gallery'),
                'singular_name' => __('Album', 'feed-them-gallery'),
                'add_new' => __('Add Album', 'feed-them-gallery'),
                'add_new_item' => __('Add New Album', 'feed-them-gallery'),
                'edit_item' => __('Edit Album', 'feed-them-gallery'),
                'new_item' => __('New Album', 'feed-them-gallery'),
                'view_item' => __('View Album', 'feed-them-gallery'),
                'search_items' => __('Search Albums', 'feed-them-gallery'),
                'not_found' => __('No Albums Found', 'feed-them-gallery'),
                'not_found_in_trash' => __('No Albums Found In Trash', 'feed-them-gallery'),
            ),

            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'show_in_menu' => 'edit.php?post_type=ft_gallery',
            'show_in_nav_menus' => true,
            'exclude_from_search' => true,

            'capabilities' => array(
                'create_posts' => true, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
            ),
            'map_meta_cap' => true, //Allows Users to still edit Payments
            'has_archive' => true,
            'hierarchical' => true,
            'query_var' => 'ft_gallery_albums',
            'rewrite' => array('slug'=>'ftg-album'),

            'menu_icon' => '',
            'supports' => array('title', 'revisions', 'thumbnail'),
            'order' => 'DESC',
            // Set the available taxonomies here
            //'taxonomies' => array('ft_gallery_topics')
        );
        register_post_type('ft_gallery_albums', $responses_cpt_args);
    }

    /**
     * FT Gallery Categories (Custom Taxonomy)
     *
     * Create FT Gallery Custom Taxonomy
     *
     * @since 1.0.2
     */
    public function ft_gallery_albums_categories() {

        $labels = array(
            'name' => _x('Categories', 'feed-them-gallery'),
            'singular_name' => _x('Category', 'feed-them-gallery'),
            'search_items' => __('Search Categories', 'feed-them-gallery'),
            'all_items' => __('All Categories', 'feed-them-gallery'),
            'parent_item' => __('Parent Category', 'feed-them-gallery'),
            'parent_item_colon' => __('Parent Category:', 'feed-them-gallery'),
            'edit_item' => __('Edit Category', 'feed-them-gallery'),
            'update_item' => __('Update Category', 'feed-them-gallery'),
            'add_new_item' => __('Add New Category', 'feed-them-gallery'),
            'new_item_name' => __('New Category Name', 'feed-them-gallery'),
            'menu_name' => __('Categories', 'feed-them-gallery'),
        );

        register_taxonomy('ft_gallery_albums_cats', array('ft_gallery_albums'), array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'register_taxonomy' => true,
            'rewrite' => array('slug'=>'ftg-album'),
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
    function ft_gallery_albums_add_cats_to_attachments() {
        register_taxonomy_for_object_type('ft_gallery_albums_cats', 'attachment');
        // add_post_type_support('attachment', 'ft_gallery_albums_cats');
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
    function ft_gallery_albums_rename_submenu_name($safe_text, $text) {
        if ('Galleries' !== $text) {
            return $safe_text;
        }
        // We are on the main menu item now. The filter is not needed anymore.
        remove_filter('attribute_escape', array($this, 'ft_gallery_albums_rename_submenu_name'));

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
    public function ft_gallery_albums_updated_messages($messages) {
        global $post, $post_ID;
        $messages['ft_gallery_albums'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Album updated.', 'feed-them-gallery'),
            2 => __('Custom field updated.', 'feed-them-gallery'),
            3 => __('Custom field deleted.', 'feed-them-gallery'),
            4 => __('Album updated.', 'feed-them-gallery'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf(__('Response restored to revision from %s', 'feed-them-gallery'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
            6 => __('Album created.', 'feed-them-gallery'),
            7 => __('Album saved.', 'feed-them-gallery'),
            8 => __('Album submitted.', 'feed-them-gallery'),
            9 => __('Album scheduled for: <strong>%1$s</strong>.', 'feed-them-gallery'),
            // translators: Publish box date format, see http://php.net/date
            // date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
            10 => __('Album draft updated.', 'feed-them-gallery'),
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
    function ft_gallery_albums_set_custom_edit_columns($columns) {

        $new = array();

        foreach ($columns as $key => $value) {

            if ($key == 'title') {  // when we find the date column
                $new[ $key ] = $value;
                $new['gallery_thumb'] = __('', 'feed-them-gallery');  // put the tags column before it
                $new['gallery_shortcode'] = __('Album Shortcode', 'feed-them-gallery');

                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                    $text = __('Gallery ZIP', 'feed-them-gallery');
                } else {
                    $text = '';
                }

                $new['gallery_zip'] = $text;

            } else {
                $new[ $key ] = $value;
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
    function ft_gallery_albums_count_post_images($post_id) {
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
    function ft_gallery_albums_custom_edit_column($column, $post_id) {
        switch ($column) {
            case 'gallery_thumb' :
                $display_gallery = new Display_Gallery();
                $image_list = $display_gallery->ft_gallery_albums_get_media_rest($post_id, '1');

                if ($image_list) {
                    echo '<a href="' . get_edit_post_link($post_id) . '"><img src="' . $image_list[0]['media_details']['sizes']['thumbnail']['source_url'] . '" alt="" />';
                    echo $this->ft_gallery_albums_count_post_images($post_id) . ' ' . __('Images', 'feed-them-gallery') . '</a>';
                }
                break;
            // display a thumbnail photo
            case 'gallery_shortcode' :
                echo '<input value="[feed-them-gallery-album id=' . $post_id . ']" onclick="this.select()"/>';
                break;

            case 'gallery_zip' :

                //Add Premium Coloumns
                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                    $newest_zip = get_post_meta($post_id, 'ft_gallery_albums_newest_zip_id', true);

                    if ($newest_zip) {
                        $newest_zip_check = $this->ft_gallery_albums_zip_exists_check($newest_zip);

                        if ($newest_zip_check == 'true') {
                            $ft_gallery_albums_get_attachment_info = $this->ft_gallery_albums_get_attachment_info($newest_zip);
                            echo '<a class="ft_gallery_download_button_icon" href="' . $ft_gallery_albums_get_attachment_info['download_url'] . '"><span class="dashicons dashicons-download"></span></a>';
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
    function ft_gallery_albums_set_button_text($translated_text, $text, $domain) {
        $post_id = isset($_GET['post']) ? $_GET['post'] : '';
        $custom_post_type = get_post_type($post_id);
        if (!empty($post_id) && $custom_post_type == 'ft_gallery_albums_responses') {
            switch ($translated_text) {
                case 'Publish' :
                    $translated_text = __('Save Album', 'feed-them-gallery');
                    break;
                case 'Update' :
                    $translated_text = __('Update Album', 'feed-them-gallery');
                    break;
                case 'Save Draft' :
                    $translated_text = __('Save Album Draft', 'feed-them-gallery');
                    break;
                case 'Edit Payment' :
                    $translated_text = __('Edit Album', 'feed-them-gallery');
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
    function ft_gallery_albums_scripts() {

        global $id, $post;

        // Get current screen.
        $current_screen = get_current_screen();

        if (is_admin() && $current_screen->post_type == 'ft_gallery_albums' && $current_screen->base == 'post') {

            // Set the post_id for localization.
            $post_id = isset($post->ID) ? $post->ID : (int)$id;

            // Image Uploader
            wp_enqueue_media(array(
                'post' => $post_id,
            ));
            add_filter('plupload_init', array($this, 'plupload_init'));
            // Updates the attachments when saving
            //  add_filter( 'wp_insert_post_data', array( $this, 'ft_gallery_albums_sort_images_meta_save' ), 99, 2 );

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

            wp_register_script('ft-gallery-albums', plugins_url('feed-them-gallery/includes/albums/js/albums.js'));
            wp_enqueue_script('ft-gallery-albums');


            wp_enqueue_script('jquery-form');

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
    function ft_gallery_albums_sort_images_meta_save($post_data) {

        $attach_ID = $this->ft_gallery_albums_get_attachment_info($post_data['ID']);

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
    function ft_gallery_albums_add_metaboxes() {
        global $post;
        // Check we are using Feed Them Gallery Custom Post type
        if ('ft_gallery_albums' != $post->post_type) {
            return;
        }

        //Image Uploader and Gallery area in admin
        add_meta_box('ft-galleries-upload-mb', __('Feed Them Gallery Settings', 'feed-them-gallery'), array($this, 'ft_gallery_albums_uploader_meta_box'), 'ft_gallery_albums', 'normal', 'high', null);

        //Link Settings Meta Box
        add_meta_box('ft-galleries-shortcode-side-mb', __('Feed Them Gallery Shortcode', 'feed-them-gallery'), array($this, 'ft_gallery_albums_shortcode_meta_box'), 'ft_gallery_albums', 'side', 'high', null);
    }



    function gallery_featured_first($gallery_id, $size = 'ft_gallery_thumb', $type='image') {

        //Gallery has a featured image so lets show it!
        if (has_post_thumbnail($gallery_id)) {
            return get_the_post_thumbnail_url($gallery_id, $size);
            //the_post_thumbnail($size);
        }
        //Nope, Gallery doesn't have a featured image so lets get first image attachement url
        else{

            $gallery_class = new Gallery();

            $attached_media =  $gallery_class->ft_gallery_get_gallery_attached_media_ids($gallery_id,$type);

            $first_attachement = $gallery_class->ft_gallery_get_attachment_info($attached_media[0]);

            $first_img = wp_get_attachment_image_src($first_attachement['ID'], $size);
           return $first_img[0];
        }
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
    function ft_gallery_albums_uploader_meta_box($object) {
        wp_nonce_field(basename(__FILE__), 'ft-galleries-settings-meta-box-nonce'); ?>

        <?php
        echo '<div class="ft-gallery-popup-form" style="display:none">';

        echo '<label>' . __('Title of image', $this->plugin_locale) . '</label>';
        echo '<input value="" class="fts-gallery-title" />';
        echo '<label>' . __('Alt text for image', $this->plugin_locale) . '</label>';
        echo '<input value="" class="fts-gallery-alttext" />';
        echo '<label>' . __('Description of image', $this->plugin_locale) . '</label>';
        echo '<textarea class="fts-gallery-description"></textarea><br/><div class="ft-submit-wrap"><a class="ft-gallery-edit-img-ajax button button-primary button-large" id="ft-gallery-edit-img-ajax" href="javascript:;" data-nonce="' . wp_create_nonce('ft_gallery_albums_edit_image_nonce') . '">' . __('Save', $this->plugin_locale) . '</a></div>';
        echo '</div><div class="clear"></div>';

        $gallery_id = isset($_GET['post']) ? $_GET['post'] : ''; ?>
        <div class="ft-gallery-settings-tabs-meta-wrap">

            <script>
                jQuery(document).ready(ftg_admin_gallery_tabs);
                function ftg_admin_gallery_tabs() {
                    // enable link to tab
                    $('ul.nav-tabs').each(function () {
                        // For each set of tabs, we want to keep track of
                        // which tab is active and its associated content
                        var $active, $content, $links = $(this).find('a');

                        // If the location.hash matches one of the links, use that as the active tab.
                        // If no match is found, use the first link as the initial active tab.
                        $active = $($links.filter('[href="' + location.hash + '"]')[0] || $links[0]);
                        $active.addClass('active');

                        $content = $($active[0].hash);

                        // Hide the remaining content
                        $links.not($active).each(function () {
                            $(this.hash).hide();
                        });

                        // Bind the click event handler
                        $(this).on('click', 'a', function (e) {
                            // Make the old tab inactive.
                            $active.removeClass('active');
                            $content.hide();

                            // Update the variables with the new link and content
                            $active = $(this);
                            $content = $(this.hash);

                            // Make the tab active.
                            $active.addClass('active');
                            $content.show();

                            // Prevent the anchor's default click action
                            e.preventDefault();
                        });
                    });
                }

                jQuery(document).ready(function ($) {
                    jQuery('.ft-gallery-notice').on('click', '.ft-gallery-notice-close', function () {
                        jQuery('.ft-gallery-notice').html('');
                        jQuery('.ft-gallery-notice').removeClass('updated, ftg-block')
                    });

                    // Show the proper tab if this link type is clicked on any tab of ours
                    jQuery(".ftg-zips-tab").click(function (e) {
                        jQuery('.tab4 a').click();
                        var clickedLink = $('.tab4 a').attr('href');
                        // push it into the url
                        location.hash = clickedLink;
                        // Prevent the anchor's default click action
                        e.preventDefault();
                    });
                    jQuery(".ftg-woo-tab").click(function (e) {
                        jQuery('.tab5 a').click();
                        var clickedLink = $('.tab5 a').attr('href');
                        // push it into the url
                        location.hash = clickedLink;
                        // Prevent the anchor's default click action
                        e.preventDefault();
                    });
                    jQuery(".ftg-images-tab").click(function (e) {
                        jQuery('.tab1 a').click();
                        var clickedLink = $('.tab1 a').attr('href');
                        // push it into the url
                        location.hash = clickedLink;
                        // Prevent the anchor's default click action
                        e.preventDefault();
                    });

                    var hash = window.location.hash.replace('#', '');
                    if (hash) {
                        document.getElementById(hash).style.display = 'block'
                    }

                    <?php  if(!empty($_GET['post_type'])) { ?>
                    var submitAjax = 'no';
                    // alert('no');
                    <?php  }
                    else { ?>
                    var submitAjax = 'yes';
                    // alert('yes');
                    <?php  } ?>

                    if (submitAjax == 'yes') {
                        jQuery('.post-type-ft_gallery_albums .wrap form#post').submit(function (e) {
                            e.preventDefault();
                            jQuery(this).ajaxSubmit({
                                beforeSend: function () {
                                    jQuery('#ftg-saveResult').html("<div class='ftg-overlay-background'><div class='ftg-relative-wrap-overlay'><div id='ftg-saveMessage'    class='ftg-successModal ftg-saving-form'></div></div></div>");
                                    jQuery('#ftg-saveMessage').append("<?php echo htmlentities(__('Saving Options', 'feed-them-gallery'), ENT_QUOTES); ?>").show();
                                    jQuery('#publishing-action .spinner').css("visibility", "visible");

                                },
                                success: function () {
                                    jQuery('#ftg-saveResult').html("<div class='ftg-overlay-background'><div class='ftg-relative-wrap-overlay'><div id='ftg-saveMessage'    class='ftg-successModal ftg-success-form'></div></div></div>");
                                    jQuery('#ftg-saveMessage').append("<?php echo htmlentities(__('Settings Saved Successfully', 'feed-them-gallery'), ENT_QUOTES); ?>").show();
                                    jQuery('#publishing-action .spinner').css("visibility", "hidden");

                                    setTimeout("jQuery('.ftg-overlay-background').hide();", 800);

                                    var hash2 = window.location.hash.replace('#', '');
                                    // alert(hash2);
                                    if (hash2 === 'images' || hash2 === '') {
                                        location.reload();
                                    }
                                    // We change the text from Updating... at the bottom of a long page to Update.
                                    jQuery('.updatefrombottom a.button-primary').html("<?php _e('Update', 'feed-them-gallery') ?>");
                                }
                            });
                            return false;
                        });
                    }
                    // click event listener
                    $('.post-type-ft_gallery_albums ul.nav-tabs a').click(function (event) {
                        // get the id
                        var clickedLink = $(this).attr('href');
                        // push it into the url
                        location.hash = clickedLink;
                    });

                });
            </script>

            <div class="tabs" id="tabs">
                <div class="tabs-menu-wrap" id="tabs-menu">
                    <ul class="nav nav-tabs nav-append-content">

                        <li class="tabbed tab1">
                            <a href="#images" data-toggle="tab" class="account-tab-highlight" aria-expanded="true">
                                <div class="ft_icon"></div>
                                <span class="das-text"><?php _e('Galleries', 'feed-them-gallery') ?></span>
                            </a>
                        </li>

                        <li class="tabbed tab2">
                            <a href="#layout" data-toggle="tab" aria-expanded="false">
                                <div class="ft_icon"></div>
                                <span class="das-text"><?php _e('Layout', 'feed-them-gallery') ?></span>
                            </a>
                        </li>

                        <li class="tabbed tab3">
                            <a href="#colors" data-toggle="tab" aria-expanded="false">
                                <div class="ft_icon"></div>
                                <span class="das-text"><?php _e('Colors', 'feed-them-gallery') ?></span>
                            </a>
                        </li>
                        <li class="tabbed tab5">
                            <a href="#woocommerce" data-toggle="tab" aria-expanded="false">
                                <div class="ft_icon"></div>
                                <span class="das-text"><?php _e('WooCommerce', 'feed-them-gallery') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content-wrap">

                    <div class="tab-pane images-tab-pane " id="images">

                        <div id="ftg-tab-content1" class="tab-content <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_images' || !isset($_GET['tab'])) {
                            echo ' pane-active';
                        } ?>">
                            <div class="ftg-section">

                                <div id="uploadContainer" style="margin-top: 10px;">

                                <h3>Galleries in Album</h3>

                                    <div class="gallery-edit-button-wrap">
                                        <button type="button" class="ft-gallery-download-gallery ft_gallery_download_button_icon button button-primary button-larg" onclick="ft_gallery_delete_galleries_from_album('<?php echo $this->parent_post_id; ?>')"><?php _e('Delete Galleries from Album', 'feed-them-gallery') ?></button>
                                        <a class="gallery-edit-button-question-one" href="javascript:;" rel="gallery-edit-question-download-gallery">?</a>
                                    </div>


                                    <div class="album-galleries">
                                        <ul class="plupload-thumbs <?php if (isset($multiple) & !empty($multiple)): ?>plupload-thumbs-multiple<?php endif; ?>" id="<?php echo $object->ID; ?>plupload-thumbs" data-post-id="<?php echo $object->ID; ?>">
                                            <?php

                                            $album_gallery_ids = get_post_meta($object->ID, 'ft_gallery_album_gallery_ids', true);

                                            $show_title = get_post_meta($object->ID, 'ft_gallery_albums_show_title', true);

                                            //Display Images Gallery
                                            $size = 'ft_gallery_thumb';

                                            //&& isset($gallery_list[0])
                                            if (is_array($album_gallery_ids) && isset($album_gallery_ids)) {

                                                foreach ($album_gallery_ids as $key => $gallery) {

                                                    $gallery_meta = get_post($gallery);

                                                    if($gallery_meta){
                                                        $gallery_img_url = $this->gallery_featured_first($gallery_meta->ID, $size);

                                                        $gallery_edit_url = get_edit_post_link($gallery_meta->ID);

                                                        $meta_box = '<li class="thumb in-album" id="list_item_' . $gallery_meta->ID . '" data-image-id="' . $gallery_meta->ID . '" data-menu-order="' . $gallery_meta->menu_order . '">';

                                                        //  $meta_box .= '<a href="' . $gallery['media_details']['sizes']['full']['source_url'] . '" rel="gallery-' . $gallery['id'] . '" class="ft-gallery-edit-img-popup">';
                                                        $meta_box .= '<img src="' . $gallery_img_url . '"/>';
                                                        //  $meta_box .= '</a>';
                                                        $meta_box .= '<div class="ft-gallery-edit-thumb-btn"><a title="Edit this image." class="ft-gallery-edit-img-popup" data-id="' . $gallery_meta->ID . '" data-nonce="' . wp_create_nonce('ft_gallery_albums_edit_image_nonce') . '" href="' . $gallery_edit_url . '"></a></div>';

                                                        $meta_box .= '<div class="ft-gallery-select-thumbn"><label class="ft-gallery-myCheckbox"><input type="checkbox" class=“ft-gallery-img-checkbox” rel="' . $gallery_meta->ID . '" name="image-' . $gallery_meta->ID . '" id="image-' . $gallery_meta->ID . '"/><span></span></label></div>';

                                                        $meta_box .= '</li>';

                                                        echo $meta_box;
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="clear"></div>

                                    <?php
                                    $display_gallery = new Display_Gallery();

                                    // $gallery_list = $display_gallery->ft_gallery_albums_get_media_rest($this->parent_post_id, '100');
                                    $args = array(
                                        'post_type' => 'ft_gallery',
                                        'posts_per_page' => -1,
                                        'orderby' => 'menu_order',
                                        'order' => 'asc',
                                        'exclude' => 0 // Exclude featured thumbnail
                                    );
                                    $gallery_list = get_posts($args);

                                    if (is_array($gallery_list) && $object->ID == true && isset($gallery_list[0])) { ?>
                                        <div class="ftg-number-of-images-wrap"><?php echo $this->ft_gallery_albums_count_post_images($object->ID) ?><?php _e(' Images', 'feed-them-gallery'); ?></div>
                                    <?php } ?>

                                    <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" />
                                <?php
                                //Happens in JS file
                                $this->ft_gallery_albums_tab_notice_html(); ?>

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
                                $gallery_size_name = get_post_meta($object->ID, 'ft_gallery_albums_images_sizes_popup', true);

                                // $gallerys_count = count( $attachments );
                                ?>
                                <div class="clear"></div>

                                <h3>Galleries NOT in Album</h3>

                                <input type="submit" class="metabox_submit" value="Submit" style="display: none;" />

                                    <div class="gallery-edit-button-wrap">
                                        <button type="button" class="ft-gallery-download-gallery ft_gallery_download_button_icon button button-primary button-larg" onclick="ft_gallery_add_galleries_to_album('<?php echo $this->parent_post_id; ?>')"><?php _e('Add Galleries to Album', 'feed-them-gallery') ?></button>
                                        <a class="gallery-edit-button-question-one" href="javascript:;" rel="gallery-edit-question-download-gallery">?</a>
                                    </div>

                                <div class="clear"></div>



                                <ul class="plupload-thumbs <?php if (isset($multiple) & !empty($multiple)): ?>plupload-thumbs-multiple<?php endif; ?>" id="<?php echo $object->ID; ?>plupload-thumbs" data-post-id="<?php echo $object->ID; ?>">
                                    <?php
                                    $show_title = get_post_meta($object->ID, 'ft_gallery_albums_show_title', true); ?><?php

                                    //Display Images Gallery


                                    $size = 'ft_gallery_thumb';

                                    //   && isset($gallery_list[0])
                                    if (is_array($gallery_list) && isset($gallery_list[0]) && !empty($gallery_list)) {

                                        foreach ($gallery_list as $key => $gallery) {

                                            //Check if Gallery ID is already in album or album's gallery is isn't created yet
                                            if(!isset($album_gallery_ids) || isset($album_gallery_ids) && !in_array($gallery->ID, $album_gallery_ids)){
                                                $gallery_img_url = $this->gallery_featured_first($gallery, $size);

                                                $gallery_edit_url = get_edit_post_link($gallery->ID);

                                                $meta_box = '<li class="thumb out-album" id="list_item_' . $gallery->ID . '" data-image-id="' . $gallery->ID . '" data-menu-order="' . $gallery->menu_order . '">';

                                                //  $meta_box .= '<a href="' . $gallery['media_details']['sizes']['full']['source_url'] . '" rel="gallery-' . $gallery['id'] . '" class="ft-gallery-edit-img-popup">';
                                                $meta_box .= '<img src="' . $gallery_img_url . '"/>';
                                                //  $meta_box .= '</a>';
                                                $meta_box .= '<div class="ft-gallery-edit-thumb-btn"><a title="Edit this image." class="ft-gallery-edit-img-popup" data-id="' . $gallery->ID . '" data-nonce="' . wp_create_nonce('ft_gallery_albums_edit_image_nonce') . '" href="' . $gallery_edit_url . '"></a></div>';

                                                $meta_box .= '<div class="ft-gallery-select-thumbn"><label class="ft-gallery-myCheckbox"><input type="checkbox" class=“ft-gallery-img-checkbox” rel="' . $gallery->ID . '" name="image-' . $gallery->ID . '" id="image-' . $gallery->ID . '"/><span></span></label></div>';


                                                //$meta_box .= '<div class="ft-image-id-for-popup"><p><strong>' . __('Uploaded:', 'feed-them-gallery') . '</strong> ' . $instagram_date . '</p><br/><input value="' . $gallery['id'] . '" class="fts-gallery-id" type="text" data-nonce="' . wp_create_nonce('ft_gallery_albums_edit_image_nonce') . '"  /><input value="' . $next_img->ID . '" class="fts-gallery-id fts-next-image" type="text" data-nonce="' . wp_create_nonce('ft_gallery_albums_edit_image_nonce') . '"  /><input value="' . $prev_img->ID . '" class="fts-gallery-id fts-prev-image" type="text" data-nonce="' . wp_create_nonce('ft_gallery_albums_edit_image_nonce') . '"  /></div>';
                                                $meta_box .= '</li>';

                                                echo $meta_box;
                                            }
                                        }
                                    }
                                    else{
                                       echo '<li>No Galleries to add to this Album.</li>';
                                    }


                                    ?>
                                </ul>
                                <div class="clear"></div>
                                <?php if (!isset($gallery_list[0])) {
                                    ?>
                                    <style type="text/css">
                                        .slickdocit-videowrapper {
                                            max-width: 100%;
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
                                            float: right;
                                        }

                                        #slickdocit-show-video:hover, #slickdocit-hide-video {
                                            opacity: .8;
                                        }

                                        #slickdocit-hide-video {
                                            display: none;
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
                                                <iframe id="slickdocit-iframe" src="https://www.youtube.com/embed/Fa2mjmFAGZQ?rel=0" data-autoplay-src="https://www.youtube.com/embed/Fa2mjmFAGZQ?rel=0&autoplay=1" frameborder="0" allowscriptaccess="always" allowfullscreen=""></iframe>
                                            </div>
                                        </div>
                                        <div id="slickdocit-show-video" class="slickdocit-show-video"><?php _e('View Quick Setup Video', 'feed-them-gallery'); ?>
                                            <span class="slickdocit-play"></span></div>
                                        <div id="slickdocit-hide-video" class="ftg-close-vid"><?php _e('Close Video', 'feed-them-gallery'); ?>
                                            <span class="slickdocit-play"></div>
                                        <script>
                                            jQuery(".slickdocit-show-video").click(function () {
                                                var videoURL = jQuery("#slickdocit-iframe");
                                                videoURL.attr("src", videoURL.data("autoplay-src"));
                                                jQuery(".slickdocit-videowrapper").slideDown();
                                                jQuery('.slickdocit-show-video').hide();
                                                jQuery('.ftg-close-vid').show();
                                            });
                                            jQuery(".ftg-close-vid").click(function () {
                                                var videoURL = jQuery("#slickdocit-iframe");
                                                jQuery(".slickdocit-videowrapper").slideUp();
                                                jQuery('.ftg-close-vid').hide();
                                                //Then assign the src to null, this then stops the video been playing
                                                jQuery('.slickdocit-show-video').show();
                                                videoURL.attr("src", '');
                                            });
                                        </script>
                                        <h3><?php _e('Quick Guide to Getting Started', 'feed-them-gallery'); ?></h3>
                                        <p><?php
                                            echo sprintf(__('Please look over the options on the %1$sSettings%2$s page before creating your first gallery.%3$s1. Enter a title for your gallery at the top of the page in the "Enter title here" input. %4$s2. Add images to the gallery and sort them in the order you want. %4$s3. Publish the gallery by clicking the blue "Publish" button. %4$s4. Now you can edit your images title, description and more. %5$sView our %6$sImage Gallery Demos%7$s or %8$sFull documentation%9$s for more details.', 'feed-them-gallery'),
                                                '<a href="' . esc_url('edit.php?post_type=ft_gallery_albums&page=ft-gallery-settings-page') . '" >',
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

                    </div><!-- /.tab-pane -->

                    <div class="tab-pane layout-tab-pane" id="layout">

                        <div id="ftg-tab-content2" class="tab-content <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_layout') {
                            echo ' pane-active';
                        } ?>">

                            <?php echo $this->ft_gallery_albums_settings_html_form($this->parent_post_id, $this->saved_settings_array['layout'], null); ?>
                            <div class="clear"></div>
                            <div class="ft-gallery-note">
                                <?php
                                echo sprintf(__('Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery'),
                                    '<a href="' . esc_url('edit.php?post_type=ft_gallery_albums&page=ft-gallery-settings-page') . '" >',
                                    '</a>'
                                );
                                ?>
                            </div>

                        </div>

                    </div><!-- /.tab-pane -->

                    <div class="tab-pane colors-tab-pane" id="colors">
                        <div id="ftg-tab-content3" class="tab-content <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_colors') {
                            echo ' pane-active';
                        } ?>">
                            <?php
                            echo $this->ft_gallery_albums_settings_html_form($this->parent_post_id, $this->saved_settings_array['colors'], null); ?>
                            <div class="clear"></div>

                            <div class="ft-gallery-note"> <?php
                                echo sprintf(__('Additional Global options available on the %1$sSettings Page%2$s', 'feed-them-gallery'),
                                    '<a href="' . esc_url('edit.php?post_type=ft_gallery_albums&page=ft-gallery-settings-page') . '" >',
                                    '</a>'
                                );
                                ?>
                            </div>

                        </div>

                    </div><!-- /.tab-pane -->

                    <div class="tab-pane zips-tab-pane" id="zips">
                        <div id="ftg-tab-content6" class="tab-content <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_zip_gallery') {
                            echo ' pane-active';
                        } ?>">
                            <?php

                            //If Premium add Functionality
                            if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                echo '<div class="ftg-section">' . $this->ft_gallery_albums_tab_premium_msg() . '</div>';
                            }
                            ?>
                            <div class="ftg-section">

                                <h3><?php _e('Gallery Digital Zip History List', 'feed-them-gallery'); ?></h3>
                                <?php
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
                                                <a class="ft_gallery_albums_delete_zip_button"><?php _e('Delete'); ?></a>
                                            </div>
                                            <div class="ft-gallery-file-delete ft-gallery-file-zip-to-woo">
                                                <a class="ft_gallery_albums_create_woo_prod_button"><?php _e('Create product'); ?></a>
                                            </div>
                                            <div class="ft-gallery-file-view">
                                                <a class="ft_gallery_albums_view_zip_button"><?php _e('View Contents'); ?></a>
                                            </div>
                                            <ol class="zipcontents_list"></ol>
                                        </li>
                                    </ul>
                                <?php }

                                //If Premium add Functionality
                                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                    //Happens in JS file
                                    $this->ft_gallery_albums_tab_notice_html();

                                    echo $this->zip_gallery_class->ft_gallery_albums_list_zip_files($this->parent_post_id);
                                }
                                ?>

                            </div>
                            <div class="clear"></div>
                        </div>


                    </div><!-- /.tab-pane -->

                    <div class="tab-pane woocommerce-tab-pane" id="woocommerce">
                        <div id="ftg-tab-content5" class="tab-content <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_woo_commerce') {
                            echo ' pane-active';
                        } ?>">

                            <?php

                            if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) { ?>
                                <div class="ftg-section">
                                    <?php $this->ft_gallery_albums_tab_premium_msg(); ?>
                                </div>
                            <?php } ?>

                            <?php
                            //  echo '<pre>';
                            //  print_r(wp_prepare_attachment_for_js('21529'));
                            //  echo '</pre>';

                            echo $this->ft_gallery_albums_settings_html_form($this->parent_post_id, $this->saved_settings_array['woocommerce'], null); ?>

                            <div class="tab-5-extra-options">

                                <div class="feed-them-gallery-admin-input-wrap ">
                                    <div class="feed-them-gallery-admin-input-label"><?php _e('Global Model Product', 'feed-them-gallery'); ?></div>
                                    <?php
                                    if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                        $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                        echo $gallery_to_woo_class->ft_gallery_albums_image_to_woo_model_prod_select($this->parent_post_id, 'global');
                                    } else {
                                        $ftg_prem_not_active = '<select disabled="" class="feed-them-gallery-admin-input"><option value="" selected="selected">Premium Required</option></select>';
                                        echo $ftg_prem_not_active;
                                    } ?>
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

                                    <div class="feed-them-gallery-admin-input-label"><?php _e('Smart Image Orientation Model Products', 'feed-them-gallery'); ?></div>
                                    <br />

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
                                        echo $gallery_to_woo_class->ft_gallery_albums_image_to_woo_model_prod_select($this->parent_post_id, 'landscape');
                                    } else {
                                        echo $ftg_prem_not_active;
                                    } ?>
                                    </p>
                                    <p>
                                    <div class="feed-them-gallery-admin-input-label"><?php _e('Square Image Model Product', 'feed-them-gallery'); ?></div>
                                    <?php
                                    if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                        $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                        echo $gallery_to_woo_class->ft_gallery_albums_image_to_woo_model_prod_select($this->parent_post_id, 'square');
                                    } else {
                                        echo $ftg_prem_not_active;
                                    }
                                    ?>
                                    </p>
                                    <p>
                                    <div class="feed-them-gallery-admin-input-label"><?php _e('Portrait Image Model Product', 'feed-them-gallery'); ?></div>
                                    <?php
                                    if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                        $gallery_to_woo_class = new Gallery_to_Woocommerce();
                                        echo $gallery_to_woo_class->ft_gallery_albums_image_to_woo_model_prod_select($this->parent_post_id, 'portrait');
                                    } else {
                                        echo $ftg_prem_not_active;
                                    } ?>
                                    </p>
                                </div>

                                <div class="feed-them-gallery-admin-input-wrap ">
                                    <div class="feed-them-gallery-admin-input-label"><?php _e('ZIP Model Product', 'feed-them-gallery'); ?></div>
                                    <?php
                                    if (is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                                        echo $gallery_to_woo_class->ft_gallery_albums_zip_to_woo_model_prod_select($this->parent_post_id);
                                    } else {
                                        echo $ftg_prem_not_active;
                                    } ?>
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
                                        '<a href="' . esc_url('edit.php?post_type=ft_gallery_albums&page=ft-gallery-settings-page') . '" >',
                                        '</a>'
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.tab-pane -->

                    <div class="tab-pane watermark-tab-pane" id="watermark">
                        <div id="ftg-tab-content7" class="tab-content <?php if (isset($_GET['tab']) && $_GET['tab'] == 'ft_watermark') {
                            echo ' pane-active';
                        } ?>">

                            <?php if (!is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) { ?>
                                <div class="ftg-section">
                                    <?php $this->ft_gallery_albums_tab_premium_msg(); ?>
                                </div>
                            <?php }

                            echo $this->ft_gallery_albums_settings_html_form($this->parent_post_id, $this->saved_settings_array['watermark'], null); ?>

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
                    </div><!-- /.tab-pane -->
                </div>
            </div>
            <div class="clear"></div>

        </div> <!-- #tabs close -->

        <div id="ftg-saveResult"></div>

        <script>
            jQuery(document).ready(function ($) {

                //create hash tag in url for tabs
                //  jQuery('.post-type-ft_gallery_albums').on('click', ".button-large", function () {
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

                if (jQuery('#ft_gallery_albums_popup').val() == 'no') {
                    jQuery('.ft-images-sizes-popup').hide();
                    // jQuery('.display-comments-wrap').show();

                }
                //Facebook Display Popup option
                jQuery('#ft_gallery_albums_popup').bind('change', function (e) {
                    if (jQuery('#ft_gallery_albums_popup').val() == 'yes') {
                        jQuery('.ft-images-sizes-popup').show();
                        // jQuery('.display-comments-wrap').show();

                    }
                    else {
                        jQuery('.ft-images-sizes-popup').hide();
                        //  jQuery('.display-comments-wrap').hide();
                    }
                });


                if (jQuery("#ft_gallery_albums_watermark").val() == 'imprint') {
                    jQuery('.ft-watermark-hidden-options').show();
                    jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').hide();
                }


                if (jQuery('#ft_gallery_albums_watermark').val() == 'overlay') {
                    jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').show();
                    jQuery('.ft-watermark-hidden-options').hide();
                }

                // facebook show load more options
                jQuery('#ft_gallery_albums_watermark').bind('change', function (e) {
                    if (jQuery('#ft_gallery_albums_watermark').val() == 'imprint') {

                        jQuery('.ft-watermark-hidden-options').show();
                        jQuery('.ft-watermark-overlay-options, .ft-gallery-watermark-opacity').hide();
                    }
                    if (jQuery('#ft_gallery_albums_watermark').val() == 'overlay') {
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
                jQuery('#ft_gallery_albums_load_more_option').bind('change', function (e) {
                    if (jQuery('#ft_gallery_albums_load_more_option').val() == 'yes') {

                        if (jQuery('#facebook-messages-selector').val() !== 'album_videos') {
                            jQuery('.fts-facebook-load-more-options-wrap').show();
                        }
                        jQuery('.fts-facebook-load-more-options2-wrap').show();
                    }

                    else {
                        jQuery('.fts-facebook-load-more-options-wrap, .fts-facebook-load-more-options2-wrap').hide();
                    }
                });


                if (jQuery('#ft_gallery_albums_load_more_option').val() == 'yes') {
                    jQuery('.fts-facebook-load-more-options-wrap, .fts-facebook-load-more-options2-wrap').show();
                    jQuery('.fts-facebook-grid-options-wrap').show();
                }
                if (jQuery('#ft_gallery_albums_grid_option').val() == 'yes') {
                    jQuery('.fts-facebook-grid-options-wrap').show();
                    jQuery(".feed-them-gallery-admin-input-label:contains('Center Facebook Container?')").parent('div').show();
                }


                if (jQuery('#ft_gallery_albums_type').val() == 'post-in-grid' || jQuery('#ft_gallery_albums_type').val() == 'gallery' || jQuery('#ft_gallery_albums_type').val() == 'gallery-collage') {
                    jQuery('.fb-page-grid-option-hide').show();
                    if (jQuery('#ft_gallery_albums_type').val() == 'gallery') {
                        jQuery('#ft_gallery_albums_height').show();
                        jQuery('.fb-page-columns-option-hide').show();
                        jQuery('.ftg-hide-for-columns').hide();
                    }
                    else {
                        jQuery('.ft_gallery_albums_height').hide();
                        jQuery('.fb-page-columns-option-hide').hide();
                        jQuery('.ftg-hide-for-columns').show();
                    }
                }
                else {
                    jQuery('.fb-page-grid-option-hide, .ft_gallery_albums_height').hide();
                }

                // facebook show grid options
                jQuery('#ft_gallery_albums_type').bind('change', function (e) {
                    if (jQuery('#ft_gallery_albums_type').val() == 'post-in-grid' || jQuery('#ft_gallery_albums_type').val() == 'gallery' || jQuery('#ft_gallery_albums_type').val() == 'gallery-collage') {
                        jQuery('.fb-page-grid-option-hide').show();
                        if (jQuery('#ft_gallery_albums_type').val() == 'gallery') {
                            jQuery('#ft_gallery_albums_height').show();
                            jQuery('.fb-page-columns-option-hide').show();
                            jQuery('.ftg-hide-for-columns').hide();
                        }
                        else {
                            jQuery('.ft_gallery_albums_height').hide();
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
    }

    /**
     * FT Gallery Tab Notice HTML
     *
     * creates notice html for return
     *
     * @since 1.0.0
     */
    function ft_gallery_albums_tab_notice_html() {
        echo '<div class="ft-gallery-notice"></div>';
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
    function ft_gallery_albums_shortcode_meta_box($object) {
        $meta_box = '<div class="ft-gallery-meta-wrap">';

        $gallery_id = isset($_GET['post']) ? $_GET['post'] : '';

        $screen = get_current_screen();

        if ($screen->parent_file == 'edit.php?post_type=ft_gallery_albums' && $screen->action == 'add') {
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
     * FT Gallery Save Custom Meta Box
     * Save Fields for Galleries
     *
     * @param $post_id
     * @param $post
     * @return string
     * @since 1.0.0
     */
    public
    function ft_gallery_albums_save_custom_meta_box($post_id, $post) {
        if (!isset($_POST['ft-galleries-settings-meta-box-nonce']) || !wp_verify_nonce($_POST['ft-galleries-settings-meta-box-nonce'], basename(__FILE__)))
            return $post_id;
        //Can User Edit Post?
        if (!current_user_can('edit_post', $post_id))
            return $post_id;
        //Autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        //CPT Check
        $slug = 'ft_gallery_albums';
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
        $attach_ID = $this->ft_gallery_albums_get_gallery_attached_media_ids($post_id);
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
     * FT Gallery Duplicate Post As Draft
     * Function creates post duplicate as a draft and redirects then to the edit post screen
     *
     * @since 1.0.0
     */
    function ft_gallery_albums_duplicate_post_as_draft() {
        global $wpdb;
        if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'ft_gallery_albums_duplicate_post_as_draft' == $_REQUEST['action']))) {
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
    function ft_gallery_albums_duplicate_post_link($actions, $post) {
        if (current_user_can('edit_posts')) {
            $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=ft_gallery_albums_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="Duplicate this item" rel="permalink">' . __('Duplicate', 'feed-them-gallery') . '</a>';
        }

        return $actions;
    }


    /**
     * FT Gallery Duplicate Post ADD Duplicate Post Button
     * Add a button in the post/page edit screen to create a clone
     *
     * @since 1.0.0
     */
    function ft_gallery_albums_duplicate_post_add_duplicate_post_button() {
        if (isset($_GET['post'])) {
            $id = $_GET['post'];
            ?>
            <div id="ht-gallery-duplicate-action">
                <a href="<?php echo wp_nonce_url('admin.php?action=ft_gallery_albums_duplicate_post_as_draft&post=' . $id, basename(__FILE__), 'duplicate_nonce') ?>" title="Duplicate this item" rel="permalink"><?php _e('Duplicate Gallery', 'feed-them-gallery'); ?></a>
            </div>
            <?php
        }
    }
} ?>