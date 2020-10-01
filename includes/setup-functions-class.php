<?php
/**
 * Setup Functions Class
 *
 * This class initiates the setup of the Feed Them Gallery plugin
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
class Setup_Functions {

	/**
	 * Global Prefix
	 * Sets Prefix for global options
	 *
	 * @var string
	 */
	public $global_prefix = 'global_';

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
	 * Core_Functions constructor.
	 */
	public function __construct() { }

	/**
	 * Add Action Filters
	 *
	 * Load up all our styles and js.
	 *
	 * @since 1.0.0
	 */
	public function add_actions_filters() {

		// Add Theme Support for post thumbs.
		add_theme_support( 'post-thumbnails' );


        // THIS GIVES US SOME OPTIONS FOR STYLING THE ADMIN AREA.
        add_action( 'admin_enqueue_scripts', array( $this, 'ft_gallery_admin_css' ) );
        // Add Feed Them Gallery Bar to Admin.
        add_action( 'admin_init', array( $this, 'ft_gallery_settings_page_options' ) );

        // Feed Them Gallery Admin Bar.
		add_action( 'wp_before_admin_bar_render', array( $this, 'ft_gallery_admin_bar_menu' ), 999 );

		// Settings option. Add Custom CSS to the header of Feed Them Gallery pages only.
		$ft_gallery_include_custom_css_checked_css = get_option( 'ft-gallery-color-options-settings-custom-css' );
		if ( '1' === $ft_gallery_include_custom_css_checked_css ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'ft_gallery_color_options_head_css' ) );
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'ft_gallery_color_options_head_css_front' ) );

		// Settings option. Add Custom CSS to the header of Feed Them Gallery pages only.
		if ( ftg_get_option( 'use_custom_css' ) )   {
            add_action( 'wp_head', array( $this, 'ft_gallery_head_css' ) );
		}

		// Widget Code to allow shortcodes.
		add_filter( 'widget_text', 'do_shortcode' );

		// Re-order Sub-Menu Items.
		// add_action( 'admin_menu', array( $this, 'ft_gallery_reorder_admin_sub_menus' ) );
		// FTG License Page.
		if ( isset( $_GET['page'] ) && 'ft-gallery-license-page' === $_GET['page'] ) {
			add_action( 'admin_footer', array( $this, 'ftg_plugin_license' ) );
		}
	}

	/**
	 * My FTG Plugin License
	 *
	 * Put in place to only show the Activate Plugin license if the input has a value
	 *
	 * @since 1.0.3
	 */
	public function ftg_plugin_license() {
		wp_enqueue_script( 'jquery' ); ?>
		<style>.ftg-license-master-form th {
				background: #f9f9f9;
				padding: 14px;
				border-bottom: 1px solid #ccc;
				margin: -14px -14px 20px;
				width: 100%;
				display: block
			}

			.ftg-license-master-form .form-table tr {
				float: left;
				margin: 0 15px 15px 0;
				background: #fff;
				border: 1px solid #ccc;
				width: 30.5%;
				max-width: 350px;
				padding: 14px;
				min-height: 220px;
				position: relative;
				box-sizing: border-box
			}

			.ftg-license-master-form .form-table td {
				padding: 0;
				display: block
			}

			.ftg-license-master-form td input.regular-text {
				margin: 0 0 8px;
				width: 100%
			}

			.ftg-license-master-form .edd-license-data[class*=edd-license-] {
				position: absolute;
				background: #fafafa;
				padding: 14px;
				border-top: 1px solid #eee;
				margin: 20px -14px -14px;
				min-height: 67px;
				width: 100%;
				bottom: 14px;
				box-sizing: border-box
			}

			.ftg-license-master-form .edd-license-data p {
				font-size: 13px;
				margin-top: 0
			}

			.ftg-license-master-form tr {
				display: none
			}

			.ftg-license-master-form tr.ftg-license-wrap {
				display: block
			}

			.ftg-license-master-form .edd-license-msg-error {
				background: rgba(255, 0, 0, 0.49)
			}

			.ftg-license-master-form tr.ftg-license-wrap {
				display: block
			}

			.ftg-license-master-form .edd-license-msg-error {
				background: #e24e4e !important;
				color: #FFF
			}

			.ftg-license-wrap .edd-license-data p {
				color: #1e981e
			}

			.edd-license-msg-error p {
				color: #FFF !important
			}

			.feed-them_page_fts-license-page .button-secondary {
				display: none;
			}
			.ftg-no-license-overlay {
				position: absolute;
				height: 100%;
				width: 100%;
				top: 0;
				left: 0;
				z-index: 100;
				background: rgba(255,255,255,.64);
				text-align: center;
				vertical-align: middle;
			}
			.ftg-no-license-overlay a {
				padding: 9px 15px;
				background: #0073aa;
				color: #FFF;
				text-decoration: none;
				border-radius: 3px;
				font-size: 14px;
				display: inline-block;
			}
			.ftg-no-license-button-wrap {
				position: absolute;
				top: 50%;
				left: 50%;
				-ms-transform: translate(-50%,-50%);
				transform: translate(-50%,-50%);
				min-width: 200px;
			}
		</style>
		<?php
	}

	/**
	 * FT Gallery Admin CSS
	 *
	 * Add CSS to the WordPress Admin (backend)
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_admin_css() {
		wp_register_style( 'ft_gallery_admin', plugins_url( 'feed-them-gallery/admin/css/admin.css' ), array(), FTG_CURRENT_VERSION );
		wp_enqueue_style( 'ft_gallery_admin' );
	}

	/**
	 * FT Gallery Reorder Admin Sub Menus
	 *
	 * Get Global Menu then Reorder FT Gallery's Admin Sub Menu
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function ft_gallery_reorder_admin_sub_menus() {
		global $submenu;

		// Unset Menu Items We don't want them to have.
		// unset($submenu['edit.php?post_type=ft_gallery'][5]);
		// unset($submenu['edit.php?post_type=ft_gallery'][10]);
		// unset($submenu['edit.php?post_type=ft_gallery'][15]);.
		return $submenu;
	}

	/**
	 * FT Gallery Register Settings
	 *
	 * Generic function for registering settings
	 *
	 * @param string $settings_name The setting name.
	 * @param array  $settings All the Settings to be returned.
	 * @since 1.0.0
	 */
	public function ft_gallery_register_settings( $settings_name, $settings ) {
		foreach ( $settings as $key => $setting ) {
			register_setting( $settings_name, $setting );
		}
	}

	/**
	 * FT Gallery Color Options Head CSS
	 *
	 * Set the color options in the header
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_color_options_head_css() {
		?>
		<style type="text/css"><?php echo esc_html( get_option( 'ft-gallery-color-options-main-wrapper-css-input' ) ); ?></style>
		<?php
	}

	/**
	 * FT Gallery Color Options CSS
	 *
	 * Set the font in the header
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_color_options_head_css_front() {

		$ft_gallery_text_color       = ftg_get_option( 'text_color' );
        $ft_gallery_text_size        = ftg_get_option( 'text_size' );
		$ft_gallery_decription_color = ftg_get_option( 'description_color' );
        $ft_gallery_description_size = ftg_get_option( 'description_size' );
		$ft_gallery_link_color       = ftg_get_option( 'link_color' );
		$ft_gallery_link_color_hover = ftg_get_option( 'link_color_hover' );
		$ft_gallery_post_time        = ftg_get_option( 'post_time' );
		?>

		<style type="text/css">
			<?php
			if ( ! empty( $ft_gallery_text_color ) || ! empty( $ft_gallery_text_size ) ) {

				?>
            strong.ftg-title-wrap {<?php if ( ! empty( $ft_gallery_text_color ) ) { ?>color: <?php echo esc_html( $ft_gallery_text_color );?> !important; <?php }?><?php if ( ! empty( $ft_gallery_text_size ) ) { ?>font-size: <?php echo esc_html( $ft_gallery_text_size );?> !important; <?php }?>}
				<?php
			}
			if ( ! empty( $ft_gallery_decription_color ) || ! empty( $ft_gallery_description_size ) ) {

				?>
            .ft-gallery-description-wrap, .ft-gallery-description-wrap p{<?php if ( ! empty( $ft_gallery_decription_color ) ) { ?>color: <?php echo esc_html( $ft_gallery_decription_color );?> !important; <?php }?><?php if ( ! empty( $ft_gallery_description_size ) ) { ?>font-size: <?php echo esc_html( $ft_gallery_description_size );?> !important; <?php }?>  }
            <?php
        }
        if ( ! empty( $ft_gallery_link_color ) ) {

            ?>
				.ft-gallery-link-popup a, .mfp-close, .ft-wp-gallery a, .ft-gallery-popup a, .ft-wp-gallery .fts-mashup-count-wrap .fts-share-wrap a, .ft-wp-gallery .fts-share-wrap a, body .ft-wp-gallery .ft-gallery-cta-button-wrap a{color: <?php echo esc_html( $ft_gallery_link_color ); ?> !important;}
				<?php
			}
			if ( ! empty( $ft_gallery_link_color_hover ) ) {

				?>
				.ft-gallery-link-popup a:hover, .mfp-close:hover, .ft-wp-gallery a:hover, .ft-gallery-popup a:hover, .ft-wp-gallery .fts-share-wrap a:hover, body .ft-wp-gallery .ft-gallery-cta-button-wrap a:hover{color: <?php echo esc_html( $ft_gallery_link_color_hover ); ?> !important;}
				<?php
			}
			if ( ! empty( $ft_gallery_post_time ) ) {
				?>
				.ft-gallery-post-time{color: <?php echo esc_html( $ft_gallery_post_time ); ?> !important;}
				<?php } ?>
		</style>
		<?php
	}

	/**
	 * FT Gallery Admin Bar Menu
	 *
	 * Create our custom menu in the WordPress admin bar.
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_admin_bar_menu() {
		global $wp_admin_bar;

		if ( ! is_super_admin() || ! is_admin_bar_showing() || ! ftg_get_option( 'show_admin_bar' ) ) {
			return;
		}
		$wp_admin_bar->add_menu(
			array(
				'id'    => 'ft_gallery_admin_bar',
				'title' => esc_html__( 'Feed Them Gallery', 'ft-gallery' ),
				'href'  => false,
			)
		);
		// Galleries.
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'ft_gallery_admin_bar_view_galleries',
				'parent' => 'ft_gallery_admin_bar',
				'title'  => esc_html__( 'Galleries ', 'ft-gallery' ),
				'href'   => admin_url( 'edit.php?post_type=ft_gallery' ),
			)
		);
		// Add Gallery.
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'ft_gallery_admin_bar_new_gallery',
				'parent' => 'ft_gallery_admin_bar',
				'title'  => esc_html__( 'Add Gallery ', 'ft-gallery' ),
				'href'   => admin_url( 'post-new.php?post_type=ft_gallery' ),
			)
		);
		// Settings.
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'ft_gallery_admin_bar_settings',
				'parent' => 'ft_gallery_admin_bar',
				'title'  => esc_html__( 'Settings', 'ft-gallery' ),
				'href'   => admin_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-settings-page' ),
			)
		);

		// System info.
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'ft_gallery_admin_bar_system_info',
				'parent' => 'ft_gallery_admin_bar',
				'title'  => esc_html__( 'System Info', 'ft-gallery' ),
				'href'   => admin_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-system-info-submenu-page' ),
			)
		);
		if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			// Plugin License.
			$wp_admin_bar->add_menu(
				array(
					'id'     => 'ft_gallery_admin_bar_plugin_license',
					'parent' => 'ft_gallery_admin_bar',
					'title'  => esc_html__( 'Plugin License', 'ft-gallery' ),
					'href'   => admin_url( 'edit.php?post_type=ft_gallery&page=ft-gallery-license-page' ),
				)
			);
		}
	}

	/**
	 * FT Gallery Settings Page Options
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_settings_page_options() {

		$settings = array(
			'ft-gallery-powered-text-options-settings',
			'ft_gallery_fix_magnific',
			// Color Options.
			'ft-gallery-admin-bar-menu',
			'ft-gallery-options-settings-custom-css-second',
			'ft_gallery_text_color',
			'ft_gallery_text_size',
			'ft_gallery_description_color',
            'ft_gallery_description_size',
			'ft_gallery_link_color',
			'ft_gallery_link_color_hover',
			'ft_gallery_post_time',
			'ft-gallery-main-wrapper-css-input',
			'ft-gallery-settings-admin-textarea-css',
			// Attachment Filename Renaming.
			'ft-gallery-use-attachment-naming',
			'ft_gallery_attch_name_gallery_name',
			'ft_gallery_attch_name_post_id',
			'ft_gallery_attch_name_date',
			'ft_gallery_attch_name_file_name',
			'ft_gallery_attch_name_attch_id',
			// Attachment Title Renaming.
			'ft_gallery_attch_title_gallery_name',
			'ft_gallery_attch_title_post_id',
			'ft_gallery_attch_title_date',
			'ft_gallery_attch_title_file_name',
			'ft_gallery_attch_title_attch_id',
			// Format Attachment Title Options.
			'ft_gallery_format_attachment_titles_options',
			// date options.
			'ft-gallery-date-and-time-format',
			'ft-gallery-timezone',
			'ft-gallery-custom-date',
			'ft-gallery-custom-time',
			'ft_gallery_language_second',
			'ft_gallery_language_seconds',
			'ft_gallery_language_minute',
			'ft_gallery_language_minutes',
			'ft_gallery_language_hour',
			'ft_gallery_language_hours',
			'ft_gallery_language_day',
			'ft_gallery_language_days',
			'ft_gallery_language_week',
			'ft_gallery_language_weeks',
			'ft_gallery_language_month',
			'ft_gallery_language_months',
			'ft_gallery_language_year',
			'ft_gallery_language_years',
			'ft_gallery_language_ago',
			'ft_gallery_language_ago',
			'ft_gallery_duplicate_post_show',
		);

		// If Woocommerce is active add options to save.
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) ) {
			// Woocommerce Options.
			$settings[] = 'ft_gallery_attch_prod_to_gallery_cat';
			$settings[] = 'ft_gallery_woo_add_to_cart';
			$settings[] = 'ft_gallery_enable_right_click';
		}

		// Add Custom Post Types to settings.
		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$output   = 'names'; // names or objects, note names is the default.
		$operator = 'and'; // 'and' or 'or'

		$post_types = get_post_types( $args, $output, $operator );

		foreach ( $post_types as $post_type ) {
			// Lowercase for setting name.
			$lower_post_type      = strtolower( $post_type );
			$final_post_type_name = 'ft-gallery-settings-pt-' . $lower_post_type;

			$settings[] = $final_post_type_name;
		}

		$this->ft_gallery_register_settings( 'ft-gallery-settings', $settings );
	}

	/**
	 * Feed Them Gallery Head CSS
	 *
	 * Add CSS to the WordPress front end Header
	 *
	 * @since 1.0.0
	 */
	public function ft_gallery_head_css() {
		?>
		<style type="text/css"><?php echo esc_html( ftg_get_option( 'custom_css' ) ); ?></style>
										  <?php
	}
}//end class