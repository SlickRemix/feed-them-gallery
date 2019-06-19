<?php
/**
 * Gallery Options Class
 *
 * This class has the options for building and saving on the Custom Meta Boxes
 *
 * @class    Gallery_Options
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

namespace feed_them_gallery;

// Exit if accessed directly!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Gallery_Options
 */
class Gallery_Options {

	/**
	 * All Gallery Options
	 *
	 * @var array
	 */
	public $all_options;

	/**
	 * Gallery_Options constructor.
	 */
	public function __construct() { }

	/**
	 * All Gallery Options
	 *
	 * Function to return all Gallery options
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_all_options() {
		$instance = new self();

		$instance->layout_options();
		$instance->color_options();
		$instance->watermark_options();
		$instance->woocommerce_options();
		$instance->woocommerce_extra_options();
		$instance->pagination_options();
		$instance->tags_options();

		return $instance->all_options;
	}

	/**
	 * Layout Options
	 *
	 * Options for the Layout Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function layout_options() {
		$this->all_options['layout'] = array(
			'section_attr_key'   => 'facebook_',
			'section_title'      => esc_html( 'Layout Options', 'feed-them-gallery' ),
			'section_wrap_class' => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'  => 'fb-page-shortcode-form',
			'form_wrap_id'       => 'fts-fb-page-form',
			// Token Check // We'll use these option for premium messages in the future.
			'premium_msg_boxes'  => array(
				'album_videos' => array(
					'req_plugin' => 'fts_premium',
					'msg'        => '',
				),
				'reviews'      => array(
					'req_plugin' => 'facebook_reviews',
					'msg'        => '',
				),
			),

			'main_options'       => array(

				// Gallery Type.
				array(
					'input_wrap_class' => 'ft-wp-gallery-type',
					'option_type'      => 'select',
					'label'            => trim(
						sprintf(
							__( 'Choose the gallery type%1$s View all Gallery %2$sDemos%3$s', 'feed-them-gallery' ),
							'<br/><small>',
							'<a href="' . esc_url( 'https://feedthemgallery.com/gallery-demo-one/' ) . '" target="_blank">',
							'</a></small>'
						)
					),
					'type'             => 'text',
					'id'               => 'ft_gallery_type',
					'name'             => 'ft_gallery_type',
					'default_value'    => 'gallery',
					'options'          => array(
						array(
							'label' => __( 'Responsive Image Gallery ', 'feed-them-gallery' ),
							'value' => 'gallery',
						),
						array(
							'label' => __( 'Image Gallery Collage (Masonry)', 'feed-them-gallery' ),
							'value' => 'gallery-collage',
						),
						array(
							'label' => __( 'Image Post', 'feed-them-gallery' ),
							'value' => 'post',
						),
						array(
							'label' => __( 'Image Post in Grid (Masonry)', 'feed-them-gallery' ),
							'value' => 'post-in-grid',
						),
					),
				),
				// Show Photo Caption.
				array(
					'input_wrap_class' => 'fb-page-description-option-hide',
					'option_type'      => 'select',
					'label'            => __( 'Show Photo Caption', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_photo_caption',
					'name'             => 'ft_gallery_photo_caption',
					'default_value'    => '',
					'options'          => array(
						array(
							'label' => __( 'Title and Description', 'feed-them-gallery' ),
							'value' => 'title_description',
						),
						array(
							'label' => __( 'Title', 'feed-them-gallery' ),
							'value' => 'title',
						),
						array(
							'label' => __( 'Description', 'feed-them-gallery' ),
							'value' => 'description',
						),
						array(
							'label' => __( 'None', 'feed-them-gallery' ),
							'value' => 'none',
						),
					),
				),
				// ******************************************
				// Facebook Grid Options
				// ******************************************
				// Facebook Page Display Posts in Grid
				// array(
				// 'grouped_options_title' => __('Grid', 'feed-them-gallery'),
				// 'input_wrap_class' => 'fb-posts-in-grid-option-wrap',
				// 'option_type' => 'select',
				// 'label' => __('Display Posts in Grid', 'feed-them-gallery'),
				// 'type' => 'text',
				// 'id' => 'ft_gallery_grid_option',
				// 'name' => 'ft_gallery_grid_option',
				// 'default_value' => 'no',
				// 'options' => array(
				// array(
				// 'label' => __('No', 'feed-them-gallery'),
				// 'value' => 'no',
				// ),
				// array(
				// 'label' => __('Yes', 'feed-them-gallery'),
				// 'value' => 'yes',
				// ),
				// ),
				// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
				// 'sub_options' => array(
				// 'sub_options_wrap_class' => 'main-grid-options-wrap',
				// ),
				// ),
				array(
					'input_wrap_class'   => 'fb-page-columns-option-hide',
					'option_type'        => 'select',
					'label'              => __( 'Number of Columns', 'feed-them-gallery' ),
					'type'               => 'text',
					'instructional-text' => sprintf(
						__( '%1$sNOTE:%2$s Using the Columns option will make this gallery fully responsive and it will adapt in size to your containers width. Choose the Number of Columns and Space between each image below.', 'feed-them-gallery' ),
						'<strong>',
						'</strong>'
					),
					'id'                 => 'ft_gallery_columns',
					'name'               => 'ft_gallery_columns',
					'default_value'      => '4',
					'options'            => array(
						array(
							'label' => __( '1', 'feed-them-gallery' ),
							'value' => '1',
						),
						array(
							'label' => __( '2', 'feed-them-gallery' ),
							'value' => '2',
						),
						array(
							'label' => __( '3', 'feed-them-gallery' ),
							'value' => '3',
						),
						array(
							'label' => __( '4', 'feed-them-gallery' ),
							'value' => '4',
						),
						array(
							'label' => __( '5', 'feed-them-gallery' ),
							'value' => '5',
						),
						array(
							'label' => __( '6', 'feed-them-gallery' ),
							'value' => '6',
						),
						array(
							'label' => __( '7', 'feed-them-gallery' ),
							'value' => '7',
						),
						array(
							'label' => __( '8', 'feed-them-gallery' ),
							'value' => '8',
						),
					),
				),
				array(
					'input_wrap_class'   => 'ftg-masonry-columns-option-hide',
					'option_type'        => 'select',
					'label'              => __( 'Number of Columns', 'feed-them-gallery' ),
					'type'               => 'text',
					'instructional-text' => sprintf(
						__( '%1$sNOTE:%2$s Using the Columns option will make this gallery fully responsive and it will adapt in size to your containers width. Choose the Number of Columns and Space between each image below.', 'feed-them-gallery' ),
						'<strong>',
						'</strong>'
					),
					'id'                 => 'ft_gallery_columns_masonry2',
					'name'               => 'ft_gallery_columns_masonry2',
					'default_value'      => '3',
					'options'            => array(
						array(
							'label' => __( '2', 'feed-them-gallery' ),
							'value' => '2',
						),
						array(
							'label' => __( '3', 'feed-them-gallery' ),
							'value' => '3',
						),
						array(
							'label' => __( '4', 'feed-them-gallery' ),
							'value' => '4',
						),
						array(
							'label' => __( '5', 'feed-them-gallery' ),
							'value' => '5',
						),
					),
				),
				array(
					'input_wrap_class' => 'ftg-masonry-columns-option-hide',
					'option_type'      => 'select',
					'label'            => __( 'Space between Images', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_columns_masonry_margin',
					'name'             => 'ft_gallery_columns_masonry_margin',
					'default_value'    => '5',
					'options'          => array(
						array(
							'label' => __( '1px', 'feed-them-gallery' ),
							'value' => '1',
						),
						array(
							'label' => __( '2px', 'feed-them-gallery' ),
							'value' => '2',
						),
						array(
							'label' => __( '3px', 'feed-them-gallery' ),
							'value' => '3',
						),
						array(
							'label' => __( '4px', 'feed-them-gallery' ),
							'value' => '4',
						),
						array(
							'label' => __( '5px', 'feed-them-gallery' ),
							'value' => '5',
						),
						array(
							'label' => __( '10px', 'feed-them-gallery' ),
							'value' => '10',
						),
						array(
							'label' => __( '15px', 'feed-them-gallery' ),
							'value' => '15',
						),
						array(
							'label' => __( '20px', 'feed-them-gallery' ),
							'value' => '20',
						),
					),
				),
				array(
					'input_wrap_class' => 'fb-page-columns-option-hide',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							__( 'Force Columns%1$s Yes, will force image columns. No, will allow the images to be resposive for smaller devices%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_force_columns',
					'name'             => 'ft_gallery_force_columns',
					'default_value'    => '',
					'options'          => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),

					),
				),
				// Grid Column Width
				// array(
				// 'input_wrap_class' => 'fb-page-grid-option-hide fb-page-columns-option-hide ftg-hide-for-columns',
				// 'option_type' => 'input',
				// 'label' => __('Grid Column Width', 'feed-them-gallery'),
				// 'type' => 'text',
				// 'id' => 'ft_gallery_grid_column_width',
				// 'name' => 'ft_gallery_grid_column_width',
				// 'instructional-text' =>
				// sprintf(__('%1$sNOTE:%2$s Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery'),
				// '<strong>',
				// '</strong>'
				// ),
				// 'placeholder' => '310px ' . __('for example', 'feed-them-gallery'),
				// 'default_value' => '310px',
				// 'value' => '',
					   // This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
					   // 'sub_options' => array(
					   // 'sub_options_wrap_class' => 'fts-facebook-grid-options-wrap',
					   // ),
				// ),
				   // Grid Spaces Between Posts.
				   array(
					   'input_wrap_class' => 'fb-page-grid-option-hide fb-page-grid-option-border-bottom',
					   'option_type'      => 'input',
					   'label'            => __( 'Space between Images', 'feed-them-gallery' ),
					   'type'             => 'text',
					   'id'               => 'ft_gallery_grid_space_between_posts',
					   'name'             => 'ft_gallery_grid_space_between_posts',
					   'placeholder'      => '1px ' . __( 'for example', 'feed-them-gallery' ),
					   'default_value'    => '1px',
					   // 'sub_options_end' => 2,
				   ),
				// Show Name.
				array(
					'input_wrap_class' => 'ft-gallery-user-name',
					'option_type'      => 'input',
					'label'            =>
						sprintf(
							__( 'User Name%1$s Company or user who took this photo%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_username',
					'name'             => 'ft_gallery_username',
					'placeholder'      => '',
					'default_value'    => '',
				),
				// Show Name Link.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							__( 'User Custom Link%1$s Custom about page or social media page link%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_user_link',
					'name'          => 'ft_gallery_user_link',
					'placeholder'   => '',
					'default_value' => '',
				),
				// Show Share.
				array(
					'input_wrap_class' => 'ft-gallery-share',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							__( 'Show Share Options%1$s Appears in the bottom left corner and in popup%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_wp_share',
					'name'             => 'ft_gallery_wp_share',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
				),
				// Show Date.
				array(
					'input_wrap_class' => 'ft-gallery-date',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							__( 'Show Date%1$s Date image was uploaded%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_wp_date',
					'name'             => 'ft_gallery_wp_date',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
				),
				// Show Icon.
				array(
					'input_wrap_class' => 'ft-gallery-icon',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							__( 'Show Wordpress Icon%1$s Appears in the top left corner%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_wp_icon',
					'name'             => 'ft_gallery_wp_icon',
					'default_value'    => 'no',
					'options'          => array(
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
				),

				// Words per photo caption
				// array(
				// 'option_type' => 'input',
				// 'label' => __('# of words per photo caption', 'feed-them-gallery') . '<br/><small>' . __('Typing 0 removes the photo caption', 'feed-them-gallery') . '</small>',
				// 'type' => 'hidden',
				// 'id' => 'ft_gallery_word_count_option',
				// 'name' => 'ft_gallery_word_count_option',
				// 'placeholder' => '',
				// 'default_value' => '',
				// ),
				// Image Sizes on page.
				array(
					'input_wrap_class'   => 'ft-images-sizes-page',
					'option_type'        => 'ft-images-sizes-page',
					'instructional-text' =>
						sprintf(
							__( '%1$sNOTE:%2$s If for some reason the image size you choose does not appear on the front end you may need to regenerate your images. This free plugin called %3$sRegenerate Thumbnails%4$s does an amazing job of that.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>',
							'<a href="' . esc_url( 'plugin-install.php?s=regenerate+thumbnails&tab=search&type=term' ) . '" target="_blank">',
							'</a>'
						),
					'label'              => __( 'Image Size on Page', 'feed-them-gallery' ),
					'class'              => 'ft-gallery-images-sizes-page',
					'type'               => 'select',
					'id'                 => 'ft_gallery_images_sizes_page',
					'name'               => 'ft_gallery_images_sizes_page',
					'default_value'      => 'medium',
					'placeholder'        => '',
					'autocomplete'       => 'off',
				),

				// Max-width for Images & Videos.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Max-width for Images', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_max_image_vid_width',
					'name'          => 'ft_gallery_max_image_vid_width',
					'placeholder'   => '500px',
					'default_value' => '',
				),
				// Gallery Width.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Gallery Max-width', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_width',
					'name'          => 'ft_gallery_width',
					'placeholder'   => '500px',
					'default_value' => '',
				),
				// Gallery Height for scrolling feeds using Post format only, this does not work for grid or gallery options except gallery squared because it does not use masonry. For all others it will be hidden.
				array(
					'input_wrap_class' => 'ft-gallery-height',
					'option_type'      => 'input',
					'label'            =>
						sprintf(
							__( 'Gallery Height%1$s Set the height to have a scrolling feed. Only works for Responsive Image Gallery and the Image Post option.%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_height',
					'name'             => 'ft_gallery_height',
					'placeholder'      => '600px',
					'default_value'    => '',
				),
				// Gallery Margin.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							__( 'Gallery Margin%1$s To center feed type auto%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_margin',
					'name'          => 'ft_gallery_margin',
					'placeholder'   => 'auto',
					'default_value' => 'auto',
				),
				// Gallery Padding.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Gallery Padding', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_padding',
					'name'          => 'ft_gallery_padding',
					'placeholder'   => '10px',
					'default_value' => '',
				),
				// ******************************************
				// Gallery Popup
				// ******************************************
				// Display Photos in Popup
				array(
					'grouped_options_title' => __( 'Popup', 'feed-them-gallery' ),
					'option_type'           => 'select',
					'label'                 => __( 'Display Photos in Popup', 'feed-them-gallery' ),
					'type'                  => 'text',
					'id'                    => 'ft_gallery_popup',
					'name'                  => 'ft_gallery_popup',
					'default_value'         => 'yes',
					'options'               => array(
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
					'sub_options'           => array(
						'sub_options_wrap_class' => 'facebook-popup-wrap',
					),
					'sub_options_end'       => true,
				),
				// Image Sizes in popup.
				array(
					'input_wrap_class'   => 'ft-images-sizes-popup',
					'option_type'        => 'ft-images-sizes-popup',
					'instructional-text' =>
						sprintf(
							__( '%1$sNOTE:%2$s If for some reason the image size you choose does not appear on the front end you may need to regenerate your images. This free plugin called %3$sRegenerate Thumbnails%4$s does an amazing job of that.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>',
							'<a href="' . esc_url( 'plugin-install.php?s=regenerate+thumbnails&tab=search&type=term' ) . '" target="_blank">',
							'</a>'
						),
					'label'              => __( 'Image Size in Popup', 'feed-them-gallery' ),
					'class'              => 'ft-gallery-images-sizes-popup',
					'type'               => 'select',
					'id'                 => 'ft_gallery_images_sizes_popup',
					'name'               => 'ft_gallery_images_sizes_popup',
					'default_value'      => '',
					'placeholder'        => '',
					'autocomplete'       => 'off',
				),
				array(
					'input_wrap_class' => 'ft-popup-display-options',
					'option_type'      => 'select',
					'label'            => __( 'Popup Options', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_popup_display_options',
					'name'             => 'ft_popup_display_options',
					'default_value'    => 'no',
					'options'          => array(
						array(
							'label' => __( 'Default', 'feed-them-gallery' ),
							'value' => 'default',
						),
						array(
							'label' => __( 'Full Width & Info below Photo', 'feed-them-gallery' ),
							'value' => 'full-width-second-half-bottom',
						),
						array(
							'label' => __( 'Full Width, Photo Only', 'feed-them-gallery' ),
							'value' => 'full-width-photo-only',
						),
					),
				),

				// ******************************************
				// Gallery Load More Options
				// ******************************************
				// Load More Button
				array(
					'grouped_options_title' => __( 'Load More Images', 'feed-them-gallery' ),
					'option_type'           => 'select',
					'label'                 =>
						sprintf(
							__( 'Load More Button%1$s Load More unavailable while using the Pagination option.%2$s', 'feed-them-gallery' ),
							'<br/><small class="ftg-loadmore-notice-colored" style="display: none;">',
							'</small>'
						),
					'type'                  => 'text',
					'id'                    => 'ft_gallery_load_more_option',
					'name'                  => 'ft_gallery_load_more_option',
					'default_value'         => 'no',
					'options'               => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
					'sub_options'           => array(
						'sub_options_wrap_class' => 'facebook-loadmore-wrap',
					),
				),

				// # of Photos
				array(

					'option_type'   => 'input',
					'label'         => __( '# of Photos Visible', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_photo_count',
					'name'          => 'ft_gallery_photo_count',
					'default_value' => '',
					'placeholder'   => '',
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output).
					'sub_options'   => array(
						'sub_options_wrap_class' => 'fts-facebook-load-more-options-wrap',
					),

				),

				// Load More Style.
				array(
					'option_type'        => 'select',
					'label'              => __( 'Load More Style', 'feed-them-gallery' ),
					'type'               => 'text',
					'id'                 => 'ft_gallery_load_more_style',
					'name'               => 'ft_gallery_load_more_style',
					'instructional-text' =>
						sprintf(
							__( '%1$sNOTE:%2$s The Button option will show a "Load More Posts" button under your feed. The AutoScroll option will load more posts when you reach the bottom of the feed. AutoScroll ONLY works if you\'ve filled in a Fixed Height for your feed.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>'
						),
					'default_value'      => 'button',
					'options'            => array(
						1 => array(
							'label' => __( 'Button', 'feed-them-gallery' ),
							'value' => 'button',
						),
						2 => array(
							'label' => __( 'AutoScroll', 'feed-them-gallery' ),
							'value' => 'autoscroll',
						),
					),
					'sub_options_end'    => true,
				),

				// Load more Button Width.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							__( 'Load more Button Width%1$s Leave blank for auto width%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_loadmore_button_width',
					'name'          => 'ft_gallery_loadmore_button_width',
					'placeholder'   => '300px ' . __( 'for example', 'feed-them-gallery' ),
					'default_value' => '300px',
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
					'sub_options'   => array(
						'sub_options_wrap_class' => 'fts-facebook-load-more-options2-wrap',
					),
				),
				// Load more Button Margin.
				array(
					'option_type'     => 'input',
					'label'           => __( 'Load more Button Margin', 'feed-them-gallery' ),
					'type'            => 'text',
					'id'              => 'ft_gallery_loadmore_button_margin',
					'name'            => 'ft_gallery_loadmore_button_margin',
					'placeholder'     => '10px ' . __( 'for example', 'feed-them-gallery' ),
					'default_value'   => '10px',
					'value'           => '',
					'sub_options_end' => 2,
				),

				// ******************************************
				// Gallery Image Count Options
				// ******************************************
				// Load More Style
				array(
					'option_type'        => 'select',
					'label'              => __( 'Show Image Count', 'feed-them-gallery' ),
					'type'               => 'text',
					'id'                 => 'ft_gallery_show_pagination',
					'name'               => 'ft_gallery_show_pagination',
					'instructional-text' =>
						sprintf(
							__( '%1$sNOTE:%2$s This will display the number of images you have in your gallery, and will appear centered at the bottom of your image feed. For Example: 4 of 50 (4 being the number of images you have loaded on the page already and 50 being the total number of images in the gallery.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>'
						),
					'default_value'      => 'yes',
					'options'            => array(
						1 => array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						2 => array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output).
					'sub_options'        => array(
						'sub_options_wrap_class' => 'fts-facebook-load-more-options-wrap',
					),
					'sub_options_end'    => true,
				),

				// ******************************************
				// Gallery Sort Options
				// ******************************************
				array(
					'grouped_options_title' => __( 'Order of Images', 'feed-them-gallery' ),
					'option_type'           => 'select',
					'label'                 => __( 'Choose the order of Images', 'feed-them-gallery' ),
					'type'                  => 'text',
					'id'                    => 'ftg_sort_type',
					'name'                  => 'ftg_sort_type',
					'default_value'         => 'above-below',
					'options'               => array(
						1 => array(
							'label' => __( 'The order you manually sorted images', 'feed-them-gallery' ),
							'value' => 'menu_order',
						),
						2 => array(
							'label' => __( 'Sort alphabetically (A-Z)', 'feed-them-gallery' ),
							'value' => 'title',
						),
						3 => array(
							'label' => __( 'Sort by date', 'feed-them-gallery' ),
							'value' => 'date',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Display Options', 'feed-them-gallery' ),
					'label'         =>
						sprintf(
							__( 'Display Options%1$s Display a select option for this gallery so your users can select the sort order.%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ftg_sorting_options',
					'name'          => 'ftg_sorting_options',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Position of Select Option', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg_position_of_sort_select',
					'name'          => 'ftg_position_of_sort_select',
					'default_value' => 'above-below',
					'options'       => array(
						1 => array(
							'label' => __( 'Top', 'feed-them-gallery' ),
							'value' => 'above',
						),
						2 => array(
							'label' => __( 'Bottom', 'feed-them-gallery' ),
							'value' => 'below',
						),
						3 => array(
							'label' => __( 'Top and Bottom', 'feed-them-gallery' ),
							'value' => 'above-below',
						),
					),
					'sub_options'   => array(
						'sub_options_wrap_class' => 'ftg-sorting-options-wrap',
					),
				),

				array(
					'option_type'     => 'select',
					'label'           => __( 'Align Select Option', 'feed-them-gallery' ),
					'type'            => 'text',
					'id'              => 'ftg_align_sort_select',
					'name'            => 'ftg_align_sort_select',
					'default_value'   => 'left',
					'options'         => array(
						1 => array(
							'label' => __( 'Left', 'feed-them-gallery' ),
							'value' => 'left',
						),
						2 => array(
							'label' => __( 'Right', 'feed-them-gallery' ),
							'value' => 'right',
						),
					),
					'sub_options_end' => true,
				),

				// ******************************************
				// Download Free Image Button Sort Options
				// ******************************************
				array(
					'grouped_options_title' => __( 'Free Image Download', 'feed-them-gallery' ),
					'option_type'           => 'ftg-free-download-size',
					'instructional-text'    =>
						sprintf(
							__( '%1$sNOTE:%2$s To turn this option on simply choose an image size. A download icon will appear under the image to the right and in the popup. If for some reason the image size you choose does not appear on the front end you may need to regenerate your images. This free plugin called %3$sRegenerate Thumbnails%4$s does an amazing job of that.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>',
							'<a href="' . esc_url( 'plugin-install.php?s=regenerate+thumbnails&tab=search&type=term' ) . '" target="_blank">',
							'</a>'
						),
					'label'                 => __( 'Choose the size', 'feed-them-gallery' ),
					'class'                 => 'ft-images-sizes-free-download-button',
					'type'                  => 'select',
					'id'                    => 'ftg_free_download_size',
					'name'                  => 'ftg_free_download_size',
					'default_value'         => '',
					'placeholder'           => '',
					'autocomplete'          => 'off',
				),

				array(
					'option_type'   => 'input',
					'label'         => __( 'Free Download Text', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_free_download_text',
					'name'          => 'ft_gallery_free_download_text',
					'placeholder'   => 'Free Download',
					'default_value' => '',
					'value'         => '',
				),

			),

		);

		return $this->all_options['layout'];
	} //END LAYOUT OPTIONS

	/**
	 * Color Options
	 *
	 * Options for the Color Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function color_options() {
		$this->all_options['colors'] = array(
			'section_attr_key'   => 'facebook_',
			'section_title'      => __( 'Feed Color Options', 'feed-them-gallery' ),
			'section_wrap_class' => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'  => 'fb-page-shortcode-form',
			'form_wrap_id'       => 'fts-fb-page-form',
			'main_options'       => array(

				// Feed Background Color.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Background Color', 'feed-them-gallery' ),
					'class'         => 'ft-gallery-feed-background-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-feed-background-color-input',
					'name'          => 'ft_gallery_feed_background_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Feed Grid Background Color.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Grid Posts Background Color', 'feed-them-gallery' ),
					'class'         => 'fb-feed-grid-posts-background-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-grid-posts-background-color-input',
					'name'          => 'ft_gallery_grid_posts_background_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Border Bottom Color.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Border Bottom Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-border-bottom-color-input',
					'name'          => 'ft_gallery_border_bottom_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Loadmore background Color.
				array(
					'grouped_options_title' => __( 'Loadmore Button', 'feed-them-gallery' ),
					'option_type'           => 'input',
					'label'                 => __( 'Background Color', 'feed-them-gallery' ),
					'class'                 => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'                  => 'text',
					'id'                    => 'ft-gallery-loadmore-background-color-input',
					'name'                  => 'ft_gallery_loadmore_background_color',
					'default_value'         => '',
					'placeholder'           => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'          => 'off',
				),
				// Loadmore background Color.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Text Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-loadmore-text-color-input',
					'name'          => 'ft_gallery_loadmore_text_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Loadmore Count Color.
				array(
					'option_type'   => 'input',
					'label'         => __( 'Image Count Text Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-loadmore-count-text-color-input',
					'name'          => 'ft_gallery_loadmore_count_text_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

			),
		);

		return $this->all_options['colors'];
	} //END LAYOUT OPTIONS

	/**
	 * Woocommerce Options
	 *
	 * Options for the Woocommerce Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function woocommerce_options() {

		$this->all_options['woocommerce'] = array(
			// required_prem_plugin must match the array key returned in ft_gallery_required_plugins function.
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'input_wrap_class'     => 'ft-woocommerce-styles',
			'section_attr_key'     => 'woocommerce_',
			'section_title'        => __( 'Woocommerce Options', 'feed-them-gallery' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			'main_options'         => array(
				// Show Purchase Button.
				array(
					'input_wrap_class' => 'ft-gallery-purchase-link',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							__( 'Show Purchase Link%1$s Appears on the page and popup. Only appears in popup for the Responsive Image Gallery.%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_purchase_link',
					'name'             => 'ft_gallery_purchase_link',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
				),
				// Purchase Button Text.
				array(
					'option_type'   => 'input',
					'label'         =>
						sprintf(
							__( 'Change Purchase Link text%1$s The default word is Purchase or add a single space to show the cart icon only.%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_purchase_word',
					'name'          => 'ft_gallery_purchase_word',
					'placeholder'   => __( 'Purchase', 'feed-them-gallery' ),
					'default_value' => '',
				),
				array(
					'option_type'   => 'checkbox',
					'label'         =>
						sprintf(
							__( 'Auto Create a product for each image uploaded.%1$s You must have a "Global Model Product" or "Smart Image Orientation Model Product" selected below for this option to work.%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'class'         => 'ft-gallery-auto-image-woo-prod',
					'type'          => 'checkbox',
					'id'            => 'ft_gallery_auto_image_woo_prod',
					'name'          => 'ft_gallery_auto_image_woo_prod',
					'default_value' => '',

				),
				array(
					// this is just in place to save the option. Using jquery to move the checkbox to the other
					// smart image area options
					'input_wrap_class' => 'ft-gallery-smart-image-checkbox-wrap',
					'option_type'      => 'checkbox',
					'label'            => '',
					'class'            => 'ft-gallery-smart-image-orient-prod',
					'type'             => 'checkbox',
					'id'               => 'ft_gallery_smart_image_orient_prod',
					'name'             => 'ft_gallery_smart_image_orient_prod',
					'default_value'    => '',

				),

			),
		);

		return $this->all_options['woocommerce'];
	}

	/**
	 * Woocommerce Extra Options
	 *
	 * These are Gallery to Woo options (just for saving not for display)
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function woocommerce_extra_options() {

		$this->all_options['woocommerce_exta'] = array(
			'main_options' => array(
				// required_prem_plugin must match the array key returned in ft_gallery_required_plugins function.
				'required_prem_plugin' => 'feed_them_gallery_premium',
				// ******************************************
				// Images to Products
				// ******************************************
				// Automatically turn created Images to products.
				array(
					'option_type'   => 'checkbox',
					'default_value' => '',
					'name'          => 'ft_gallery_auto_image_woo_prod',
				),
				array(
					'option_type'   => 'checkbox',
					'default_value' => '',
					'name'          => 'ft_gallery_smart_image_orient_prod',
				),
				array(
					'option_type'   => 'select',
					'default_value' => '',
					'name'          => 'ft_gallery_image_to_woo_model_prod',
				),
				array(
					'option_type'   => 'select',
					'default_value' => '',
					'name'          => 'ft_gallery_landscape_to_woo_model_prod',
				),
				array(
					'option_type'   => 'select',
					'default_value' => '',
					'name'          => 'ft_gallery_square_to_woo_model_prod',
				),
				array(
					'option_type'   => 'select',
					'default_value' => '',
					'name'          => 'ft_gallery_portrait_to_woo_model_prod',
				),

				array(
					'option_type'   => 'select',
					'default_value' => '',
					'name'          => 'ft_gallery_zip_to_woo_model_prod',
				),
			),
		);

		return $this->all_options['woocommerce_exta'];
	}

	/**
	 * Watermark Options
	 *
	 * Options for the Watermark Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function watermark_options() {
		$this->all_options['watermark'] = array(
			// required_prem_plugin must match the array key returned in ft_gallery_required_plugins function.
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'section_attr_key'     => 'facebook_',
			'section_title'        => __( 'Watermark Options', 'feed-them-gallery' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			'main_options'         => array(
				// Disable Right Click.
				array(
					'input_wrap_class'   => 'ft-watermark-disable-right-click',
					'instructional-text' =>
						sprintf(
							__( '%1$sNOTE:%2$s This option will disable the right click option on desktop computers so people cannot look at the source code. This is not fail safe but for the vast majority this is enough to deter people from trying to find the image source.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>'
						),
					'option_type'        => 'select',
					'label'              => __( 'Disable Right Click', 'feed-them-gallery' ),
					'type'               => 'text',
					'id'                 => 'ft_gallery_watermark_disable_right_click',
					'name'               => 'ft_gallery_watermark_disable_right_click',
					'default_value'      => '',
					'options'            => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
				),
				// Use Watermark Options.
				array(
					'input_wrap_class' => 'ft-watermark-enable-options',
					'option_type'      => 'select',
					'label'            => __( 'Use Options Below', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_watermark_enable_options',
					'name'             => 'ft_gallery_watermark_enable_options',
					'default_value'    => 'no',
					'options'          => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
				),

				// Choose Watermark Image.
				array(
					'option_type'        => 'input',
					'instructional-text' =>
						sprintf(
							__( '%1$sNOTE:%2$s Upload the exact image size you want to display, we will not rescale the image in anyway.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>'
						),
					'label'              => __( 'Watermark Image', 'feed-them-gallery' ),
					'id'                 => 'ft-watermark-image',
					'name'               => 'ft-watermark-image',
					'class'              => '',
					'type'               => 'button',
					'default_value'      => __( 'Upload or Choose Watermark', 'feed-them-gallery' ),
					'placeholder'        => '',
					'value'              => '',
					'autocomplete'       => 'off',
				),
				// Watermark Image Link for front end if user does not use imagick or GD library method.
				array(
					'input_wrap_class' => 'ft-watermark-hide-these-options',
					'option_type'      => 'input',
					// 'label' => __('Watermark Image', 'feed-them-gallery'),
					// 'class' => 'fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'             => 'hidden',
					'id'               => 'ft_watermark_image_input',
					// 'instructional-text' => '<strong>' . __('NOTE:', 'feed-them-gallery') . '</strong> ' . __('Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery'),
					'name'             => 'ft_watermark_image_input',
					'default_value'    => '',
					// 'placeholder' => __('', 'feed-them-gallery'),
					'autocomplete'     => 'off',
				),
				// Watermark Image ID so we can pass it to merge the watermark over images.
				array(
					'input_wrap_class' => 'ft-watermark-hide-these-options',
					'option_type'      => 'input',
					// 'label' => __('Watermark Image', 'feed-them-gallery'),
					// 'class' => 'fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'             => 'hidden',
					'id'               => 'ft_watermark_image_id',
					// 'instructional-text' => '<strong>' . __('NOTE:', 'feed-them-gallery') . '</strong> ' . __('Define the Width of each post and the Space between each post below. You must add px after any number.', 'feed-them-gallery'),
					'name'             => 'ft_watermark_image_id',
					'default_value'    => '',
					// 'placeholder' => __('', 'feed-them-gallery'),
					'autocomplete'     => 'off',
				),

				// Watermark Options
				array(
					'input_wrap_class' => 'ft-watermark-enabled',
					'option_type'      => 'select',
					'label'            => __( 'Watermark Type', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_watermark',
					'name'             => 'ft_gallery_watermark',
					'default_value'    => 'yes',
					'options'          => array(
						array(
							'label' => __( 'Watermark Overlay Image (Does not Imprint logo on Image)', 'feed-them-gallery' ),
							'value' => 'overlay',
						),
						array(
							'label' => __( 'Watermark Image (Imprint logo on the selected image sizes)', 'feed-them-gallery' ),
							'value' => 'imprint',
						),
					),
				),

				// Watermark Options
				array(
					'input_wrap_class' => 'ft-watermark-overlay-options',
					'option_type'      => 'select',
					'label'            => __( 'Overlay Options', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_watermark',
					'name'             => 'ft_gallery_watermark_overlay_enable',
					'default_value'    => 'popup-only',
					'options'          => array(
						array(
							'label' => __( 'Select an Option', 'feed-them-gallery' ),
							'value' => '',
						),
						array(
							'label' => __( 'Watermark in popup only', 'feed-them-gallery' ),
							'value' => 'popup-only',
						),
						array(
							'label' => __( 'Watermark for image on page only', 'feed-them-gallery' ),
							'value' => 'page-only',
						),
						array(
							'label' => __( 'Watermark for image on page and popup', 'feed-them-gallery' ),
							'value' => 'page-and-popup',
						),
					),
				),

				// Hidden Input to set array
				array(
					'input_wrap_class'   => 'ft-watermark-hidden-options ft-gallery-image-sizes-checkbox-wrap-label',
					'option_type'        => 'checkbox-image-sizes',
					'instructional-text' =>
						sprintf(
							__( '%1$sIMPORTANT:%2$s This option will permanently mark your chosen image size once you click the publish button or update button. Set the opacity of your %3$sWatermark Image%4$s before you upload it above for this option. We suggest using a png for the best clarity and not a gif.', 'feed-them-gallery' ),
							'<strong>',
							'</strong>',
							'<strong>',
							'</strong>'
						),
					'label'              => __( 'Image Sizes', 'feed-them-gallery' ),
					'class'              => 'ft-watermark-opacity',
					'type'               => 'hidden',
					'id'                 => 'ft_watermark_image_sizes',
					'name'               => 'ft_watermark_image_sizes',
					'default_value'      => '',
					'value'              => '',
					'placeholder'        => __( '', 'feed-them-gallery' ),
					'autocomplete'       => 'off',
				),

				// Watermark Image Sizes to convert
				array(
					'input_wrap_class' => 'ft-watermark-hidden-options ft-gallery-image-sizes-checkbox-wrap',
					'option_type'      => 'checkbox-dynamic-image-sizes',
					'label'            => __( '', 'feed-them-gallery' ),
					'class'            => 'ft-watermark-opacity',
					'type'             => 'checkbox',
					'id'               => 'ft_watermark_image_',
					'name'             => '',
					'default_value'    => '',
					'placeholder'      => __( '', 'feed-them-gallery' ),
					'autocomplete'     => 'off',
				),
				// Duplicate Full Image before it is watermarked, usefull if zip option is being used and or selling full image
				array(
					'input_wrap_class' => 'ft-watermark-duplicate-image',
					'option_type'      => 'select',
					'label'            =>
						sprintf(
							__( 'Duplicate Full Image%1$s before watermarking', 'feed-them-gallery' ),
							'<br/>'
						),
					'type'             => 'text',
					'id'               => 'ft_gallery_duplicate_image',
					'name'             => 'ft_gallery_duplicate_image',
					'default_value'    => '',
					'options'          => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
				),
				// Watermark Opacity
				array(
					'input_wrap_class' => 'ft-gallery-watermark-opacity',
					'option_type'      => 'input',
					'label'            => __( 'Image Opacity', 'feed-them-gallery' ),
					'class'            => 'ft-watermark-opacity',
					'type'             => 'text',
					'id'               => 'ft_watermark_image_opacity',
					'name'             => 'ft_watermark_image_opacity',
					'default_value'    => '',
					'placeholder'      => __( '.5 for example', 'feed-them-gallery' ),
					'autocomplete'     => 'off',
				),
				// Watermark Position
				array(
					'input_wrap_class' => 'ft-watermark-position',
					'option_type'      => 'select',
					'label'            => __( 'Watermark Position', 'feed-them-gallery' ),
					'type'             => 'text',
					'id'               => 'ft_gallery_position',
					'name'             => 'ft_gallery_position',
					'default_value'    => 'bottom-right',
					'options'          => array(
						array(
							'label' => __( 'Centered', 'feed-them-gallery' ),
							'value' => 'center',
						),
						array(
							'label' => __( 'Top Right', 'feed-them-gallery' ),
							'value' => 'top-right',
						),
						array(
							'label' => __( 'Top Left', 'feed-them-gallery' ),
							'value' => 'top-left',
						),
						array(
							'label' => __( 'Top Center', 'feed-them-gallery' ),
							'value' => 'top-center',
						),
						array(
							'label' => __( 'Bottom Right', 'feed-them-gallery' ),
							'value' => 'bottom-right',
						),
						array(
							'label' => __( 'Bottom Left', 'feed-them-gallery' ),
							'value' => 'bottom-left',
						),
						array(
							'label' => __( 'Bottom Center', 'feed-them-gallery' ),
							'value' => 'bottom-center',
						),
					),
				),
				// watermark Image Margin
				array(
					'option_type'   => 'input',
					'label'         => __( 'Watermark Margin', 'feed-them-gallery' ),
					'class'         => 'ft-watermark-image-margin',
					'type'          => 'text',
					'id'            => 'ft_watermark_image_margin',
					'name'          => 'ft_watermark_image_margin',
					'default_value' => '10px',
					'placeholder'   => __( '10px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
			),
		);

		return $this->all_options['watermark'];
	} //END WATERMARK OPTIONS

	/**
	 * Pagination Options
	 *
	 * Options for the Pagination Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function pagination_options() {
		$this->all_options['pagination'] = array(
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'section_attr_key'     => 'facebook_',
			'section_title'        => __( 'Pagination', 'feed-them-gallery' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info.
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			// Token Check // We'll use these option for premium messages in the future.
			'premium_msg_boxes'    => array(
				'album_videos' => array(
					'req_plugin' => 'fts_premium',
					'msg'        => '',
				),
				'reviews'      => array(
					'req_plugin' => 'facebook_reviews',
					'msg'        => '',
				),
			),

			'main_options'         => array(

				// ******************************************
				// Gallery Pagination Options
				// ******************************************
				array(
					'option_type'   => 'select',
					'label'         => __( 'Display Pagination', 'feed-them-gallery' ),
					'label'         =>
						sprintf(
							__( 'Display Pagination%1$s Pagination unavailable while using the Load More option.%2$s', 'feed-them-gallery' ),
							'<br/><small class="ftg-pagination-notice-colored" style="display: none;">',
							'</small>'
						),
					'type'          => 'text',
					'id'            => 'ft_gallery_show_true_pagination',
					'name'          => 'ft_gallery_show_true_pagination',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
				),

				// # of Photos
				array(

					'option_type'   => 'input',
					'label'         => __( '# of Photos Visible', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_pagination_photo_count',
					'name'          => 'ft_gallery_pagination_photo_count',
					'default_value' => '',
					'placeholder'   => __( '', 'feed-them-gallery' ),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Position of Pagination', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft_gallery_position_of_pagination',
					'name'          => 'ft_gallery_position_of_pagination',
					'default_value' => 'above-below',
					'options'       => array(
						1 => array(
							'label' => __( 'Top', 'feed-them-gallery' ),
							'value' => 'above',
						),
						2 => array(
							'label' => __( 'Bottom', 'feed-them-gallery' ),
							'value' => 'below',
						),
						3 => array(
							'label' => __( 'Top and Bottom', 'feed-them-gallery' ),
							'value' => 'above-below',
						),
					),
					// This should be placed in the STARTING field of sub options that way wrap and instruction text is above this div (end will be in final options for div output)
					// 'sub_options' => array(
					// 'sub_options_wrap_class' => 'ftg-pagination-options-wrap',
					// ),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Align Pagination', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg_align_pagination',
					'name'          => 'ftg_align_pagination',
					'default_value' => 'right',
					'options'       => array(
						1 => array(
							'label' => __( 'Left', 'feed-them-gallery' ),
							'value' => 'left',
						),
						2 => array(
							'label' => __( 'Right', 'feed-them-gallery' ),
							'value' => 'right',
						),
					),
				),
				// Pagination Color
				// JUST NEED TO FINISH THE COLOR OPTIONS FOR THE PAGINATION AND APPLY THEM TO THE FRONT END
				// Loadmore background Color
				array(
					'option_type'   => 'input',
					'label'         => __( 'Button Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-pagination-background-color-input',
					'name'          => 'ft_gallery_pagination_button_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => __( 'Active Button', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-pagination-background-color-input',
					'name'          => 'ft_gallery_pagination_active_button_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Loadmore background Color
				array(
					'option_type'   => 'input',
					'label'         => __( 'Numbers Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-pagination-text-color-input',
					'name'          => 'ft_gallery_pagination_text_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

				array(
					'grouped_options_title' => __( 'Image Count Options', 'feed-them-gallery' ),
					'option_type'           => 'select',
					'label'                 =>
						sprintf(
							__( 'Display Image Count%1$s For Example: Showing 1-50 of 800 Images.%2$s', 'feed-them-gallery' ),
							'<br/><small>',
							'</small>'
						),
					'type'                  => 'text',
					'id'                    => 'ftg_display_image_count',
					'name'                  => 'ftg_display_image_count',
					'default_value'         => 'yes',
					'options'               => array(
						1 => array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
						2 => array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Align Image Count', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg_align_count',
					'name'          => 'ftg_align_count',
					'default_value' => 'left',
					'options'       => array(
						1 => array(
							'label' => __( 'Left', 'feed-them-gallery' ),
							'value' => 'left',
						),
						2 => array(
							'label' => __( 'Right', 'feed-them-gallery' ),
							'value' => 'right',
						),
					),
				),

				array(
					'option_type'   => 'input',
					'label'         => __( 'Image count Text Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-gallery-true-pagination-count-text-color-input',
					'name'          => 'ft_gallery_true_pagination_count_text_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

			),
		);

		return $this->all_options['pagination'];
	} //END PAGINATION OPTIONS


	/**
	 * Tags Options
	 *
	 * Options for the Tags Tab
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function tags_options() {
		$this->all_options['tags'] = array(
			'required_prem_plugin' => 'feed_them_gallery_premium',
			'section_attr_key'     => 'facebook_',
			'section_title'        => __( 'Image Tags', 'feed-them-gallery' ),
			'section_wrap_class'   => 'ftg-section-options',
			// Form Info
			'form_wrap_classes'    => 'fb-page-shortcode-form',
			'form_wrap_id'         => 'fts-fb-page-form',
			// Token Check // We'll use these option for premium messages in the future
			'premium_msg_boxes'    => array(
				'album_videos' => array(
					'req_plugin' => 'fts_premium',
					'msg'        => '',
				),
				'reviews'      => array(
					'req_plugin' => 'facebook_reviews',
					'msg'        => '',
				),
			),

			'main_options'         => array(

				// ******************************************
				// Gallery Tags Options
				// ******************************************
				array(
					'option_type'   => 'select',
					'label'         => __( 'Show Image Tags', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-gallery-show-tags',
					'name'          => 'ft_gallery_show_tags',
					'default_value' => 'no',
					'options'       => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Yes', 'feed-them-gallery' ),
							'value' => 'yes',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Tags Separator', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg-image-tags-separator',
					'name'          => 'ftg_image_tags_separator',
					'default_value' => '',
					'options'       => array(
						array(
							'label' => __( 'Comma - ie* One, Two, Three', 'feed-them-gallery' ),
							'value' => ',&nbsp;',
						),
						array(
							'label' => __( 'Period - ie* One &#46; Two &#46; Three', 'feed-them-gallery' ),
							'value' => '&nbsp;&#46;&nbsp;',
						),
						array(
							'label' => __( 'Bullet - ie* One &bull; Two &bull; Three', 'feed-them-gallery' ),
							'value' => '&nbsp;&bull;&nbsp;',
						),
						array(
							'label' => __( 'Pipe - ie* One | Two | Three', 'feed-them-gallery' ),
							'value' => '&nbsp;|&nbsp;',
						),
						array(
							'label' => __( 'Space - ie* One Two Three', 'feed-them-gallery' ),
							'value' => '&nbsp;',
						),
						array(
							'label' => __( 'Dash - ie* One - Two - Three', 'feed-them-gallery' ),
							'value' => '&nbsp;-&nbsp;',
						),
					),
				),

				array(
					'option_type'   => 'input',
					'label'         => __( 'Link Font Size', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-tags-text-size',
					'name'          => 'ft_tags_text_size',
					'default_value' => '',
					'placeholder'   => __( '12px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Tags Link Color
				array(
					'option_type'   => 'input',
					'label'         => __( 'Link Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-tags-link-color',
					'name'          => 'ft_tags_link_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'select',
					'label'         => __( 'Align', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg-align-tags',
					'name'          => 'ftg_align_tags',
					'default_value' => '',
					'options'       => array(
						1 => array(
							'label' => __( 'Left', 'feed-them-gallery' ),
							'value' => 'left',
						),
						2 => array(
							'label' => __( 'Right', 'feed-them-gallery' ),
							'value' => 'right',
						),
						3 => array(
							'label' => __( 'Center', 'feed-them-gallery' ),
							'value' => 'center',
						),
					),
				),
				/*
				array(
					'option_type' => 'input',
					'label' => __('Link Margin Right', 'feed-them-gallery'),
					'type' => 'text',
					'id' => 'ft-tags-link-margin-right',
					'name' => 'ft_tags_link_margin_right',
					'default_value' => '',
					'placeholder' => __('5px', 'feed-them-gallery'),
					'autocomplete' => 'off',
				),*/

