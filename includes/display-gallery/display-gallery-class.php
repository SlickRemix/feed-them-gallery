<?php
/**
 * Display Gallery
 *
 * Class Feed Them Gallery Settings Page
 *
 * @class    Display_Gallery
 * @version  1.0.1
 * @package  FeedThemSocial/Core
 * @category Class
 * @author   SlickRemix
 */

namespace feed_them_gallery;
// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Class Display_Gallery
 */
class Display_Gallery extends Gallery {

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Display_Gallery constructor.
     */
    function __construct() {

        //Add API Endpoint
        add_action('rest_api_init', array($this, 'ft_gallery_register_gallery_route'));

        //Add Shortcode
        add_shortcode('feed-them-gallery', array($this, 'ft_gallery_display_gallery_shortcode'));

        add_action('wp_ajax_ft_gallery_update_title_ajax', array($this, 'ft_gallery_update_title_ajax'));
        add_action('wp_ajax_ft_gallery_edit_image_ajax', array($this, 'ft_gallery_edit_image_ajax'));
        add_action('wp_ajax_ft_gallery_update_image_ajax', array($this, 'ft_gallery_update_image_ajax'));
        add_action('wp_ajax_ft_gallery_delete_image_ajax', array($this, 'ft_gallery_delete_image_ajax'));
        add_action('wp_ajax_ft_gallery_update_image_information_ajax', array($this, 'ft_gallery_update_image_information_ajax'));

        //Add Display Gallery Scripts
        add_action('current_screen', array($this, 'ft_gallery_display_gallery_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'ft_gallery_head'));

        add_action('wp_ajax_ft_gallery_load_more', array($this, 'ft_gallery_load_more'));
        add_action('wp_ajax_nopriv_ft_gallery_load_more', array($this, 'ft_gallery_load_more'));
    }

    /**
     * FT Gallery Register Gallery Route (REST API)
     *
     * Register gallery route to use wordpress's REST API
     * @since 1.0.0
     */
    public function ft_gallery_register_gallery_route() {
        register_rest_route('ftgallery/v2', '/post-gallery', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array($this, 'ft_gallery_display_post_images')
        ));
    }

    /**
     * FT Gallery Display Gallery Scripts
     *
     * Add scripts to WordPress Admin header
     *
     * @since 1.0.0
     */
    function ft_gallery_display_gallery_scripts() {
        $current_screen = get_current_screen();

        if ($current_screen->post_type == 'ft_gallery' && $current_screen->base == 'post') {
            wp_enqueue_script('js_color', plugins_url('/feed-them-gallery/admin/js/jscolor/jscolor.js'), array('jquery'), FTG_CURRENT_VERSION);
            wp_enqueue_script('ft_gallery_display_gallery_scripts', plugins_url('/feed-them-gallery/admin/js/admin.js'), array('jquery'), FTG_CURRENT_VERSION);
            wp_enqueue_script('jquery');
            wp_localize_script('ft_gallery_display_gallery_scripts', 'ssAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            wp_enqueue_script('ft_gallery_display_gallery_scripts');
        }
    }

    /**
     * FT Gallery Header Scripts
     *
     * Add gallery scripts to frontend header
     *
     * @since 1.0.0
     */
    function ft_gallery_head() {

        wp_enqueue_style('ft-gallery-feeds', plugins_url('feed-them-gallery/includes/feeds/css/styles.css'), array(), FTG_CURRENT_VERSION);
        wp_enqueue_script('ft-masonry-pkgd', plugins_url('feed-them-gallery/includes/feeds/js/masonry.pkgd.min.js'), array('jquery'), FTG_CURRENT_VERSION);
        wp_enqueue_script('ft-images-loaded', plugins_url('feed-them-gallery/includes/feeds/js/imagesloaded.pkgd.min.js'), array(), FTG_CURRENT_VERSION);
        wp_enqueue_script('ft-front-end-js', plugins_url('feed-them-gallery/includes/js/front-end.js'), array(), FTG_CURRENT_VERSION);
        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php')) {
            wp_enqueue_script('add-to-cart-ajax_ajax', plugins_url() . '/feed-them-gallery/includes/feeds/js/add-to-cart-ajax.js', array('jquery'), FTG_CURRENT_VERSION, true);
        }


    }

    /**
     * FTS Ago
     *
     * Create date format like fb and twitter. Thanks: http://php.quicoto.com/how-to-calculate-relative-time-like-facebook/ .
     *
     * @param $timestamp
     * @return string
     * @since 1.0.0
     */
    function ft_gallery_ago($timestamp) {
        // not setting isset'ing anything because you have to save the settings page to even enable this feature
        $fts_language_second = get_option('ft_gallery_language_second');
        if (empty($fts_language_second)) $fts_language_second = 'second';
        $fts_language_seconds = get_option('ft_gallery_language_seconds');
        if (empty($fts_language_seconds)) $fts_language_seconds = 'seconds';
        $fts_language_minute = get_option('ft_gallery_language_minute');
        if (empty($fts_language_minute)) $fts_language_minute = 'minute';
        $fts_language_minutes = get_option('ft_gallery_language_minutes');
        if (empty($fts_language_minutes)) $fts_language_minutes = 'minutes';
        $fts_language_hour = get_option('ft_gallery_language_hour');
        if (empty($fts_language_hour)) $fts_language_hour = 'hour';
        $fts_language_hours = get_option('ft_gallery_language_hours');
        if (empty($fts_language_hours)) $fts_language_hours = 'hours';
        $fts_language_day = get_option('ft_gallery_language_day');
        if (empty($fts_language_day)) $fts_language_day = 'day';
        $fts_language_days = get_option('ft_gallery_language_days');
        if (empty($fts_language_days)) $fts_language_days = 'days';
        $fts_language_week = get_option('ft_gallery_language_week');
        if (empty($fts_language_week)) $fts_language_week = 'week';
        $fts_language_weeks = get_option('ft_gallery_language_weeks');
        if (empty($fts_language_weeks)) $fts_language_weeks = 'weeks';
        $fts_language_month = get_option('ft_gallery_language_month');
        if (empty($fts_language_month)) $fts_language_month = 'month';
        $fts_language_months = get_option('ft_gallery_language_months');
        if (empty($fts_language_months)) $fts_language_months = 'months';
        $fts_language_year = get_option('ft_gallery_language_year');
        if (empty($fts_language_year)) $fts_language_year = 'year';
        $fts_language_years = get_option('ft_gallery_language_years');
        if (empty($fts_language_years)) $fts_language_years = 'years';
        $fts_language_ago = get_option('ft_gallery_language_ago');
        if (empty($fts_language_ago)) $fts_language_ago = 'ago';

        //	$periods = array( "sec", "min", "hour", "day", "week", "month", "years", "decade" );
        $periods = array($fts_language_second, $fts_language_minute, $fts_language_hour, $fts_language_day, $fts_language_week, $fts_language_month, $fts_language_year, "decade");
        $periods_plural = array($fts_language_seconds, $fts_language_minutes, $fts_language_hours, $fts_language_days, $fts_language_weeks, $fts_language_months, $fts_language_years, "decades");

        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
            if (!is_numeric($timestamp)) {
                return "";
            }
        }
        $difference = date_i18n(time()) - $timestamp;
        // Customize in your own language. Why thank-you I will.
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        if ($difference > 0) { // this was in the past
            $ending = $fts_language_ago;
        } else { // this was in the future
            $difference = -$difference;
            //not doing dates in the future for posts
            $ending = "to go";
        }
        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j] = $periods_plural[$j];
        }
        $text = "$difference $periods[$j] $ending";

        return $text;
    }

    /**
     * FT Gallery Rand String
     *
     * Random String Generator
     *
     * @param int $length
     * @return string
     * @since 1.0.0
     */
    function ft_gallery_rand_string($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * FT Gallery Custom Date
     *
     * Generate Custom Date using settings from Settings Page
     *
     * @param $created_time
     * @return string
     * @since 1.0.0
     */
    function ft_gallery_custom_date($created_time) {
        $ftsCustomDate = get_option('ft-gallery-custom-date');
        $ftsCustomTime = get_option('ft-gallery-custom-time');
        $CustomDateCheck = get_option('ft-gallery-date-and-time-format');
        $fts_timezone = get_option('ft-gallery-timezone');

        if ($ftsCustomDate == '' && $ftsCustomTime == '') {
            $CustomDateFormat = $CustomDateCheck;
        } elseif ($ftsCustomDate !== '' || $ftsCustomTime !== '') {
            $CustomDateFormat = $ftsCustomDate . ' ' . $ftsCustomTime;
        } else {
            $CustomDateFormat = 'F jS, Y \a\t g:ia';
        }
        if (!empty($fts_timezone)) {
            date_default_timezone_set($fts_timezone);
        }

        if ($CustomDateCheck == 'one-day-ago') {
            $uTime = $this->ft_gallery_ago($created_time);
        } else {
            $uTime = !empty($CustomDateCheck) ? date_i18n($CustomDateFormat, strtotime($created_time)) : $this->ft_gallery_ago($created_time);
        }

        //Return the time
        return $uTime;
    }

    /**
     * FT Gallery Custom Trim Words
     *
     * This function is a duplicate of fb trim words and is used for all feeds except fb, which uses it's original function that also filters tags which we don't need.
     *
     * @param $text
     * @param int $num_words
     * @param $more
     * @return mixed
     * @since 1.0.0
     */
    public function ft_gallery_trim_words($text, $num_words = 45, $more) {
        !empty($num_words) && $num_words !== 0 ? $more : __('...');
        $text = nl2br($text);
        $text = strip_shortcodes($text);
        // Add tags that you don't want stripped
        $text = strip_tags($text, '<strong><br><em><i><a>');
        $words_array = preg_split("/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY);
        $sep = ' ';
        if (count($words_array) > $num_words) {
            array_pop($words_array);
            $text = implode($sep, $words_array);
            $text = $text . $more;
        } else {
            $text = implode($sep, $words_array);
        }

        return wpautop($text);
    }


    /**
     * FT Gallery Get Image Sizes
     *
     * @param $attachment_id
     * @return mixed
     * @since 1.0.0
     */
    function ft_gallery_get_image_sizes($attachment_id) {

        $metadata = wp_get_attachment_metadata($attachment_id);

        return $metadata;
    }

    /**
     * FT Gallery Get Attachment
     *
     * Get attachement info from core function
     *
     * @param $attachment_id
     * @return array
     * @since 1.0.0
     */
    function ft_gallery_get_attachment($attachment_id) {

        $attachment = $this->ft_gallery_get_attachment_info($attachment_id);

        return $attachment;
    }

    /**
     * FT Gallery Display Post Images
     * Return a List of Images attached to a post.
     *
     * @return mixed
     * @since
     */
    public function ft_gallery_display_post_images() {
        //Get Post ID (Admin ELSE Front end)
        // if (is_admin()) {
        //  $final_id = 18240;
        // } else {
        global $post;
        $final_id = $post->ID;
        // }

        $args = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_mime_type' => 'image', 'post_parent' => $final_id);
        $attachments = get_posts($args);


        return $attachments;
    }


    /**
     * FT Gallery Get Media Rest (REST API)
     *
     * Get Media using WordPress's REST API
     *
     * @param $parent_post_id
     * @param string $per_page
     * @return string
     * @since 1.0.0
     */
    public function ft_gallery_get_media_rest($parent_post_id, $per_page = '100') {


        $request = new \WP_REST_Request('GET', '/wp/v2/media');
        // Set one or more request query parameters
        $request->set_param('per_page', $per_page);
        $request->set_param('parent', $parent_post_id);
        $request->set_param('media_type', 'image');

        $response = rest_do_request($request);

        /*  echo '<pre>';
          print_r($response);
          echo '</pre>';*/


        // Check for error
        if (is_wp_error($response)) {
            return 'oops something isn\'t right.';
        }

        $final_response = isset($response->data) ? $response->data : 'No Images attached to this post.';

        return $final_response;
    }

    /**
     * FT Gallery Delete Gallery Image REST API
     *
     * Delete Image using WordPress's REST API
     *
     * @param $parent_post_id
     * @return string
     * @since 1.0.0
     */
    public function ft_gallery_delete_media_rest($parent_post_id) {
        $request = new \WP_REST_Request('DELETE', '/wp/v2/media/' . $parent_post_id);
        // Set one or more request query parameters
        $request->set_param('force', true);

        $response = rest_do_request($request);

        // Check for error
        if (is_wp_error($response)) {
            return 'oops something isn\'t right.';
        }

        $final_response = isset($response->data) ? $response->data : 'No Images attached to this post.';

        return $final_response;
    }

    /**
     * FT Gallery Update Media Rest
     *
     * Update or Remove Gallery Image REST API
     *
     * @link https://developer.wordpress.org/rest-api/reference/media/#update-media
     *
     * @param $parent_post_id
     * @param array $args
     * @return string
     * @see ft_gallery_delete_quick_item_ajax() Where ajax is fired to trigger this function
     * @since 1.0.0
     */
    public function ft_gallery_update_media_rest($parent_post_id, array $args) {
        $request = new \WP_REST_Request('POST', '/wp/v2/media/' . $parent_post_id);

        //Set each Parameter passed
        if (isset($args) && !empty($args)) {
            foreach ($args as $param => $value) {
                //Set Parameter and Value of Parameter
                $request->set_param($param, $value);
            }
        }

        $response = rest_do_request($request);

        // Check for error
        if (is_wp_error($response)) {
            return 'oops something isn\'t right:' . $response;
        }

        $final_response = isset($response->data) ? $response->data : 'No Images attached to this post.';

        return $final_response;
    }

    /**
     * FT Gallery Delete Image AJAX
     *
     * Delete an image using AJAX
     *
     * @since 1.0.0
     */
    function ft_gallery_delete_image_ajax() {
        $permission = check_ajax_referer('ft_gallery_delete_image_nonce', 'nonce', false);
        if ($permission == false) {
            echo 'error';
        } else {
            $display_gallery = new Display_Gallery();
            $display_gallery->ft_gallery_delete_media_rest($_REQUEST['id']);
            echo 'success';
        }
        exit();
    }

    /**
     * FT Gallery Update Image AJAX
     *
     * Update image using AJAX
     *
     * @since 1.0.0
     */
    function ft_gallery_update_image_ajax() {
        //  $permission = check_ajax_referer('ft_gallery_update_image_nonce', 'nonce', false);
        //  if ($permission == false) {
        //      echo 'error';
        //  } else {
        $display_gallery = new Display_Gallery();

        //Remove image from Gallery if data-remove on a tag
        if (isset($_REQUEST['ft_gallery_img_remove']) && $_REQUEST['ft_gallery_img_remove'] == 'true') {
            $args = array('post' => null);
        }
        //$display_gallery->ft_gallery_delete_media_rest($_REQUEST['id']);
        $display_gallery->ft_gallery_update_media_rest($_REQUEST['id'], $args);
        echo 'Update success';
        //  }
        exit();
    }

    /**
     * FT Gallery Update Image Information AJAX
     *
     * Preload Image information to input fields in popup AJAX
     *
     * @since 1.0.0
     */
    function ft_gallery_update_image_information_ajax() {
        $permission = check_ajax_referer('ft_gallery_edit_image_nonce', 'nonce', false);
        if ($permission == false) {
            echo 'error';
        } else {
            $attachment_array = $this->ft_gallery_get_attachment_info($_REQUEST['id']);

            echo json_encode($attachment_array);
        }
        exit();
    }


    /**
     * FT Gallery Edit Image AJAX
     *
     * Edit image using AJAX
     *
     * @since 1.0.0
     */
    function ft_gallery_edit_image_ajax() {
        if (isset($_REQUEST['nonce']) && $_REQUEST['nonce'] !== 'attach_image') {
            $permission = check_ajax_referer('ft_gallery_edit_image_nonce', 'nonce', false);
            if ($permission == '') {
                echo 'error';
            } else {

                $img_title = isset($_REQUEST['title']) && !empty($_REQUEST['title']) ? $_REQUEST['title'] : '';
                $img_alttext = isset($_REQUEST['alttext']) && !empty($_REQUEST['alttext']) ? $_REQUEST['alttext'] : '';
                $img_description = isset($_REQUEST['description']) && !empty($_REQUEST['description']) ? $_REQUEST['description'] : '';

                //Set Parameters for image from Gallery
                $args = array(
                    'title' => $img_title,
                    'alt_text' => $img_alttext,
                    'description' => $img_description,
                );

                //$display_gallery->ft_gallery_delete_media_rest($_REQUEST['id']);
                $this->ft_gallery_update_media_rest($_REQUEST['id'], $args);
                echo 'success';
            }
        } else {
            $args = array('post' => $_REQUEST['postID'],);

            $this->ft_gallery_update_media_rest($_REQUEST['id'], $args);
            echo $_REQUEST['id'] . ' Image attached to this post';
        }
        exit();
    }

    /**
     * FT Gallery Update Title AJAX
     *
     * Add file name as title on image plupload
     *
     * @since 1.0.0
     */
    function ft_gallery_update_title_ajax() {

        $url = isset($_REQUEST['url']) && !empty($_REQUEST['url']) ? $_REQUEST['url'] : '';
        $get_image_id = $this->ft_gallery_get_attachment_id($url);

        $display_gallery = new Display_Gallery();

        //Set Parameters for image from Gallery
        $args = array(
            'title' => '',
        );

        $display_gallery->ft_gallery_update_media_rest($get_image_id, $args);
        echo $get_image_id;

        exit();
    }

    public function ftg_sort_order_select($ftg_id){
        $orderby_date = isset($_GET['orderby']) && 'date' === $_GET['orderby'] ? ' selected="selected"' : '';
        $orderby_alphabetical = isset($_GET['orderby']) && 'title' === $_GET['orderby'] ? ' selected="selected"' : '';
        $orderby_original = isset($_GET['orderby']) && 'menu_order' === $_GET['orderby'] ? ' selected="selected"' : '';

        $ftg_align_pagination = null !== get_post_meta($ftg_id, 'ftg_align_sort_select', true) ? get_post_meta($ftg_id, 'ftg_align_sort_select', true) : '';

        $align_class =  'right' === $ftg_align_pagination ? ' ftg-sort-order-right' : '';

        print '<div class="ftg-orderby-wrap'.$align_class.'"><form class="feed-them-gallery-ordering" method="get" ><select name="orderby" class="ftg-orderby" onchange="this.form.submit()">
					<option value="menu_order"'.$orderby_original.'>'. esc_html( 'Sort order of Images', 'feed-them-gallery' ).'</option>
					<option value="title"'.$orderby_alphabetical.'>'. esc_html( 'Sort alphabetically (A-Z)', 'feed-them-gallery' ).'</option>
					<option value="date"'.$orderby_date.'>'. esc_html( 'Sort by date', 'feed-them-gallery' ).'</option></select></form></div>';
    }


    public function ftg_pagination($ftg_id){

        $per_page = get_post_meta($ftg_id, 'ft_gallery_pagination_photo_count', true);

        $total_pagination_count = $this->ft_gallery_count_post_images($ftg_id);

        if( get_query_var('paged')){
            $count_per_page = min( $total_pagination_count, $per_page * get_query_var('paged') );
            $per_page_final = $count_per_page - $per_page;
        }
        else {
            $per_page_final = '1';
            $count_per_page = get_post_meta($ftg_id, 'ft_gallery_pagination_photo_count', true);
        }

        $ftg_align_pagination = null !== get_post_meta($ftg_id, 'ftg_align_pagination', true) ? get_post_meta($ftg_id, 'ftg_align_pagination', true) : '';
        $ftg_align_count = null !== get_post_meta($ftg_id, 'ftg_align_count', true) ? get_post_meta($ftg_id, 'ftg_align_count', true) : '';
        $ftg_display_count = null !== get_post_meta($ftg_id, 'ftg_display_image_count', true) ? get_post_meta($ftg_id, 'ftg_display_image_count', true) : '';

        $ft_gallery_pagination_text_color = get_post_meta($ftg_id, 'ft_gallery_pagination_text_color', true)  ? '.ftg-pagination .page-numbers{color:'.get_post_meta($ftg_id, 'ft_gallery_pagination_text_color', true).'!important;}' : '';
        $ft_gallery_pagination_button_color = get_post_meta($ftg_id, 'ft_gallery_pagination_button_color', true)  ? '.ftg-pagination a.page-numbers{background:'.get_post_meta($ftg_id, 'ft_gallery_pagination_button_color', true).'!important;}' : '';
        $ft_gallery_pagination_active_button_color = get_post_meta($ftg_id, 'ft_gallery_pagination_active_button_color', true)  ? '.ftg-pagination .page-numbers.current{background:'.get_post_meta($ftg_id, 'ft_gallery_pagination_active_button_color', true).'!important;}' : '';

        if('' !== $ft_gallery_pagination_text_color || '' !== $ft_gallery_pagination_button_color || '' !== $ft_gallery_pagination_active_button_color ){
            // FINISH CONVERTING THE PAGINATION STYLES TO SHOW... I NEED TO MOVE THIS TO STYLES IN HEADER I THINK... I DON'T SEE INLINE STYLE OPTIONS FOR https://developer.wordpress.org/reference/functions/paginate_links/
            print '<style>'.$ft_gallery_pagination_text_color.$ft_gallery_pagination_button_color.$ft_gallery_pagination_active_button_color.'</style>';
        }


        $align_class =  'left' === $ftg_align_pagination ? ' ftg-page-left' : '';

        $align_count_class =  'right' === $ftg_align_count ? ' ftg-total-page-count-align-right' : '';

        $ft_gallery_true_pagination_count_text_color = null !== get_post_meta($ftg_id, 'ft_gallery_true_pagination_count_text_color', true) ? ' style="color:'.get_post_meta($ftg_id, 'ft_gallery_true_pagination_count_text_color', true).'"' : '';
        $page_count = 'yes' === $ftg_display_count ? '<div class="ftg-total-pagination-count'.$align_count_class.'"'.$ft_gallery_true_pagination_count_text_color.'>'. esc_html( 'Showing', 'feed-them-gallery' ).' '.$per_page_final . '-'. $count_per_page .'  of '.$total_pagination_count . ' '. esc_html( 'Images', 'feed-them-gallery' ).'</div>' : '';


        if('left' === $ftg_align_pagination) {
            print $page_count;
        }

        print '<div class="ftg-pagination'.$align_class.'">';

        print paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'mid_size' => 3,
            'end_size' => 3,
            'prev_text' => __('&#10094;'),
            'next_text' => __('&#10095;'),
            'total' => ceil(esc_html( $total_pagination_count ) / esc_html( $per_page ) ) // 3 items per page
        ));
        print '</div>';
         if('right' === $ftg_align_pagination) {
            print $page_count;
        }
        print '<div class="clear"></div>';
    }

    /**
     *
     * FT Gallery Display Gallery Shortcode
     *
     * Create shortcode to display shortcode
     *
     * @param $atts
     * @return string
     * @since 1.0.0
     */
    public function ft_gallery_display_gallery_shortcode($atts) {
        $ftg = shortcode_atts(array(
            //All We need is ID of Gallery Post the rest will be passed through a rest call
            'id' => '',
            'offset' => '',
            'media_count' => '',
            'is_album' => ''
        ), $atts);


        $album_gallery_ids = get_post_meta($ftg['id'], 'ft_gallery_album_gallery_ids', true);

        if (get_post_meta($ftg['id'], 'ft_popup_display_options', true) == 'full-width-second-half-bottom' || get_post_meta($ftg['id'], 'ft_popup_display_options', true) == 'full-width-photo-only') {
            ?>
            <style>
                <?php
                if(get_post_meta($ftg['id'], 'ft_popup_display_options', true) == 'full-width-second-half-bottom'){
                ?>
                @media (min-width: 0px) {
                    .ft-gallery-popup .fts-popup-second-half.fts-instagram-popup-second-half {
                        float: left !important
                    }

                    .ft-gallery-popup .fts-popup-second-half {
                        height: 100% !important;
                        width: 100% !important;
                        position: relative !important;
                        float: left !important;
                    }

                    .ft-gallery-popup .fts-popup-half {
                        background: #000 !important;
                        text-align: center !important;
                        vertical-align: middle !important;
                        z-index: 500 !important;
                        width: 100% !important;
                    }

                    .ft-gallery-popup .mfp-bottom-bar {
                        background: #FFF;
                        padding-bottom: 10px
                    }

                    .ft-gallery-popup .mfp-iframe-holder .mfp-content {
                        top: 0
                    }

                    .ft-gallery-popup .mfp-iframe-holder .fts-popup-image-position {
                        height: auto !important
                    }

                    .ft-gallery-popup .mfp-container {
                        padding-top: 40px;
                        padding-bottom: 0
                    }

                    .ft-gallery-popup .mfp-container:before {
                        display: none
                    }

                    .fts-popup-image-position {
                        min-height: 50px !important
                    }

                    .ft-gallery-popup .fts-popup-second-half .mfp-bottom-bar {
                        height: auto !important;
                        overflow: visible !important;
                        min-height: auto !important
                    }
                }

                <?php }
                elseif(get_post_meta($ftg['id'], 'ft_popup_display_options', true) == 'full-width-photo-only'){?>
                @media (min-width: 0px) {
                    .ft-gallery-popup .fts-popup-half {
                        background: #000 !important;
                        text-align: center !important;
                        vertical-align: middle !important;
                        z-index: 500 !important;
                        width: 100% !important;
                    }

                    .ft-gallery-popup .mfp-container:before {
                        display: inline-block;
                    }

                    .ft-gallery-popup .fts-popup-second-half {
                        height: 100%;
                        width: 100%;
                        position: relative;
                        float: left;
                    }

                    .ft-gallery-popup .mfp-container {
                        padding-top: 40px;
                        padding-bottom: 0;
                    }

                    .ft-gallery-popup .fts-popup-second-half .mfp-bottom-bar {
                        height: auto !important;
                        overflow: visible !important;
                        min-height: auto !important;
                    }

                    .ft-gallery-popup .mfp-bottom-bar {
                        background: #FFF;
                        padding-bottom: 10px;
                    }

                    .ft-gallery-popup .fts-popup-second-half {
                        display: none !important;
                    }
                }

                <?php } ?>
            </style>

            <?php
        }
        $feed_type = 'wp_gallery';





        // format types are: post, post-in-grid, gallery
        $format_type = get_post_meta($ftg['id'], 'ft_gallery_type', true);
        //Make sure it's not ajaxing
        if (!isset($_GET['load_more_ajaxing'])) {
            $_REQUEST['ft_gallery_dynamic_name'] = trim($this->ft_gallery_rand_string(10));
            //Create Dynamic Class Name
            $fts_dynamic_class_name = '';
            if (isset($_REQUEST['ft_gallery_dynamic_name'])) {
                $fts_dynamic_class_name = 'feed_dynamic_class' . $_REQUEST['ft_gallery_dynamic_name'];
            }
        }
        $ft_gallery_dynamic_string = trim($this->ft_gallery_rand_string(10));

        if(!empty(get_post_meta($ftg['id'], 'ft_gallery_load_more_option', true)) && 'yes' === get_post_meta($ftg['id'], 'ft_gallery_load_more_option', true)){
            $post_count = get_post_meta($ftg['id'], 'ft_gallery_photo_count', true) == TRUE ? get_post_meta($ftg['id'], 'ft_gallery_photo_count', true) : '999';
        }
        elseif(!empty(get_post_meta($ftg['id'], 'ft_gallery_show_true_pagination', true)) && 'yes' === get_post_meta($ftg['id'], 'ft_gallery_show_true_pagination', true)){
            $post_count = get_post_meta($ftg['id'], 'ft_gallery_pagination_photo_count', true) == TRUE ? get_post_meta($ftg['id'], 'ft_gallery_pagination_photo_count', true) : '999';
        }
        else {
            $post_count = '9999';
        }

        $scrollMore = get_post_meta($ftg['id'], 'ft_gallery_load_more_style', true);

        $loadmore_btn_maxwidth = get_post_meta($ftg['id'], 'ft_gallery_loadmore_button_width', true);
        $loadmore_btn_margin = get_post_meta($ftg['id'], 'ft_gallery_loadmore_button_margin', true);

        $loadmore_btn_maxwidth = isset($loadmore_btn_maxwidth) ? $loadmore_btn_maxwidth : '350px';
        $loadmore_btn_margin = isset($loadmore_btn_margin) ? $loadmore_btn_margin : '10px';


        $pagination = get_post_meta($ftg['id'], 'ft_gallery_show_pagination', true);

        $pagination = isset($pagination) ? $pagination : 'yes';

        // this is the image size in written format,ie* thumbnail, medium, large etc.
        //$show_title = get_post_meta($object->ID, 'ft_gallery_show_title', true);

        $title_description = get_post_meta($ftg['id'], 'ft_gallery_photo_caption', true);
        $words = get_post_meta($ftg['id'], 'ft_gallery_word_count_option', true);
        $more = '... read more';
        $stack_animation = 'no';
        $feed_name_rand_string = 'ft_gallery_' . $this->ft_gallery_rand_string(10);
        $padding = get_post_meta($ftg['id'], 'ft_gallery_padding', true);
        // $padding = '0';
        // $background_color = 'none';

        if (get_post_meta($ftg['id'], 'ft_gallery_type', true) == 'post' || get_post_meta($ftg['id'], 'ft_gallery_type', true) == 'gallery') {
            $height = get_post_meta($ftg['id'], 'ft_gallery_height', true);
        } else {
            $height = 'auto';
        }
        $mashup_margin = 'auto';
        $center_container = 'yes';
        $wrapper_margin = get_post_meta($ftg['id'], 'ft_gallery_margin', true);
        $space_between_photos = get_post_meta($ftg['id'], 'ft_gallery_grid_space_between_posts', true);
        $background_color = get_post_meta($ftg['id'], 'ft_gallery_feed_background_color', true);
        $border_bottom_color = get_post_meta($ftg['id'], 'ft_gallery_border_bottom_color', true);
        $background_color_grid_posts = get_post_meta($ftg['id'], 'ft_gallery_grid_posts_background_color', true);

        //  $space_between_photos = '10px';
        // $image_size = '';
        $image_size = get_post_meta($ftg['id'], 'ft_gallery_max_image_vid_width', true);





        $ft_gallery_columns_masonry2 = null !== get_post_meta($ftg['id'], 'ft_gallery_columns_masonry2', true) ? get_post_meta($ftg['id'], 'ft_gallery_columns_masonry2', true) : '';

        if(empty($ft_gallery_columns_masonry2)){
            $masonry_class = 'ftg-masonry-3-column';
        }
        elseif( '2' === $ft_gallery_columns_masonry2){
            $masonry_class = 'ftg-masonry-2-column';
        }
        elseif( '3' === $ft_gallery_columns_masonry2){
            $masonry_class = 'ftg-masonry-3-column';
        }
        elseif( '4' === $ft_gallery_columns_masonry2){
            $masonry_class = 'ftg-masonry-4-column';
        }
        elseif( '5' === $ft_gallery_columns_masonry2){
            $masonry_class = 'ftg-masonry-5-column';
        }
        else {
            // leaving this else for people who may already have had a size set, however when they resave on the page it will convert to the new method.
            // I'm forcing this because in this time of mobile devices allowing people to set a size just ruins that experience. and creates more support.
            $grid_width = null !== get_post_meta($ftg['id'], 'ft_gallery_grid_column_width', true) ? get_post_meta($ftg['id'], 'ft_gallery_grid_column_width', true) : '';
        }

        $ft_gallery_columns_masonry_margin = null !== get_post_meta($ftg['id'], 'ft_gallery_columns_masonry_margin', true) ? get_post_meta($ftg['id'], 'ft_gallery_columns_masonry_margin', true) : '';
        // we leave a space before each class to separate it from the class ftg-masonry above.
        if(empty($ft_gallery_columns_masonry_margin)){
            $masonry_margin = ' ftg-masonry-5px-margin';
        }
        elseif( '1' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-1px-margin';
        }
        elseif( '2' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-2px-margin';
        }
        elseif( '3' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-3px-margin';
        }
        elseif( '4' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-4px-margin';
        }
        elseif( '5' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-5px-margin';
        }
        elseif( '10' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-10px-margin';
        }
        elseif( '15' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-15px-margin';
        }
        elseif( '20' === $ft_gallery_columns_masonry_margin){
            $masonry_margin = ' ftg-masonry-20px-margin';
        }


        if( get_query_var('paged')){
            $count_per_page = min( $total_pagination_count, $per_page * get_query_var('paged') );
            $per_page_final = $count_per_page - $per_page;
        }
        else {
            $per_page_final = '1';
            $count_per_page = get_post_meta($ftg_id, 'ft_gallery_pagination_photo_count', true);
        }



        $feed_width = get_post_meta($ftg['id'], 'ft_gallery_width', true);

        // We use this to activate the watermark options, so they are turned off by default.
        $ft_gallery_watermark_enable_options = get_post_meta($ftg['id'], 'ft_gallery_watermark_enable_options', true);

        // Watermark Type
        // 2 options: Watermark Overlay Image (Does not Imprint logo on Image) = overlay / Watermark Image (Imprint logo on the selected image sizes) = imprint
        $watermark = get_post_meta($ftg['id'], 'ft_gallery_watermark', true);

        // 3 options: watermark in popup = popup-only / Watermark for image on page and popup = page-and-popup / Watermark for image on page = page-only
        $watermark_overlay_enable = get_post_meta($ftg['id'], 'ft_gallery_watermark_overlay_enable', true);

        $watermark_image_position = get_post_meta($ftg['id'], 'ft_gallery_position', true);
        $watermark_image_margin = get_post_meta($ftg['id'], 'ft_watermark_image_margin', true);
        $watermark_image_url = get_post_meta($ftg['id'], 'ft_watermark_image_input', true);
        $watermark_image_opacity = get_post_meta($ftg['id'], 'ft_watermark_image_opacity', true);
        $watermark_right_click_disable = get_post_meta($ftg['id'], 'ft_gallery_watermark_disable_right_click', true);

        // Option to disable the right click to inspect element on page so people can't just steal image easily
        if (isset($watermark_right_click_disable) && 'yes' === $watermark_right_click_disable) { ?>
            <script>
                jQuery(document).bind("contextmenu", function (event) {
                    event.preventDefault();
                });
                // window.ondragstart = function() { return false; }
                jQuery(document).ready(function () {
                    jQuery('img').on('dragstart', function (event) {
                        event.preventDefault();
                    });
                });
            </script>
        <?php }


        $edit_url = get_admin_url() . 'post.php?post=' . $ftg['id'] . '&action=edit';
        $popup = get_post_meta($ftg['id'], 'ft_gallery_popup', true);
        //   $popup = 'yes';
        if (isset($popup) && 'yes' === $popup) {
            // it's ok if these styles & scripts load at the bottom of the page
            $fts_fix_magnific = get_option('ft_gallery_fix_magnific') ? get_option('ft_gallery_fix_magnific') : '';
            if (isset($fts_fix_magnific) && '1' !== $fts_fix_magnific ) {
                wp_enqueue_style('ft-gallery-popup', plugins_url('feed-them-gallery/includes/feeds/css/magnific-popup.css'), array(), FTG_CURRENT_VERSION);
            }
            if (is_plugin_active('feed-them-premium/feed-them-premium.php')) {
                // run our magnific popup.js in fts instead of double loading
            } else {
                wp_enqueue_script('ft-gallery-popup-js', plugins_url('feed-them-gallery/includes/feeds/js/magnific-popup.js'), array(), FTG_CURRENT_VERSION);
            }
            // here is the click function for our custom popup
            wp_enqueue_script('ft-gallery-popup-click-js', plugins_url('feed-them-gallery/includes/feeds/js/magnific-popup-click.js'), array(), FTG_CURRENT_VERSION);
        }


        $hide_icon = get_post_meta($ftg['id'], 'ft_gallery_wp_icon', true);
        $hide_date = get_post_meta($ftg['id'], 'ft_gallery_wp_date', true);
        $show_share = get_post_meta($ftg['id'], 'ft_gallery_wp_share', true);
        $show_purchase_link = get_post_meta($ftg['id'], 'ft_gallery_purchase_link', true);

        if(null !== get_post_meta($ftg['id'], 'ft_gallery_purchase_word', true)){
            $purchase_text = get_post_meta($ftg['id'], 'ft_gallery_purchase_word', true);
        }
        else {
            $purchase_text = 'Purchase';
        }

        $username = null !== get_post_meta($ftg['id'], 'ft_gallery_username', true) ? get_post_meta($ftg['id'], 'ft_gallery_username', true) : '';
        $username_link = null !== get_post_meta($ftg['id'], 'ft_gallery_user_link', true) ? get_post_meta($ftg['id'], 'ft_gallery_user_link', true) : 'javacript:;';
        // link target options are: _blank, _self
        $link_target = '_blank';
        ob_start();
        if (isset($title)) {
            ?>
            <div class="ft-gallery-main-title"><?php print $title ?></div>
        <?php }

        $ftg_sorting_options = null !== get_post_meta($ftg['id'], 'ftg_sorting_options', true) && 'yes' === get_post_meta($ftg['id'], 'ftg_sorting_options', true)  ? get_post_meta($ftg['id'], 'ftg_sorting_options', true) : '';
        $ft_gallery_pagination_photo_count = null !== get_post_meta($ftg['id'], 'ft_gallery_pagination_photo_count', true) ? get_post_meta($ftg['id'], 'ft_gallery_pagination_photo_count', true) : '50';
        $ftg_loadmore_option = null !== get_post_meta($ftg['id'], 'ft_gallery_load_more_option', true) && 'yes' === get_post_meta($ftg['id'], 'ft_gallery_load_more_option', true)  ? get_post_meta($ftg['id'], 'ft_gallery_load_more_option', true) : '';
        $ftg_photo_count = null !== get_post_meta($ftg['id'], 'ft_gallery_photo_count', true) ? get_post_meta($ftg['id'], 'ft_gallery_photo_count', true) : '50';
        $ft_gallery_show_true_pagination = null !== get_post_meta($ftg['id'], 'ft_gallery_show_true_pagination', true) && 'yes' === get_post_meta($ftg['id'], 'ft_gallery_show_true_pagination', true)  ? get_post_meta($ftg['id'], 'ft_gallery_show_true_pagination', true) : '';


        if('yes' === $ftg_loadmore_option){
            $post_count = $ftg_photo_count;
        }
        elseif('yes' === $ft_gallery_show_true_pagination){
            $post_count = $ft_gallery_pagination_photo_count;
        }
        else {
            $post_count = '9999';
        }

        // Opening Div for header pagination
        if('yes' === $ftg_sorting_options || 'yes' === $ft_gallery_show_true_pagination) {
            print '<div class="ftg-pagination-header">';
        }
            if('yes' === $ftg_sorting_options) {
                $ft_gallery_position_of_pagination = null !== get_post_meta($ftg['id'], 'ftg_position_of_sort_select', true) ? get_post_meta($ftg['id'], 'ftg_position_of_sort_select', true) : '';
                if('above-below' === $ft_gallery_position_of_pagination || 'above' === $ft_gallery_position_of_pagination) {
                    $this->ftg_sort_order_select( $ftg['id'] );
                }
            }

            if('yes' === $ft_gallery_show_true_pagination) {
                $ft_gallery_position_of_pagination = null !== get_post_meta($ftg['id'], 'ft_gallery_position_of_pagination', true) ? get_post_meta($ftg['id'], 'ft_gallery_position_of_pagination', true) : '';
                if('above-below' === $ft_gallery_position_of_pagination || 'above' === $ft_gallery_position_of_pagination) {
                    $this->ftg_pagination( $ftg['id'] );
                }
            }
        // End closing Div for header pagination
        if('yes' === $ftg_sorting_options || 'yes' === $ft_gallery_show_true_pagination) {
            print '</div><div class="ftg-clear"></div>';
        }



        //echo do_shortcode( '[ft-gallery-specific-cats-menu gallery_id='.$ftg['id'].' menu_title="Specific Gallery Categories"]' );

        $ft_gallery_loadmore_background_color = get_post_meta($ftg['id'], 'ft_gallery_loadmore_background_color', true);
        $ft_gallery_loadmore_text_color = get_post_meta($ftg['id'], 'ft_gallery_loadmore_text_color', true);
        $ft_gallery_pagination_text_color = get_post_meta($ftg['id'], 'ft_gallery_pagination_text_color', true);
        $feed_width = isset($feed_width) && $feed_width !== '' ? 'max-width:' . $feed_width . ';' : '';
        $mashup_margin = isset($mashup_margin) && $mashup_margin !== '' ? 'margin:' . $mashup_margin . ';' : '';
        $height = isset($height) && $height !== '' ? 'height:' . $height . ';overflow:auto;' : '';
        $padding = isset($padding) && $padding !== '' ? 'padding:' . $padding . ';' : '';
        $background_color = isset($background_color) && $background_color !== '' ? 'background:' . $background_color . ';' : '';
        $background_color_grid_posts = isset($background_color_grid_posts) && $background_color_grid_posts !== '' ? 'background:' . $background_color_grid_posts . ';' : '';

        $border_bottom_color = isset($border_bottom_color) && $border_bottom_color !== '' ? 'border-bottom:1px solid ' . $border_bottom_color . ';' : '';

        if ($feed_width !== '' || $mashup_margin !== '' || $height !== '' || $padding !== '' || $background_color !== '') {
            $style_start = 'style="';
            $style_end = '"';
        } else {
            $style_start = '';
            $style_end = '';
        }

        $fts_powered_text_options_settings = get_option('ft-gallery-powered-text-options-settings');

        if ($fts_powered_text_options_settings == '1') {
            ?>
            <script>jQuery('body').addClass('ft-gallery-powered-by-hide');</script><?php
        }
        //Make sure it's not ajaxing
        if (!isset($_GET['load_more_ajaxing'])) {
            // We have 3 wrapper options at the moment post, post-in-grid, gallery and gallery-collage
            if ($format_type == 'post-in-grid' || $format_type == 'gallery-collage') {
                print '<div class="ft-wp-gallery ft-wp-gallery-masonry popup-gallery-fb-posts ' . $feed_name_rand_string . ' ' . $fts_dynamic_class_name . ' masonry js-masonry"';
                if (isset($center_container) && $center_container == 'yes') {
                    print 'data-masonry-options=\'{"itemSelector": ".ft-gallery-post-wrap", "isFitWidth": ' . ($center_container == 'no' ? 'false' : 'true') . ' ' . ($stack_animation == 'no' ? ', "transitionDuration": 0' : '') . '}\' style="margin:auto;'. $background_color .'"';
                }
                print '>';
            } elseif ($format_type == 'gallery') {
                $scrollable = isset($scrollMore) && $scrollMore == 'autoscroll' ? $feed_name_rand_string.'-scrollable ft-wp-gallery-scrollable' : '';

                $ft_gallery_columns = get_post_meta($ftg['id'], 'ft_gallery_columns', true);
                $ft_gallery_force_columns = get_post_meta($ftg['id'], 'ft_gallery_force_columns', true);
                $columns = isset($ft_gallery_columns) ? 'data-ftg-columns="'.$ft_gallery_columns.'" ' : '';
                $force_columns = isset($ft_gallery_force_columns) ? 'data-ftg-force-columns="'.$ft_gallery_force_columns.'" ' : '';

                print '<div '. $columns . $force_columns .'data-ftg-margin='.$space_between_photos.' data-ftg-width="'.$grid_width.'" class="fts-mashup ft-wp-gallery-centered ft-wp-gallery popup-gallery-fb-posts ' . $feed_name_rand_string .' '.$scrollable.'" ' . $style_start . $feed_width . $mashup_margin . $height . $padding . $background_color . $style_end . '>';
                print '<div class="' . $fts_dynamic_class_name . '">';

            } elseif ($format_type == 'post') {
                print '<div class="fts-mashup ft-wp-gallery popup-gallery-fb-posts ' . $feed_name_rand_string . ' ' . $fts_dynamic_class_name . '" ' . $style_start . $feed_width . $mashup_margin . $height . $padding . $background_color . $style_end . '>';
            }
        }

        //$show_title = get_post_meta($object->ID, 'ft_gallery_show_title', true);
        ?><?php


        if(isset($ftg['is_album']) && 'yes' === $ftg['is_album']){

            $image_list = get_post_meta($ftg['id'], 'ft_gallery_album_gallery_ids', true);
           // echo '<pre>';
           // print_r($image_list);
           // echo '</pre>';


        }else {
            $orderby_set = null !== get_post_meta($ftg['id'], 'ftg_sort_type', true) ? get_post_meta($ftg['id'], 'ftg_sort_type', true) : 'menu_order' ;
            $orderby = isset($_GET['orderby']) ?  sanitize_text_field( $_GET['orderby'] ) : $orderby_set;
                if(isset($_GET['orderby']) && 'menu_order' === $_GET['orderby'] || 'menu_order'  === $orderby_set || 'title'  === $orderby_set  || isset($_GET['orderby']) && 'title' === $_GET['orderby']){
                    $order = 'asc';
                }
                else {
                    $order = 'desc';
                }
           // $paged = $ftg['offset'];
            if('yes' === $ft_gallery_show_true_pagination){
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            }
            else {
                $paged = $ftg['offset'];
            }
            $image_list = get_posts(array(
                'post_parent' => $ftg['id'],
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'posts_per_page' =>  esc_html( $post_count ),
                'orderby' => esc_html( $orderby ),
                'order' => esc_html( $order ),
                'paged'          => esc_html( $paged )
            ));

        }
        // echo '<pre>';
        //  print_r($image_list);
        //  echo '</pre>';
        if (is_array($image_list) && isset($image_list[0])) {


            if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')) {
                $gallery_to_woo = new Gallery_to_Woocommerce();
                $siteurl = get_option('get_bloginfo');
                $purchase_link = get_option('ft_gallery_woo_add_to_cart');
                $purchase_link_option = isset($purchase_link['ft_gallery_woo_options']) ? $purchase_link['ft_gallery_woo_options'] : '';
            }

            $ft_gallery_load_more_option = get_post_meta($ftg['id'], 'ft_gallery_load_more_option', true);

            //Make sure it's not ajaxing
            if (!isset($_GET['load_more_ajaxing']) && !isset($_REQUEST['fts_no_more_posts'])) {
                $ftg['offset'] = '0';
            }

            $gallery_class = new Gallery();
            $albums_class = new Albums();

            foreach ($image_list as $image) {

                $date = isset($image->post_date) ? $image->post_date : '';


                if(isset($ftg['is_album']) && 'yes' === $ftg['is_album']) {


                    $gallery_id = $image->ID;

                    $gallery_post_link = get_post_permalink($gallery_id);
                    $gallery_attachments_count = $albums_class->ft_gallery_count_gallery_attachments($gallery_id);
                    $gallery_title = get_the_title($gallery_id);


                    $attached_media = $gallery_class->ft_gallery_get_gallery_attached_media_ids( $image->ID , 'image' );
                    $featured = $albums_class->gallery_featured_first($image->ID, true);
                     if(isset($featured)){
                         $featured_image = $featured;
                         // print_r($featured);
                    }

                    $image = wp_prepare_attachment_for_js( $attached_media[0]);

                }
                else {

                    // very interesting function that returns some detailed info.
                    // found @: https://wordpress.org/ideas/topic/functions-to-get-an-attachments-caption-title-alt-description
                    $image = wp_prepare_attachment_for_js( $image->ID );
                }




                $description = make_clickable( $image['description'] );
                $img_title = $image['title'];

                // Going to remove this option for the time being, since unlike facebook we don't have another page to take them to with the rest of the description
                // $image_description = $this->ft_gallery_trim_words(isset($description) ? $description : '', $words, $more);
                $image_description = isset($description) ? $description : '';

                //Social media sharing URLs
                $link = get_permalink();
                $ft_gallery_share_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . $link;
                $ft_gallery_share_twitter = 'https://twitter.com/intent/tweet?text=' . $link;
                $ft_gallery_share_google = 'https://plus.google.com/share?url=' . $link;
                $ft_gallery_share_linkedin = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $link . '&title=' . strip_tags($image_description);
                $ft_gallery_share_email = 'mailto:?subject=Shared Link&body=' . $link . ' - ' . strip_tags($image_description);

                $fts_final_date = $this->ft_gallery_custom_date($date, $feed_type);
                $instagram_date = $fts_final_date;
                // date_i18n( get_option( 'date_format' ), strtotime( '11/15-1976' ) );

                // All text for img(s) on the page, this does not apply to image background gallery types
                $ft_gallery_alt_text = $image['alt'] == true ? $image['alt'] : $img_title;

                // The size of the image in the popup
                $image_size_name = get_post_meta($ftg['id'], 'ft_gallery_images_sizes_popup', true);
                // this is the image size in written format,ie* thumbnail, medium, large etc.
                $item_popup = explode(" ", $image_size_name);
                $item_final_popup = wp_get_attachment_image_src($attachment_id = $image['id'], $item_popup[0], false);

                // The size of the image on the page (some people might not want the full source on the page because that is a lot of weight so we let them choose)
                $image_size_page = get_post_meta($ftg['id'], 'ft_gallery_images_sizes_page', true);
                // this is the image size in written format,ie* thumbnail, medium, large etc.
                $item_page = explode(" ", $image_size_page);
                $item_final_page = wp_get_attachment_image_src($attachment_id = $image['id'], $item_page[0], false);

                //  echo '<pre>';
                //  print_r($item_final_popup);
                //  echo '</pre>';


                $image_source_full = wp_get_attachment_image_src($attachment_id = $image['id'], 'full', false);

                $image_source_large = wp_get_attachment_image_src($attachment_id = $image['id'], 'large', false);
                $image_source_medium_large = wp_get_attachment_image_src($attachment_id = $image['id'], 'medium_large', false);
                $image_source_medium = wp_get_attachment_image_src($attachment_id = $image['id'], 'medium', false);

                if (isset($image_size_page) && $image_size_page !== 'Choose an option') {
                    $image_source_page = $item_final_page[0];
                } elseif (isset($image_size_page) && isset($image_source_large)) {
                    $image_source_page = $image_source_large[0];
                } elseif (isset($image_size_page) && isset($image_source_medium_large)) {
                    $image_source_page = $image_source_medium_large[0];
                } elseif (isset($image_size_page) && isset($image_source_medium)) {
                    $image_source_page = $image_source_medium[0];
                } else {
                    $image_source_page = '';
                }


                if(isset($ftg['is_album'], $featured_image) && 'yes' === $ftg['is_album']) {

                    $image_source_page = $featured_image;

                }



                if (isset($popup) && 'yes' === $popup) {

                    if (isset($image_size_name) && $image_size_page !== 'Choose an option') {
                        $image_source_popup = $item_final_popup[0];
                    } elseif (isset($image_source_large)) {
                        $image_source_popup = $image_source_large[0];
                    } elseif (isset($image_source_medium_large)) {
                        $image_source_popup = $image_source_medium_large[0];
                    } elseif (isset($image_source_medium)) {
                        $image_source_popup = $image_source_medium[0];
                    } else {
                        $image_source_popup = '';
                    }
                }

                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php')){
                    //Check custom post meta for woo product field
                    $productID = get_post_meta($image['id'], 'ft_gallery_woo_prod', true);
                }

                // Regular Post Format
                if ($format_type == 'post' || $format_type == 'post-in-grid' || $format_type == 'gallery-collage') {
                    ?>
                    <div class="ft-gallery-post-wrap fts-feed-type-wp_gallery ft-post-format <?php echo $masonry_class . $masonry_margin; if ($format_type == 'gallery-collage') { ?> ft-gallery-collage<?php } ?> ft-gallery-<?php echo $ft_gallery_dynamic_string ?>" style="<?php if ($format_type == 'post-in-grid' || $format_type == 'gallery-collage') { ?>width:<?php print $grid_width ?>;margin:<?php print $masonry_margin ?>;<?php }
                    print $background_color_grid_posts ?>;<?php print $padding; ?>;<?php print $border_bottom_color ?>">

                        <div class="ft-text-for-popup" style="<?php if (isset($hide_icon) && $hide_icon == 'no' && isset($username) && $username == 'none' && isset($hide_date) && $hide_date == 'no' && isset($title_description) && $title_description == 'none' || $format_type == 'gallery-collage') { ?>display:none !important;<?php } ?>">
                            <div class="ft-text-for-popup-content">
                                <?php if (isset($hide_icon) && $hide_icon == 'yes') { ?>
                                    <div class="ft-gallery-icon-wrap-right fts-mashup-wp_gallery-icon ft-wp-gallery-icon">
                                        <a href="<?php print $username_link ?>" target="<?php print $link_target ?>"></a>
                                    </div>
                                <?php } ?>
                                <?php if (isset($username) && $username !== 'none') { ?>
                                    <span class="ft-gallery-fb-user-name"><a href="<?php print $username_link ?>" target="<?php print $link_target ?>"><?php print $username ?></a></span>
                                <?php } ?>
                                <?php if (isset($hide_date) && $hide_date == 'yes') { ?>
                                    <span class="ft-gallery-post-time"><?php print $instagram_date ?></span>
                                <?php } ?>

                                <div class="ft-gallery-description-wrap">
                                    <?php if ($title_description == 'title' || $title_description == 'title_description') { ?>
                                        <p><strong><?php print $img_title ?></strong>
                                        </p><?php } ?><?php if ($title_description == 'description' || $title_description == 'title_description') { ?>
                                        <p><?php print $image_description ?></p><?php } ?>
                                </div>

                            </div>
                        </div>
                        <div class="fts-mashup-image-and-video-wrap" <?php if (isset($image_size)){ ?>style="max-width: <?php print $image_size; ?>"<?php } ?>>

                            <a href="<?php print $image_source_popup ?>" class="ft-gallery-link-popup-master ft-gallery-link-popup-click-action" style="position: relative; overflow: hidden;"><img class="fts-mashup-instagram-photo " src="<?php print $image_source_page ?>" alt="<?php print $ft_gallery_alt_text ?>"><?php
                                if (isset($ft_gallery_watermark_enable_options) && $ft_gallery_watermark_enable_options == 'yes' && isset($watermark) && $watermark == 'overlay') {
                                    ?>
                                    <div class="<?php if (isset($watermark_overlay_enable) && $watermark_overlay_enable == 'popup-only') { ?>ft-image-overlay fts-image-overlay-hide<?php } elseif (isset($watermark_overlay_enable) && $watermark_overlay_enable == 'page-and-popup') { ?>ft-image-overlay<?php } ?>">
                                        <div class="fts-watermark-inside fts-watermark-inside-<?php echo $watermark_image_position ?>" <?php if (isset($watermark_image_opacity) && $watermark_image_opacity == true){ ?>style="opacity:<?php echo $watermark_image_opacity ?>"<?php } ?>>
                                            <img src="<?php print $watermark_image_url ?>" <?php if (isset($watermark_image_margin) && $watermark_image_margin == true){ ?>style="margin:<?php echo $watermark_image_margin ?>"<?php } ?> alt="<?php print $ft_gallery_alt_text ?>"/>
                                        </div>
                                    </div>
                                <?php } ?></a>

                        </div>
                        <?php print ' <div class="ftg-varation-for-popup">';
                        if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php') && isset($productID) && $productID !== '') {

                            // Get $product object from product ID
                            $this->ftg_variable_add_to_cart($productID);
                        }
                        print '</div>';

                        $free_image_size = get_post_meta($ftg['id'], 'ftg_free_download_size', true) ? get_post_meta($ftg['id'], 'ftg_free_download_size', true) : '';
                        if ($show_share == 'yes' ||  is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php') && isset($productID) && $productID !== '' && $show_purchase_link == 'yes') { ?>

                            <div class="fts-mashup-count-wrap">

                                <?php if ($show_share == 'yes') { ?>
                                    <div class="fts-share-wrap">
                                        <a href="javascript:;" class="ft-gallery-link-popup"></a>
                                        <div class='ft-gallery-share-wrap'>
                                            <a href='<?php print $ft_gallery_share_facebook ?>' target='_blank' class='ft-galleryfacebook-icon'><i class='fa fa-facebook-square'></i></a>
                                            <a href='<?php print $ft_gallery_share_twitter ?>' target='_blank' class='ft-gallerytwitter-icon'><i class='fa fa-twitter'></i></a>
                                            <a href='<?php print $ft_gallery_share_google ?>' target='_blank' class='ft-gallerygoogle-icon'><i class='fa fa-google-plus'></i></a>
                                            <a href='<?php print $ft_gallery_share_linkedin ?>' target='_blank' class='ft-gallerylinkedin-icon'><i class='fa fa-linkedin'></i></a>
                                            <a href='<?php print $ft_gallery_share_email ?>' target='_blank' class='ft-galleryemail-icon'><i class='fa fa-envelope'></i></a>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="ft-gallery-cta-button-wrap">
                                    <?php
                                    if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php') && isset($productID) && $productID !== ''  && $show_purchase_link == 'yes') {


                                        // Check to see if we are working with a variable product and if so make the purchase link go to cart
                                        $product = wc_get_product($productID);

                                        if ($product->get_type('variable') == 'variable') {
                                            $purchase_link = '' . $siteurl . '/cart';
                                        } else {
                                            if ($purchase_link_option == 'prod_page') {
                                                $purchase_link = '' . $siteurl . '/product/?p=' . $productID . '';
                                            } elseif ($purchase_link_option == 'add_cart') {
                                                $purchase_link = '' . $siteurl . '/?add-to-cart=' . $productID . '';
                                            } elseif ($purchase_link_option == 'add_cart_checkout') {
                                                $purchase_link = '' . $siteurl . '/cart/?add-to-cart=' . $productID . '';
                                            } elseif ($purchase_link_option == 'cart_checkout') {
                                                $purchase_link = '' . $siteurl . '/cart';
                                            } else {
                                                $purchase_link = '' . $siteurl . '/product/?p=' . $productID . '';
                                            }
                                        }

                                        //If Image already has product meta check the product still exists
                                        if (!empty($productID)) {
                                            $product_exist = $gallery_to_woo->ft_gallery_create_woo_prod_exists_check($productID);
                                            if ($product_exist) {
                                                echo '<a class="ft-gallery-buy-now ft-gallery-link-popup-master" href="' . $purchase_link . '" ">' . $purchase_text . '</a>';
                                            }
                                        }
                                    } // end if woo active and product ID set

                                if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && !empty($free_image_size) && 'Choose an option' !== $free_image_size) {
                                    // this is the image size in written format,ie* thumbnail, medium, large etc.
                                    $item_page_free = explode(" ", $free_image_size);
                                    $free_image_url = wp_get_attachment_image_src($attachment_id = $image['id'], $item_page_free[0], false);
                                    print '<a href="'.$free_image_url[0].'" download class="ft-gallery-download noLightbox"></a>';
                                }
                                ?>
                                </div>
                            </div>
                            <div class="clear"></div>
                        <?php } ?>

                    </div>
                <?php } // Instagram Style Post Format "Image gallery squared"
                elseif ($format_type == 'gallery') {
                    ?>
                    <div class='fts-feed-type-wp_gallery slicker-ft-gallery-placeholder ft-gallery-wrapper ft-gallery-<?php echo $ft_gallery_dynamic_string ?>' style='background-image:url(<?php print $image_source_page ?>);width:<?php print $grid_width ?>;height:<?php print $grid_width ?>;margin:<?php print $space_between_photos ?>;'>

                       <?php


                       if(isset($ftg['is_album']) & 'yes' === $ftg['is_album'] ) {

                       ?><div class="ft-album-contents"><?php
                            echo $gallery_title . ' (' .$gallery_attachments_count .')';
                            ?></div>

                        <a href="<?php  print $gallery_post_link ?>" title='<?php print $ft_gallery_alt_text ?>' class="ft-gallery-link-popup-master ft-gallery-link-popup-click-action ft-view-photo"></a>

                        <?php }



                        if (isset($popup) && 'yes' === $popup){ ?>
                        <div class='slicker-instaG-backg-link'>
                            <div class="ft-text-for-popup">
                                <div class="ft-text-for-popup-content">
                                    <?php if ($hide_icon == 'yes') { ?>
                                        <div class="ft-gallery-icon-wrap-right fts-mashup-wp_gallery-icon ft-wp-gallery-icon">
                                            <a href="<?php print $username_link ?>" target="<?php print $link_target ?>"></a>
                                        </div>
                                    <?php } ?>

                                    <?php if ($username !== 'none') { ?>
                                        <span class="ft-gallery-fb-user-name"><a href="<?php print $username_link ?>" target="<?php print $link_target ?>"><?php print $username ?></a></span>
                                    <?php } ?>

                                    <?php if ($hide_date == 'yes') { ?>
                                        <span class="ft-gallery-post-time"><?php print $instagram_date ?></span>
                                    <?php } ?>
                                    <div class="ft-gallery-description-wrap">
                                        <?php if ($title_description == 'title' || $title_description == 'title_description') { ?>
                                            <p><strong><?php print $img_title ?></strong>
                                            </p><?php } ?><?php if ($title_description == 'description' || $title_description == 'title_description') { ?>
                                            <p><?php print $image_description ?></p><?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php print $image_source_popup; ?>" title='<?php print $ft_gallery_alt_text ?>' class="ft-gallery-link-popup-master ft-gallery-link-popup-click-action ft-view-photo">
                            <?php } ?>
                            <?php if (isset($ft_gallery_watermark_enable_options) && $ft_gallery_watermark_enable_options == 'yes' && isset($watermark) && $watermark == 'overlay') {
                                ?>
                                <div class="<?php if (isset($watermark_overlay_enable) && $watermark_overlay_enable == 'popup-only') { ?>ft-image-overlay fts-image-overlay-hide<?php } elseif (isset($watermark_overlay_enable) && $watermark_overlay_enable == 'page-and-popup') { ?>ft-image-overlay<?php } ?>">
                                    <div class="fts-watermark-inside fts-watermark-inside-<?php echo $watermark_image_position ?>" <?php if (isset($watermark_image_opacity) && $watermark_image_opacity == true){ ?>style="opacity:<?php echo $watermark_image_opacity ?>"<?php } ?>>
                                        <img src="<?php print $watermark_image_url ?>" <?php if (isset($watermark_image_margin) && $watermark_image_margin == true){ ?>style="margin:<?php echo $watermark_image_margin ?>"<?php } ?> alt="<?php print $ft_gallery_alt_text ?>" />
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($popup) && 'yes' === $popup){
                            // Get $product object from product ID
                            ?>
                        </a>
                    <?php print ' <div class="ftg-varation-for-popup" style="display: none!important;">';
                    if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php') && isset($productID) && $productID !== '') {

                        // Get $product object from product ID
                        $this->ftg_variable_add_to_cart($productID);
                    }
                    print '</div>';

                    $free_image_size = get_post_meta($ftg['id'], 'ftg_free_download_size', true) ? get_post_meta($ftg['id'], 'ftg_free_download_size', true) : '';
                    if ($show_share == 'yes' || is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php') && isset($productID) && $productID !== '' && $show_purchase_link == 'yes') { ?>
                        <div class="fts-mashup-count-wrap">

                                <?php if ($show_share == 'yes') { ?>
                                    <div class="fts-share-wrap">
                                        <a href="javascript:;" class="ft-gallery-link-popup"></a>
                                        <div class='ft-gallery-share-wrap'>
                                            <a href='<?php print $ft_gallery_share_facebook ?>' target='_blank' class='ft-galleryfacebook-icon'><i class='fa fa-facebook-square'></i></a>
                                            <a href='<?php print $ft_gallery_share_twitter ?>' target='_blank' class='ft-gallerytwitter-icon'><i class='fa fa-twitter'></i></a>
                                            <a href='<?php print $ft_gallery_share_google ?>' target='_blank' class='ft-gallerygoogle-icon'><i class='fa fa-google-plus'></i></a>
                                            <a href='<?php print $ft_gallery_share_linkedin ?>' target='_blank' class='ft-gallerylinkedin-icon'><i class='fa fa-linkedin'></i></a>
                                            <a href='<?php print $ft_gallery_share_email ?>' target='_blank' class='ft-galleryemail-icon'><i class='fa fa-envelope'></i></a>
                                        </div>
                                    </div>
                                <?php } ?>

                        <div class="ft-gallery-cta-button-wrap">
                            <?php
                            if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php') && isset($productID) && $productID !== ''  && $show_purchase_link == 'yes') {


                                // Check to see if we are working with a variable product and if so make the purchase link go to cart
                                $product = wc_get_product($productID);

                                if ($product->get_type('variable') == 'variable') {
                                    $purchase_link = '' . $siteurl . '/cart';
                                } else {
                                    if ($purchase_link_option == 'prod_page') {
                                        $purchase_link = '' . $siteurl . '/product/?p=' . $productID . '';
                                    } elseif ($purchase_link_option == 'add_cart') {
                                        $purchase_link = '' . $siteurl . '/?add-to-cart=' . $productID . '';
                                    } elseif ($purchase_link_option == 'add_cart_checkout') {
                                        $purchase_link = '' . $siteurl . '/cart/?add-to-cart=' . $productID . '';
                                    } elseif ($purchase_link_option == 'cart_checkout') {
                                        $purchase_link = '' . $siteurl . '/cart';
                                    } else {
                                        $purchase_link = '' . $siteurl . '/product/?p=' . $productID . '';
                                    }
                                }

                                //If Image already has product meta check the product still exists
                                if (!empty($productID)) {
                                    $product_exist = $gallery_to_woo->ft_gallery_create_woo_prod_exists_check($productID);
                                    if ($product_exist) {
                                        echo '<a class="ft-gallery-buy-now ft-gallery-link-popup-master" href="' . $purchase_link . '" ">' . $purchase_text . '</a>';
                                    }
                                }
                            } // end if woo active and product ID set

                            if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && !empty($free_image_size) && 'Choose an option' !== $free_image_size) {
                                // this is the image size in written format,ie* thumbnail, medium, large etc.
                                $item_page_free = explode(" ", $free_image_size);
                                $free_image_url = wp_get_attachment_image_src($attachment_id = $image['id'], $item_page_free[0], false);
                                print '<a href="'.$free_image_url[0].'" download title="" class="ft-gallery-download noLightbox"></a>';
                            }
                            ?>
                        </div>
                        <div class="clear"></div>
                    <?php } ?>


                        <div class="clear"></div>
                    <?php } ?>
                    </div>
                    </div>
                    <?php
                }
                // END else is no reg post format

            }
            //END // We have 3 wrapper options at the moment post, post-in-grid, gallery and gallery-collage

            $offset = $ftg['offset'];


            if($ft_gallery_load_more_option == "yes"){

                //******************
                //Load More BUTTON Start
                //******************
                $_REQUEST['ft_gallery_offset'] = $offset;
                //Make sure it's not ajaxing
                if (!isset($_GET['load_more_ajaxing'])) {
                    $offset = 2;
                    $post_count = $post_count;
                } else {
                    $offset = 1 + $ftg['offset'];
                    $post_count = $post_count + $ftg['media_count'];
                }

                if ($post_count > $this->ft_gallery_count_post_images($ftg['id'])) {
                    $post_count = $this->ft_gallery_count_post_images($ftg['id']);
                }
                ?>
                <script>var ft_gallery_offset<?php echo $_REQUEST['ft_gallery_dynamic_name']; ?>= "<?php echo $offset ?>";
                    var ft_gallery_posts<?php echo $_REQUEST['ft_gallery_dynamic_name']; ?>= "<?php echo $post_count ?>";
                    jQuery('.ft-gallery-image-loaded-count').html(ft_gallery_posts<?php echo $_REQUEST['ft_gallery_dynamic_name']; ?>)</script><?php
                //Make sure it's not ajaxing
                if (!isset($_GET['load_more_ajaxing'])) {

                    $ft_gallery_dynamic_name = $_REQUEST['ft_gallery_dynamic_name'];
                    $time = time();
                    $nonce = wp_create_nonce($time . "load-more-nonce");
                    ?>
                    <script> jQuery(document).ready(function () {
                            <?php if ($scrollMore == 'autoscroll') { // this is where we do SCROLL function to LOADMORE if = autoscroll in shortcode ?>
                            jQuery(".<?php echo $feed_name_rand_string ?>-scrollable").bind("scroll", function () {
                                if (jQuery(this).scrollTop() + jQuery(this).innerHeight() >= jQuery(this)[0].scrollHeight) {
                                    <?php }
                                    else { // this is where we do CLICK function to LOADMORE if = button in shortcode ?>
                                    jQuery("#loadMore_<?php echo $ft_gallery_dynamic_name ?>").click(function () {
                                        <?php }
                                        $ft_gallery_bounce_color = isset($ft_gallery_loadmore_text_color) && $ft_gallery_loadmore_text_color == true ? ' style="background:' . $ft_gallery_loadmore_text_color . ';"' : '';
                                        ?>
                                        jQuery("#loadMore_<?php echo $ft_gallery_dynamic_name ?>").addClass('fts-fb-spinner');
                                        var button = jQuery('#loadMore_<?php echo $ft_gallery_dynamic_name ?>').html('<div class="bounce1"<?php echo $ft_gallery_bounce_color ?>></div><div class="bounce2"<?php echo $ft_gallery_bounce_color ?>></div><div class="bounce3"<?php echo $ft_gallery_bounce_color ?>></div>');
                                        console.log(button);

                                        var yes_ajax = "yes";
                                        var ft_gallery_id = "<?php echo esc_html( $ftg['id'] ); ?>";
                                        var ft_gallery_offset = ft_gallery_offset<?php echo sanitize_text_field( $_REQUEST['ft_gallery_dynamic_name'] ); ?>;
                                        var ft_gallery_post_count = ft_gallery_posts<?php echo sanitize_text_field( $_REQUEST['ft_gallery_dynamic_name'] ); ?>;
                                        var fts_security = "<?php echo esc_html( $nonce );?>";
                                        var fts_time = "<?php echo esc_html( $time );?>";
                                        var fts_d_name = "<?php echo esc_html( $ft_gallery_dynamic_name );?>";
                                        jQuery.ajax({
                                            data: {
                                                action: "ft_gallery_load_more",
                                                ft_gallery_id: ft_gallery_id,
                                                ft_gallery_offset: ft_gallery_offset,
                                                ft_gallery_media_count: ft_gallery_post_count,
                                                load_more_ajaxing: yes_ajax,
                                                fts_security: fts_security,
                                                fts_time: fts_time,
                                                ft_gallery_dynamic_name: fts_d_name
                                            },
                                            type: 'GET',
                                            url: "<?php echo admin_url('admin-ajax.php') ?>",
                                            success: function (data) {
                                                console.log('Well Done and got this from sever: ' + data);
                                                jQuery('.<?php echo $fts_dynamic_class_name ?>').append(data).filter('.<?php echo $fts_dynamic_class_name ?>').html();
                                                <?php if ($format_type == 'post-in-grid' || $format_type == 'gallery-collage') {  ?>
                                                jQuery('.<?php echo $fts_dynamic_class_name ?>').masonry('reloadItems');
                                                setTimeout(function () {
                                                    // Do something after 3 seconds
                                                    jQuery('.<?php echo $fts_dynamic_class_name ?>').masonry('layout');
                                                }, 500);
                                                <?php } ?>
                                                if (ft_gallery_posts<?php echo $_REQUEST['ft_gallery_dynamic_name']; ?> >=  <?php echo $this->ft_gallery_count_post_images($ftg['id']); ?>) {
                                                    jQuery('#loadMore_<?php echo $ft_gallery_dynamic_name ?>').replaceWith('<?php
                                                        print'<div style="';
                                                        if (isset($loadmore_btn_maxwidth) && $loadmore_btn_maxwidth !== '') {
                                                            print'max-width:' . $loadmore_btn_maxwidth . ';';
                                                        }
                                                        if (isset($ft_gallery_loadmore_background_color) && $ft_gallery_loadmore_background_color !== '') {
                                                            print'background:' . $ft_gallery_loadmore_background_color . ';';
                                                        }
                                                        if (isset($ft_gallery_loadmore_text_color) && $ft_gallery_loadmore_text_color !== '') {
                                                            print'color:' . $ft_gallery_loadmore_text_color . ';';
                                                        }
                                                        print'margin:' . $loadmore_btn_margin . ' auto ' . $loadmore_btn_margin . '" class="fts-fb-load-more">' . __('No More Photos', 'feed-them-gallery') . '</div>';
                                                        ?>'
                                                    );
                                                    //  jQuery('.ft-wp-gallery-scrollable').removeAttr('class');
                                                    jQuery('.<?php echo $feed_name_rand_string ?>-scrollable').unbind('scroll');
                                                }
                                                jQuery('#loadMore_<?php echo $ft_gallery_dynamic_name ?>').html('<?php _e('Load More', 'feed-them-gallery') ?>');
                                                jQuery("#loadMore_<?php echo $ft_gallery_dynamic_name ?>").removeClass('fts-fb-spinner');
                                                // Reload the share each funcion otherwise you can't open share option.
                                                jQuery.fn.ftsShare();
                                                // Reload this function again otherwise the popup won't work correctly for the newly loaded items
                                                jQuery.fn.slickWordpressPopUpFunction();
                                                <?php if (is_plugin_active('feed-them-gallery-premium/feed-them-gallery-premium.php') && is_plugin_active('woocommerce/woocommerce.php')) {?>
                                                jQuery.fn.ftg_apply_quant_btn();
                                                jQuery.getScript("/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart-variation.min.js");<?php } ?>

                                                <?php if ($format_type == 'gallery') { ?>if (jQuery("#ftg-gallery-demo").hasClass("ftg-demo-1")) {
                                                    outputSRmargin(document.querySelector('#margin').value)
                                                } // Reload our margin for the demo
                                                // Reload our imagesizing function so the images show up proper
                                                slickremixFTGalleryImageResizing();
                                                <?php } ?>
                                            }
                                        }); // end of ajax()
                                        return false;
                                        <?php // string $scrollMore is at top of this js script. acception for scroll option closing tag
                                        if ($scrollMore == 'autoscroll' ) { ?>
                                    }; // end of scroll ajax load.
                                    <?php } ?>
                                }
                                ); // end of document.ready
                        }); // end of form.submit
                    </script>
                    <?php
                }//End Check
                // main closing div not included in ajax check so we can close the wrap at all times

                print '</div>'; // closing main div for photos and scroll wrap


                //Make sure it's not ajaxing
                if (!isset($_GET['load_more_ajaxing'])) {
                    $ft_gallery_dynamic_name = $_REQUEST['ft_gallery_dynamic_name'];
                    // this div returns outputs our ajax request via jquery append html from above

                    print '<div class="fts-clear"></div>';
                    print '<div id="output_' . $fts_dynamic_class_name . '"></div>';
                    if ($scrollMore == 'autoscroll') {
                        print '<div id="loadMore_' . $ft_gallery_dynamic_name . '" class="fts-fb-load-more fts-fb-autoscroll-loader">'.__('Load More', 'feed-them-gallery').'</div>';
                    }
                }
                ?>
                <?php //only show this script if the height option is set to a number
                if ($height !== '' && empty($height) == NULL) { ?>
                    <script>
                        // this makes it so the page does not scroll if you reach the end of scroll bar or go back to top
                        jQuery.fn.isolatedScrollFTGallery = function () {
                            this.bind('mousewheel DOMMouseScroll', function (e) {
                                var delta = e.wheelDelta || (e.originalEvent && e.originalEvent.wheelDelta) || -e.detail,
                                    bottomOverflow = this.scrollTop + jQuery(this).outerHeight() - this.scrollHeight >= 0,
                                    topOverflow = this.scrollTop <= 0;
                                if ((delta < 0 && bottomOverflow) || (delta > 0 && topOverflow)) {
                                    e.preventDefault();
                                }
                            });
                            return this;
                        };
                        jQuery('.ft-wp-gallery-scrollable').isolatedScrollFTGallery();
                    </script>
                <?php } //end $height !== 'auto' && empty($height) == NULL ?>
                <?php
                if (isset($scrollMore) && $scrollMore == 'autoscroll' || isset($height) && $height !== '') {
                    print '</div><!--closing height div for scrollable feeds-->'; // closing height div for scrollable feeds
                }
                //Make sure it's not ajaxing
                if (!isset($_GET['load_more_ajaxing'])) {
                    print '<div class="fts-clear"></div>';
                    if (isset($scrollMore) && $scrollMore == 'button') {
                        print '<div class="fts-instagram-load-more-wrapper">';
                        print'<div id="loadMore_' . $ft_gallery_dynamic_name . '"" style="';
                        if (isset($loadmore_btn_maxwidth) && $loadmore_btn_maxwidth !== '') {
                            print'max-width:' . $loadmore_btn_maxwidth . ';';
                        }
                        if (isset($ft_gallery_loadmore_background_color) && $ft_gallery_loadmore_background_color !== '') {
                            print'background:' . $ft_gallery_loadmore_background_color . ';';
                        }
                        if (isset($ft_gallery_loadmore_text_color) && $ft_gallery_loadmore_text_color !== '') {
                            print'color:' . $ft_gallery_loadmore_text_color . ';';
                        }
                        print'margin:' . $loadmore_btn_margin . ' auto ' . $loadmore_btn_margin . '" class="fts-fb-load-more">' . __('Load More', 'feed-them-gallery') . '</div>';
                        print'</div>';

                    }
                    if($pagination == 'yes') {

                        if (isset($ft_gallery_pagination_text_color) && $ft_gallery_pagination_text_color !== '') {
                            $ft_gallery_pagination_text_color = 'style="color:' . $ft_gallery_pagination_text_color . ';"';
                        }
                        echo '<div class="ftgallery-image-count-wrap"'.$ft_gallery_pagination_text_color.'>';
                        echo '<span class="ft-gallery-image-loaded-count">' . $post_count . '</span>';
                        echo '<span class="ft-gallery-count-of">' . __('of', 'feed-them-gallery') . '</span>';
                        echo '<span class="ft-gallery-image-count-total"> ' . $this->ft_gallery_count_post_images($ftg['id']) . ' </span>';
                        echo '</div>';
                    }
                }//End Check
                unset($_REQUEST['ft_gallery_offset']);
            }
            else {
                if ($format_type == 'gallery') {
                    print '</div>'; // closing div for feed
                }
                print '</div>'; // closing div for main wrapper

            }


        } //Error or Empty!
        else {
            $image_list;
        }

        // FTG Pagination Function


        // Opening Div for footer pagination
        if('yes' === $ftg_sorting_options || 'yes' === $ft_gallery_show_true_pagination) {
            print '<div class="ftg-pagination-footer">';
        }
            if('yes' === $ftg_sorting_options) {
                $ft_gallery_position_of_pagination = null !== get_post_meta($ftg['id'], 'ftg_position_of_sort_select', true) ? get_post_meta($ftg['id'], 'ftg_position_of_sort_select', true) : '';
                if('above-below' === $ft_gallery_position_of_pagination || 'below' === $ft_gallery_position_of_pagination) {
                    $this->ftg_sort_order_select( $ftg['id'] );
                }
            }

            if('yes' === $ft_gallery_show_true_pagination) {
                $ft_gallery_position_of_pagination = null !== get_post_meta($ftg['id'], 'ft_gallery_position_of_pagination', true) ? get_post_meta($ftg['id'], 'ft_gallery_position_of_pagination', true) : '';
                if('above-below' === $ft_gallery_position_of_pagination || 'below' === $ft_gallery_position_of_pagination) {
                    $this->ftg_pagination( $ftg['id'] );
                }
            }
        // End closing Div for footer pagination
        if('yes' === $ftg_sorting_options || 'yes' === $ft_gallery_show_true_pagination) {
            print '</div><div class="ftg-clear"></div>';
        }



        //Make sure it's not ajaxing
        if (!isset($_GET['load_more_ajaxing'])) {
            if (current_user_can('manage_options')) {
                ?>
                <div class="ft-gallery-edit-link" style="text-align: center;">
                    <a href="<?php print $edit_url ?>" target="_blank"><?php _e('Edit Gallery', 'feed-them-gallery') ?></a>
                </div>
            <?php }
        }//End Check



        return ob_get_clean();
    }

    /**
     * FT Gallery Get Attachment ID
     *
     * Get an attachment ID given a URL.
     *
     * @param string $url
     *
     * @return int Attachment ID on success, 0 on failure
     */
    function ft_gallery_get_attachment_id($url) {

        $attachment_id = 0;

        $dir = wp_upload_dir();

        if (false !== strpos($url, $dir['baseurl'] . '/')) { // Is URL in uploads directory?

            $file = basename($url);

            $query_args = array(
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'value' => $file,
                        'compare' => 'LIKE',
                        'key' => '_wp_attachment_metadata',
                    ),
                )
            );

            $query = new \ WP_Query($query_args);

            if ($query->have_posts()) {

                foreach ($query->posts as $post_id) {

                    $meta = wp_get_attachment_metadata($post_id);

                    $original_file = basename($meta['file']);
                    $cropped_image_files = wp_list_pluck($meta['sizes'], 'file');

                    if ($original_file === $file || in_array($file, $cropped_image_files)) {
                        $attachment_id = $post_id;
                        break;
                    }

                }

            }

        }

        return $attachment_id;
    }


    /**
     * FT Gallery 2day Array
     *
     * Arrange 2 dimensional array
     *
     * @param $array
     * @param int $col_count
     * @return bool
     * @since 1.0.0
     */
    function ft_gallery_array_2d($array, $col_count = 1) {
        $result = false;
        if (!empty($array) && is_array($array)) {
            $row_count = ceil(count($array) / $col_count);
            $pointer = 0;
            for ($row = 0; $row < $row_count; $row++) {
                for ($col = 0; $col < $col_count; ++$col) {
                    if (isset($array[$pointer])) {
                        $result[$row]['id'] = $array[$pointer];
                        $pointer++;
                    }
                }
            }
        }

        return $result;
    }


    /**
     * FT Gallery Sort Image List
     *
     * Sort the list of images
     *
     * @param $gallery_id
     * @since 1.0.0
     */
    function ft_gallery_sort_image_list($gallery_id) {
        // IF images are sorted on gallery post
        $image_list_sort = $this->ft_gallery_get_media_rest($gallery_id, '100');
        // We take the saved array that gets stored in the post meta field, ft-gallery-images-sort-order, when you sort the images on a gallery page and use the function below ft_gallery_array_2d to format the array so we can flip the array and use the number count and compare the $image_list arrays id's with our usort function below that
        $image_sort = get_post_meta($gallery_id, 'ft-gallery-images-sort-order', true);
        echo '<pre>' . print_r($image_list_sort, 1) . '</pre>';
        //http://snipplr.com/view/67672/
        function ft_gallery_array_2d($array, $col_count = 1) {
            $result = false;
            if (!empty($array) && is_array($array)) {
                $row_count = ceil(count($array) / $col_count);
                $pointer = 0;
                for ($row = 0; $row < $row_count; $row++) {
                    for ($col = 0; $col < $col_count; ++$col) {
                        if (isset($array[$pointer])) {
                            $result[$row]['id'] = $array[$pointer];
                            $pointer++;
                        }
                    }
                }
            }

            return $result;
        }

        $result = ft_gallery_array_2d($image_sort, 1);
        echo '<pre>' . print_r($image_sort, 1) . '</pre>';

        $skeys = array_flip($image_sort);

        echo '<pre>' . print_r($skeys, 1) . '</pre>';
        usort($image_list_sort,
            function ($a, $b) use ($skeys) {
                $final = isset($skeys[$a['id']]) ? $skeys[$a['id']] : null;
                $a = $final;
                $b = $skeys[$b['id']];
                $newlist = $a - $b;
                echo '<pre>' . print_r($newlist, 1) . '</pre>';

                return $newlist;
            }
        );

        // END IF images are sorted on gallery post
    }

    /**
     * My FT Gallery Load More
     *
     * This function is being called from the fb feed... it calls the ajax in this case.
     *
     * @since 1.0.0
     */
    function ft_gallery_load_more() {
        if (!wp_verify_nonce($_REQUEST['fts_security'], $_REQUEST['fts_time'] . 'load-more-nonce')) {
            exit('Sorry, You can\'t do that!');
        } else {

            $post_count = sanitize_text_field( $_REQUEST['ft_gallery_post_count'] );
            $offset = sanitize_text_field( $_REQUEST['ft_gallery_offset'] );
            $media = sanitize_text_field( $_REQUEST['ft_gallery_media_count'] );

            $object = do_shortcode('[feed-them-gallery id=' . sanitize_text_field( $_REQUEST['ft_gallery_id'] ) . ' offset=' . sanitize_text_field( $offset ) . ' media_count=' . sanitize_text_field( $media ) . ']');
            echo $object;
        }
        die();
    }


    function ftg_variable_add_to_cart($productID) {
        global $product;

        $product = wc_get_product($productID);
        //  $variations = $this->find_valid_variations($productID);

        if ($product->get_type('variable') == 'variable') { ?>
            <div class="ft-gallery-variations-wrap">
            <div class="ft-gallery-variations-price-wrap">
                <?php
                //  Saving commented out items: Use case... if we want to have a From: $10 - $50 option
                // $prefix = sprintf('%s: ', __('From', 'feed-them-gallery'));
                $min_price_regular = $product->get_variation_regular_price('min', true);
                $min_price_sale = $product->get_variation_sale_price('min', true);
                $max_price = $product->get_variation_price('max', true);
                // $min_price = $product->get_variation_price('min', true);
                $price = ($min_price_sale == $min_price_regular) ? wc_price($min_price_regular) . ' - ' . wc_price($max_price) : print  '<del>' . wc_price($min_price_regular) . '</del>' . '<ins>' . wc_price($min_price_sale) . '</ins>';
                // print ( $min_price == $max_price ) ? $price : sprintf('%s%s', $prefix, $price);
                print  $price; ?>
            </div>
            <div class="ft-gallery-variations-text ft-gallery-js-load">
                <?php
                // Enqueue variation scripts
                wp_enqueue_script('wc-add-to-cart-variation');
                // Load the template
                wc_get_template('single-product/add-to-cart/variable.php', array(
                    'available_variations' => $product->get_available_variations(),
                    'attributes' => $product->get_variation_attributes(),
                    'selected_attributes' => $product->get_default_attributes()
                ));
                ?>
            </div>
            </div><?php
        } elseif ($product->get_type('variable') == 'simple') { ?>

            <div class="ft-gallery-variations-wrap">
                <div class="ft-gallery-variations-price-wrap ft-gallery-simple-price">

                    <?php
                    $price_regular = $product->get_regular_price('min', true);
                    $price_sale = $product->get_sale_price('min', true);
                    $price = $price_sale ? '<del>' . wc_price($price_regular) . '</del>' . '<ins>' . wc_price($price_sale) . '</ins>' : wc_price($price_regular);
                    // print ( $min_price == $max_price ) ? $price : sprintf('%s%s', $prefix, $price);
                    print  $price;
                    ?>
                </div>
                <div class="ft-gallery-simple-cart">
                    <?php
                    // Enqueue variation scripts
                    wc_get_template('single-product/add-to-cart/simple.php');
                    ?>
                </div>
            </div>
        <?php }
    }

    function find_valid_variations($productID) {
        global $product;
        $product = wc_get_product($productID);
        $variations = $product->get_available_variations();
        $attributes = $product->get_attributes();
        $new_variants = array();

        // Loop through all variations
        foreach ($variations as $variation) {

            // Peruse the attributes.

            // 1. If both are explicitly set, this is a valid variation
            // 2. If one is not set, that means any, and we must 'create' the rest.

            $valid = true; // so far
            foreach ($attributes as $slug => $args) {
                if (array_key_exists("attribute_$slug", $variation['attributes']) && !empty($variation['attributes']["attribute_$slug"])) {
                    // Exists

                } else {
                    // Not exists, create
                    $valid = false; // it contains 'anys'
                    // loop through all options for the 'ANY' attribute, and add each
                    foreach (explode('|', $attributes[ $slug ]['value']) as $attribute) {
                        $attribute = trim($attribute);
                        $new_variant = $variation;
                        $new_variant['attributes']["attribute_$slug"] = $attribute;
                        $new_variants[] = $new_variant;
                    }

                }
            }
            // This contains ALL set attributes, and is itself a 'valid' variation.
            if ($valid)
                $new_variants[] = $variation;
        }

        return $new_variants;
    }
}

?>
