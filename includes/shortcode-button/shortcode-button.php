<?php
/**
 * Shortcode_Button Class
 *
 * This class has the functions to create add a shortcode button to wordpress "Edit Post" page
 *
 * @class    Shortcode_Button
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */
namespace feed_them_gallery;
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Class Shortcode_Button
 */
class Shortcode_Button {
    public $all_options = '';

    public static function load() {
        $instance = new self();

        // Initiate Shortcode_media_button.
        $instance->ft_gallery_shortcode_media_button();
        $instance->add_actions_filters();
    }

    public function __construct() {}

    public function add_actions_filters(){
        add_action('wp_ajax_ft_gallery_editor_get_galleries', array($this, 'ft_gallery_editor_get_galleries'));
        add_filter('media_buttons_context', array($this, 'ft_gallery_shortcode_media_button'));
        add_action('admin_enqueue_scripts', array($this, 'ft_gallery_shortcode_get_all_options'));
        add_action('print_media_templates', array($this, 'ft_gallery_print_media_templates'));
    }

    /**
     * FT Gallery Shortcode Get All Options
     *
     * Adds the Custom Shortcode button scripts that appears on admin post type pages
     *
     * @since 1.0.0
     */
    public function ft_gallery_shortcode_get_all_options() {

        $current_screen = get_current_screen();
        $is_admin = is_admin();

        // We must only show contents below if we're on a post page in the wp admin
        if ($is_admin && 'post' !== $current_screen->base || $is_admin && 'ft_gallery' === $current_screen->post_type || $is_admin && 'ft_gallery_albums' === $current_screen->post_type) {
            return;
        }

        // Enqueue the gallery / album selection script
        wp_enqueue_script('gallery-select-script', plugins_url('feed-them-gallery/includes/shortcode-button/js/gallery-select.js'), array('jquery'), FTG_CURRENT_VERSION, true);
        wp_localize_script('gallery-select-script', 'ft_gallery_select', array(
            'get_galleries_nonce' => wp_create_nonce('ft-gallery-editor-get-galleries'),
            'modal_title' => __('Insert', 'feed-them-gallery'),
            'insert_button_label' => __('Insert', 'feed-them-gallery'),
        ));

        // Enqueue the script that will trigger the editor button.
        wp_enqueue_script('editor-script', plugins_url('feed-them-gallery/includes/shortcode-button/js/editor.js'), array('jquery'), FTG_CURRENT_VERSION, true);
        wp_localize_script('gallery-select-script', 'ft_gallery_editor', array(
            'modal_title' => __('Insert Gallery', 'feed-them-gallery'),
            'insert_button_label' => __('Insert', 'feed-them-gallery'),
        ));
    }

    /**
     * FT Gallery Shortcode Media Button
     *
     * Adds a custom gallery insert button beside the media uploader button.
     *
     * @since 1.0.0
     *
     * @return string $buttons Amended media buttons context HTML.
     */
    public function ft_gallery_shortcode_media_button() {

        // Create the media button.
        $button = '<a id="ft-media-modal-button" href="javascript:;" class="button feed-them-gallery-choose-gallery" data-action="gallery" title="' . esc_attr__('Add Gallery', 'feed-them-gallery') . '" >
            <span class="ft-media-icon"></span> ' .
            __('Add FT Gallery', 'feed-them-gallery') .
            '</a>
            ';
        // Filter the button.
        $button = apply_filters('ft_gallery_media_button', $button);

        // Append the button.
        return $button;

    }

    /**
     * FT Gallery Get Galleries
     *
     * Returns all galleries created on the site.
     *
     * @since 1.0.0
     *
     * @param    bool $skip_empty Skip empty sliders.
     * @param    bool $ignore_cache Ignore Transient cache.
     * @param    string $search_terms Search for specified Galleries by Title
     *
     * @return array|bool Array of gallery data or false if none found.
     */
    public function ft_gallery_get_galleries($skip_empty = true, $ignore_cache = false, $search_terms = '') {

        // Get gallery items
        $galleries = $this->ft_gallery_internal_get_galleries($skip_empty, $search_terms);

        // Return the gallery data.
        return $galleries;
    }

