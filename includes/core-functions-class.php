<?php
/**
 * Core Functions Class
 *
 * This class has some of the core functions of Feed Them Gallery
 *
 * @class    Core_Functions
 * @version  1.0.0
 * @package  FeedThemSocial/Core
 * @category Class
 * @author   SlickRemix
 */
namespace feed_them_gallery;
/**
 * Class Core_Functions
 */
class Core_Functions {
    public $output = "";
    public $feeds_core = "";

    /**
     * Global Prefix
     * Sets Prefix for global options
     *
     * @var string
     */
    public $global_prefix = 'global_';

    /**
     * Core_Functions constructor.
     */
    function __construct() {

        add_filter('single_template', array($this, 'ft_gallery_locate_template'), 999);
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
     * FT Gallery Required Plugins
     *
     * Return an array of required plugins.
     *
     * @return array
     * @since 1.0.0
     */
    function ft_gallery_required_plugins() {
        $required_premium_plugins = array(
            'feed_them_gallery_premium' => array(
                'title' => 'Feed Them Gallery Premium',
                'plugin_url' => 'feed-them-gallery-premium/feed-them-gallery-premium.php',
                'demo_url' => 'https://feedthemgallery.com/',
                'purchase_url' => 'https://www.slickremix.com/downloads/feed-them-gallery/',
            ),);

        return $required_premium_plugins;
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

        $prem_required_plugins = $this->ft_gallery_required_plugins();

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
                    case 'checkbox-dynamic-image-sizes':

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

                        $output .= '</select>';
                        break;



                    //Image sizes for Free download icon
                    case 'ftg-free-download-size':
                       
                        $final_value_images = $gallery_options_returned['ftg_free_download_size'];
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

    function ft_gallery_locate_template($located) {
        global $post;

        $post_type = $post->post_type;

        switch($post_type){

            case 'ft_gallery':
                //Set The Template name
                $template_name = 'gallery-template.php';

                $use_template = true;
                break;
            case 'ft_gallery_albums':
                //Set The Template name
                $template_name = 'album-template.php';

                $use_template = true;
                break;

            default:
                $use_template = false;
                break;
        }

        if ($use_template == true) {
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

}//END Class