				// Tags Background Color
				array(
					'grouped_options_title' => __( 'Image Tags Background Wrap', 'feed-them-gallery' ),
					'option_type'           => 'input',
					'label'                 => __( 'Color', 'feed-them-gallery' ),
					'class'                 => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'                  => 'text',
					'id'                    => 'ft-tags-background-color-input',
					'name'                  => 'ft_gallery_tags_background_color',
					'default_value'         => '',
					'placeholder'           => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'          => 'off',
				),

				// Tags Background Color
				array(
					'option_type'   => 'input',
					'label'         => __( 'Padding', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-tags-padding',
					'name'          => 'ft_gallery_tags_padding',
					'default_value' => '',
					'placeholder'   => __( '18px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

				// Tags Text
				array(
					'grouped_options_title' => __( 'Customize the word, Tags:', 'feed-them-gallery' ),
					'option_type'           => 'input',
					'label'                 => __( 'Change Tags Text', 'feed-them-gallery' ),
					'type'                  => 'text',
					'id'                    => 'ftg-image-tags-text',
					'name'                  => 'ftg_image_tags_text',
					'default_value'         => '',
					'placeholder'           => __( 'Tags:', 'feed-them-gallery' ),
					'autocomplete'          => 'off',
				),
				// Tags Text Size
				array(
					'option_type'   => 'input',
					'label'         => __( 'Font Size', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-tags-text-size',
					'name'          => 'ft_gallery_tags_text_size',
					'default_value' => '',
					'placeholder'   => __( '12px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => __( 'Font Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-tags-text-color',
					'name'          => 'ft_tags_text_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => __( ' Margin Right', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-tags-text-margin-right',
					'name'          => 'ft_tags_text_margin_right',
					'default_value' => '',
					'placeholder'   => __( '5px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

				// ******************************************
				// Gallery Gallery Tags Options
				// ******************************************
				array(
					'grouped_options_title' => __( 'Gallery Tags', 'feed-them-gallery' ),
					'option_type'           => 'select',
					'label'                 => __( 'Show Gallery Tags', 'feed-them-gallery' ),
					'type'                  => 'text',
					'id'                    => 'ft-gallery-show-page-tags',
					'name'                  => 'ft_gallery_show_page_tags',
					'default_value'         => 'no',
					'options'               => array(
						array(
							'label' => __( 'No', 'feed-them-gallery' ),
							'value' => 'no',
						),
						array(
							'label' => __( 'Above Images', 'feed-them-gallery' ),
							'value' => 'above_images',
						),
						array(
							'label' => __( 'Below Images', 'feed-them-gallery' ),
							'value' => 'below_images',
						),
					),
				),

				array(
					'option_type'   => 'select',
					'label'         => __( 'Tags Separator', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg-page-tags-separator',
					'name'          => 'ftg_page_tags_separator',
					'default_value' => '',
					'options'       => array(
						array(
							'label' => __( 'Comma - ie* One, Two, Three', 'feed-them-gallery' ),
							'value' => ',&nbsp;',
						),
						array(
							'label' => __( 'Period - ie* One &#46; Two &#46; Three', 'feed-them-gallery' ),
							'value' => '&nbsp;&#46;&nbsp;',
						),
						array(
							'label' => __( 'Bullet - ie* One &bull; Two &bull; Three', 'feed-them-gallery' ),
							'value' => '&nbsp;&bull;&nbsp;',
						),
						array(
							'label' => __( 'Pipe - ie* One | Two | Three', 'feed-them-gallery' ),
							'value' => '&nbsp;|&nbsp;',
						),
						array(
							'label' => __( 'Space - ie* One Two Three', 'feed-them-gallery' ),
							'value' => '&nbsp;',
						),
						array(
							'label' => __( 'Dash - ie* One - Two - Three', 'feed-them-gallery' ),
							'value' => '&nbsp;-&nbsp;',
						),
					),
				),

				array(
					'option_type'   => 'input',
					'label'         => __( 'Link Font Size', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-page-tags-text-size',
					'name'          => 'ft_page_tags_text_size',
					'default_value' => '',
					'placeholder'   => __( '12px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				// Tags Link Color
				array(
					'option_type'   => 'input',
					'label'         => __( 'Link Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-page-tags-link-color',
					'name'          => 'ft_page_tags_link_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'select',
					'label'         => __( 'Align', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ftg-align-page-tags',
					'name'          => 'ftg_align_page_tags',
					'default_value' => '',
					'options'       => array(
						1 => array(
							'label' => __( 'Left', 'feed-them-gallery' ),
							'value' => 'left',
						),
						2 => array(
							'label' => __( 'Right', 'feed-them-gallery' ),
							'value' => 'right',
						),
						3 => array(
							'label' => __( 'Center', 'feed-them-gallery' ),
							'value' => 'center',
						),
					),
				),
				/*
				array(
					'option_type' => 'input',
					'label' => __('Link Margin Right', 'feed-them-gallery'),
					'type' => 'text',
					'id' => 'ft-page-tags-link-margin-right',
					'name' => 'ft_page_tags_link_margin_right',
					'default_value' => '',
					'placeholder' => __('5px', 'feed-them-gallery'),
					'autocomplete' => 'off',
				),*/

				// Tags Background Color
				array(
					'grouped_options_title' => __( 'Gallery Tags Background Wrap', 'feed-them-gallery' ),
					'option_type'           => 'input',
					'label'                 => __( 'Color', 'feed-them-gallery' ),
					'class'                 => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'                  => 'text',
					'id'                    => 'ft-page-tags-background-color-input',
					'name'                  => 'ft_page_gallery_tags_background_color',
					'default_value'         => '',
					'placeholder'           => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'          => 'off',
				),

				// Tags Background Color
				array(
					'option_type'   => 'input',
					'label'         => __( 'Padding', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-page-tags-padding',
					'name'          => 'ft_gallery_page_tags_padding',
					'default_value' => '',
					'placeholder'   => __( '18px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

				// Tags Text
				array(
					'grouped_options_title' => __( 'Customize the phrase, Gallery Tags:', 'feed-them-gallery' ),
					'option_type'           => 'input',
					'label'                 => __( 'Change Tags: Text', 'feed-them-gallery' ),
					'type'                  => 'text',
					'id'                    => 'ft-gallery-page-tags-text',
					'name'                  => 'ftg_page_tags_text',
					'default_value'         => '',
					'placeholder'           => __( 'Gallery Tags:', 'feed-them-gallery' ),
					'autocomplete'          => 'off',
				),
				// Tags Text Size
				array(
					'option_type'   => 'input',
					'label'         => __( 'Font Size', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-gallery-page-tags-text-size',
					'name'          => 'ft_gallery_page_tags_text_size',
					'default_value' => '',
					'placeholder'   => __( '12px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => __( 'Font Color', 'feed-them-gallery' ),
					'class'         => 'fb-border-bottom-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:\'#eee\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'white\'}',
					'type'          => 'text',
					'id'            => 'ft-page-tags-text-color',
					'name'          => 'ft_page_tags_text_color',
					'default_value' => '',
					'placeholder'   => __( '#ddd', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),
				array(
					'option_type'   => 'input',
					'label'         => __( ' Margin Right', 'feed-them-gallery' ),
					'type'          => 'text',
					'id'            => 'ft-page-tags-text-margin-right',
					'name'          => 'ft_page_tags_text_margin_right',
					'default_value' => '',
					'placeholder'   => __( '5px', 'feed-them-gallery' ),
					'autocomplete'  => 'off',
				),

			),
		);

		return $this->all_options['tags'];
	} //END TAGS OPTIONS
}