    /**
     * FT Gallery Internal Get Galleries
     *
     * Internal method that returns all galleries created on the site.
     *
     * @since 1.0.0
     *
     * @param bool $skip_empty Skip Empty Galleries.
     * @param string $search_terms Search for specified Galleries by Title
     * @return mixed                    Array of gallery data or false if none found.
     */
    public function ft_gallery_internal_get_galleries($skip_empty = true, $search_terms = '') {

        // Build WP_Query arguments.
        $args = array(
            'post_type' => 'ft_gallery',
            'post_status' => 'publish',
            'posts_per_page' => 99,
            'no_found_rows' => true,
            'fields' => 'ids',
        );

        // If search terms exist, add a search parameter to the arguments.
        if (!empty($search_terms)) {
            $args['s'] = $search_terms;
        }

        // Run WP_Query.
        $galleries = new \ WP_Query($args);
        if (!isset($galleries->posts) || empty($galleries->posts)) {
            return false;
        }

        // Now loop through all the galleries found and only use galleries that have images in them.
        $ret = array();
        foreach ($galleries->posts as $id) {
            $data = '[ft-gallery id=' . $id . ']';

            // error_log($data);

            $ret[] = array('id' => $id);
            // Add gallery to array of galleries.
        }

        // Return the gallery data.
        return $ret;
    }

    /**
     * FT Gallery Editor Get Galleries
     *
     * Returns Galleries, with an optional search term
     *
     * @since 1.0.0
     */
    function ft_gallery_editor_get_galleries() {
        global $post;

        // Check nonce
        check_admin_referer('ft-gallery-editor-get-galleries', 'nonce');

        // Get POSTed fields
        $search = (bool)$_POST['search'];
        $search_terms = sanitize_text_field($_POST['search_terms']);
        $prepend_ids = stripslashes_deep($_POST['prepend_ids']);
        $results = array();

        // Get galleries
        $galleries = $this->ft_gallery_get_galleries(false, true, ($search ? $search_terms : ''));


        $display_gallery = new Display_Gallery();
        // Build array of just the data we need.
        foreach (( array )$galleries as $gallery) {
            // Get the thumbnail of the first image
            if (isset($gallery['gallery']) && !empty($gallery['gallery'])) {

            }
            $image_list = $display_gallery->ft_gallery_get_media_rest($gallery['id'], '1');
            $thumbnail = $image_list[0]['media_details']['sizes']['medium']['source_url'];
            error_log($thumbnail);
            // Instead of pulling the title from config, attempt to pull it from the gallery post first
            if (isset($gallery['id'])) {
                $gallery_post = get_post($gallery['id']);
            } else {
                $gallery_post = false;
            }

            $temp_title = false;
            if (isset($gallery_post->post_title)) {
                $temp_title = trim($gallery_post->post_title);
            }

            if (!empty($temp_title)) {
                $gallery_title = $gallery_post->post_title;
            } else if (isset($gallery['config']['title'])) {
                $gallery_title = $gallery['config']['title'];
            } else {
                $gallery_title = false;
            }

            // Check to make sure variables are there
            $gallery_id = false;
            $gallery_config_slug = false;

            if (isset($gallery['id'])) {
                $gallery_id = $gallery['id'];
            }
            if (isset($gallery['config']['slug'])) {
                $gallery_config_slug = $gallery['config']['slug'];
            }

            // Add gallery to results
            $results[] = array(
                'id' => $gallery_id,
                'slug' => $gallery_config_slug,
                'title' => $gallery_title,
                'thumbnail' => $thumbnail,
                // Tells the editor modal whether this is a Gallery or Album for the shortcode output
                'action' => 'gallery',
            );
        }

        // If any prepended Gallery IDs were specified, get them now
        // These will typically be a Defaults Gallery, which wouldn't be included in the above ft_gallery_get_galleries() call
        if (is_array($prepend_ids) && count($prepend_ids) > 0) {
            $prepend_results = array();

            // Get each Gallery
            foreach ($prepend_ids as $gallery_id) {
                // Get gallery
                $gallery = get_post_meta($gallery_id, '_eg_gallery_data', true);

                // Get gallery first image
                if (isset($gallery['gallery']) && !empty($gallery['gallery'])) {
                    $display_gallery = new Display_Gallery();
                    $image_list = $display_gallery->ft_gallery_get_media_rest($gallery['id'], '1');
                    $thumbnail = $image_list[0]['media_details']['sizes']['thumbnail']['source_url'];
                }

                // Add gallery to results
                $prepend_results[] = array(
                    'id' => $gallery['id'],
                    'slug' => $gallery['config']['slug'],
                    'title' => $gallery['config']['title'],
                    'thumbnail' => ((isset($thumbnail) && is_array($thumbnail)) ? $thumbnail[0] : ''),
                    'action' => 'gallery', // Tells the editor modal whether this is a Gallery or Album for the shortcode output
                );
            }

            // Add to results
            if (is_array($prepend_results) && count($prepend_results) > 0) {
                $results = array_merge($prepend_results, $results);
            }
        }

        // Return galleries
        wp_send_json_success($results);

    }

    /**
     * FT Gallery Print Media Templates
     *
     * Outputs backbone.js wp.media compatible templates, which are loaded into the modal view
     *
     * @since 1.0.0
     */
    public function ft_gallery_print_media_templates() {

        // Insert Gallery (into Visual / Text Editor)
        // Use: wp.media.template( 'ft-selection' )
        ?>
        <script type="text/html" id="tmpl-ft-selection">
            <div class="media-frame-title">
                <h1>{{data.modal_title}}</h1>
            </div>
            <div class="media-frame-content">
                <div class="attachments-browser ft-gallery ft-gallery-editor">
                    <!-- Galleries -->
                    <ul class="attachments"></ul>

                    <!-- Sidebar -->
                    <div class="media-sidebar attachment-info"></div>

                    <!-- Search -->
                    <div class="media-toolbar">
                        <div class="media-toolbar-secondary">
                            <span class="spinner"></span>
                        </div>
                        <div class="media-toolbar-primary search-form">
                            <label for="ft-gallery-search" class="screen-reader-text"><?php _e('Search', 'feed-them-gallery'); ?></label>
                            <input type="search" placeholder="<?php _e('Search', 'feed-them-gallery'); ?>" id="ft-gallery-search" class="search"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bar -->
            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-primary search-form">
                        <button type="button" class="button media-button button-primary button-large media-button-insert" disabled="disabled">
                            {{data.insert_button_label}}
                        </button>
                    </div>
                </div>
            </div>
        </script>
        <?php
        // Single Selection Item (Gallery or Album)
        // Use: wp.media.template( 'ft-selection-item' )
        ?>
        <script type="text/html" id="tmpl-ft-selection-item">
            <div class="attachment-preview" data-id="{{ data.id }}">
                <div class="thumbnail">
                    <#
                            if ( data.thumbnail != '' ) {
                            #>
                        <img src="{{ data.thumbnail }}" alt="{{ data.title }}"/>
                        <#
                                }
                                #>
                            <strong>
                                <span>{{ data.title }}</span>
                            </strong>
                            <code>
                                [feed-them-{{ data.action }} id="{{ data.id }}"]
                            </code>
                </div>
            </div>

            <a class="check">
                <div class="media-modal-icon"></div>
            </a>
        </script>
        <?php
        // Selection Sidebar
        // Use: wp.media.template( 'ft-selection-sidebar' )
        ?>
        <script type="text/html" id="tmpl-ft-selection-sidebar">
            <h3><?php _e('Helpful Tips', 'feed-them-gallery'); ?></h3>
            <strong><?php _e('Choosing Your Gallery', 'feed-them-gallery'); ?></strong><p>
                <?php _e('Simply click on one of the boxes to the left or you can Ctrl(PC) / cmd(MAC) and click to select multiple Galleries.  The "Insert" button will be activated once you have selected a gallery.', 'feed-them-gallery'); ?>
            </p><strong><?php _e('Insert Your Gallery', 'feed-them-gallery'); ?></strong><p>
                <?php _e('To insert your gallery, click on the "Insert" button below.', 'feed-them-gallery'); ?>
            </p>
            <h3><?php _e('Title Options', 'feed-them-gallery'); ?></h3>
            <p><?php _e('Add the Gallery Title before each shortcode and Align the Title if you like.', 'feed-them-gallery'); ?></p>
            <div class="settings">
                <label class="setting">
                    <p>
                        <span class="name"><?php _e('Display Title', 'feed-them-gallery'); ?></span>
                        <select name="title" size="1">
                            <option value="0" selected><?php _e('No', 'feed-them-gallery'); ?></option>
                            <?php
                            for ($i = 1; $i <= 6; $i++) {
                                ?>
                                <option value="h<?php echo $i; ?>"><?php echo sprintf(__('Yes, as Heading H%s', 'feed-them-gallery'), $i); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </p>
                </label>
                <label class="setting">
                    <span class="name"><?php _e('Align Title', 'feed-them-gallery'); ?></span>
                    <select name="align" size="1">
                        <option value="" selected><?php _e('No', 'feed-them-gallery'); ?></option>
                        <option value="left"><?php _e('Left', 'feed-them-gallery'); ?></option>
                        <option value="center"><?php _e('Center', 'feed-them-gallery'); ?></option>
                        <option value="right"><?php _e('Right', 'feed-them-gallery'); ?></option>
                    </select>
                </label>
            </div>
        </script>
        <?php
        // Error
        // Use: wp.media.template( 'ft-gallery-error' )
        ?>
        <script type="text/html" id="tmpl-ft-gallery-error">
            <p>
                {{ data.error }} </p>
        </script>

        <?php
    }
}