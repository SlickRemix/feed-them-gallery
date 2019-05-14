<?php
/**
 * Settings Page
 *
 * Class Feed Them Gallery Settings Page
 *
 * @class    Settings_Page
 * @version  1.0.0
 * @package  FeedThemSocial/Admin
 * @category Class
 * @author   SlickRemix
 */

namespace feed_them_gallery;

/**
 * Class Settings_Page
 */
class Settings_Page {


	public static function load() {
		$instance = new self();

		// Add Actions and Filters.
		$instance->add_actions_filters();
	}

	public function add_actions_filters() {
		if ( is_admin() ) {
			// Adds setting page to Feed Them Gallery menu.
			add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		}
	}
	/**
	 * Settings_Page constructor.
	 */
	public function __construct() {}

	/**
	 * FT Gallery Submenu Pages
	 *
	 * Admin Submenu buttons
	 *
	 * @since 1.0.0
	 */
	public function add_submenu_page() {
		// Settings Page.
		add_submenu_page(
			'edit.php?post_type=ft_gallery',
			__( 'Settings', 'ft-gallery' ),
			__( 'Settings', 'ft-gallery' ),
			'manage_options',
			'ft-gallery-settings-page',
			array( $this, 'Settings_Page' )
		);
	}


	/**
	 * Settings Page
	 *
	 * Feed Them Gallery Settings Page
	 *
	 * @since 1.0.0
	 */
	function Settings_Page() {
		// Feed Them Gallery Functions Class.
		?>
		<div class="ft-gallery-main-template-wrapper-all">


			<div class="ft-gallery-settings-admin-wrap" id="theme-settings-wrap">
				<h2><img src="<?php echo plugins_url( 'css/ft-gallery-logo.png', __FILE__ ); ?>" /></h2>
				<a class="buy-extensions-btn" href="https://www.slickremix.com/ft-gallery-documentation/" target="_blank"><?php _e( 'Setup Documentation', 'ft-gallery' ); ?></a>

				<div class="ft-gallery-settings-admin-input-wrap company-info-style ft-gallery-cache-wrap" style="padding-bottom: 0px;">
					<?php
					isset( $ssAdminBarMenu ) ? $ssAdminBarMenu : '';
					$ssAdminBarMenu = get_option( 'ft-gallery-admin-bar-menu' );
					?>
					<div class="clear"></div>
				</div>
				<!--/ft-gallery-settings-admin-input-wrap-->

				<form method="post" class="ft-gallery-settings-admin-form wp-core-ui" action="options.php">
					<?php
					// get our registered settings from the gq theme functions
					settings_fields( 'ft-gallery-settings' );
					?>

					<div class="ft-rename-options-wrap">
						<h4 style="margin-top: 10px;  border:none;padding: 0 0 20px 0;"><?php _e( 'Attachment File & Title Renaming [on upload]', 'feed-them-gallery' ); ?></h4>


						<?php _e( 'Use attachment renaming when importing/uploading attachments. This will overwrite original Filename.', 'ft-gallery' ); ?>
						<br />
						<strong><?php _e( 'Below are examples of what the attachment filenames and Titles will look like after uploading:', 'ft-gallery' ); ?></strong> <?php _e( '(Click "Save All Changes" to view Examples)', 'ft-gallery' ); ?>
						<br /><br />
						<input name="ft-gallery-use-attachment-naming" type="checkbox" id="ft-gallery-attachment-naming" value="1" <?php echo checked( '1', get_option( 'ft-gallery-use-attachment-naming' ) ); ?>/>
						<?php
						if ( get_option( 'ft-gallery-use-attachment-naming' ) == '1' ) {
							_e( '<strong>Checked:</strong> You are using Attachment File and Title Renaming when uploading each image.', 'ft-gallery' );

						} else {
							_e( '<strong>Not Checked:</strong> You are using the Original filename for Attachment names and Titles that is uploaded with each file.', 'ft-gallery' );
						}
						?>
						<br /><br />
						<div class="clear"></div>

						<div class="settings-sub-wrap">
							<h5><?php _e( 'Filename', 'ft-gallery' ); ?></h5>

							<label><input name="ft_gallery_attch_name_gallery_name" type="checkbox" value="1" <?php echo checked( '1', get_option( 'ft_gallery_attch_name_gallery_name' ) ); ?>/> <?php _e( 'Include Gallery Name', 'ft-gallery' ); ?>
								(Example: this-gallery-name)</label>

							<label><input name="ft_gallery_attch_name_post_id" type="checkbox" value="1" <?php echo checked( '1', get_option( 'ft_gallery_attch_name_post_id' ) ); ?>/> <?php _e( 'Include Gallery ID Number', 'ft-gallery' ); ?>
								(Example: 20311)</label>

							<label><input name="ft_gallery_attch_name_date" type="checkbox" value="1" <?php echo checked( '1', get_option( 'ft_gallery_attch_name_date' ) ); ?>/> <?php _e( 'Include Date', 'ft-gallery' ); ?>
								(Example: 08-11-17)</label>

                            <label><input name="ft_gallery_attch_name_file_name" type="checkbox" value="1" <?php echo checked( '1', get_option( 'ft_gallery_attch_name_file_name' ) ); ?>/> <?php _e( 'Include File Name', 'ft-gallery' ); ?>
                                (Example: my-image-name)</label>

                            <label><input name="ft_gallery_attch_name_attch_id" type="checkbox" value="1" <?php echo checked( '1', get_option( 'ft_gallery_attch_name_attch_id' ) ); ?>/> <?php _e( 'Include Attachment ID', 'ft-gallery' ); ?>
                                (Example: 1234)</label>

                            <div class="ft-gallery-attch-name-example">
                                <?php
                                $attch_name_output = '';
                                //Attachment Filename Gallery Name
                                if (get_option('ft_gallery_attch_name_gallery_name') == '1') {
                                    $attch_name_output .= '<span class="ft_gallery_attch_name_gallery_name">this-gallery-name-</span>';
                                }
                                //Attachment Filename Gallery ID
                                if (get_option('ft_gallery_attch_name_post_id') == '1') {
                                    $attch_name_output .= '<span class="ft_gallery_attch_name_post_id">20311-</span>';
                                }
                                //Attachment Filename Date
                                if (get_option('ft_gallery_attch_name_date') == '1') {
                                    $attch_name_output .= '<span class="ft_gallery_attch_name_date">08-11-17-</span>';
                                }
                                //Attachment Filename Date
                                if (get_option('ft_gallery_attch_name_file_name') == '1') {
                                    $attch_name_output .= '<span class="ft_gallery_attch_name_file_name">my-image-name-</span>';
                                }
                                //Attachment Filename Date
                                if (get_option('ft_gallery_attch_name_attch_id') == '1') {
                                    $attch_name_output .= '<span class="ft_gallery_attch_name_attch_id">1234</span>';
                                }
                                //Output Filename Example
                                echo '<div class="clear"></div><div class="ftg-filename-renaming-example"><strong><em>Example Filename:</em></strong> ' . $attch_name_output . '.jpg</div>';
                                ?>
                            </div>
                        </div>

                        <div class="settings-sub-wrap">
                            <h5><?php _e('Title', 'ft-gallery'); ?></h5>

                            <label><input name="ft_gallery_attch_title_gallery_name" type="checkbox" value="1" <?php echo checked('1', get_option('ft_gallery_attch_title_gallery_name')); ?>/> <?php _e('Include Gallery Name', 'ft-gallery'); ?>
                                (Example: This Gallery Name)</label>

                            <label><input name="ft_gallery_attch_title_post_id" type="checkbox" value="1" <?php echo checked('1', get_option('ft_gallery_attch_title_post_id')); ?>/> <?php _e('Include Gallery ID Number', 'ft-gallery'); ?>
                                (Example: 20311)</label>

                            <label><input name="ft_gallery_attch_title_date" type="checkbox" value="1" <?php echo checked('1', get_option('ft_gallery_attch_title_date')); ?>/> <?php _e('Include Date', 'ft-gallery'); ?>
                                (Example: 08-11-17)</label>

                            <label><input name="ft_gallery_attch_title_file_name" type="checkbox" value="1" <?php echo checked('1', get_option('ft_gallery_attch_title_file_name')); ?>/> <?php _e('Include File Name', 'ft-gallery'); ?>
                                (Example: my-image-name)</label>

                            <label><input name="ft_gallery_attch_title_attch_id" type="checkbox" value="1" <?php echo checked( '1', get_option( 'ft_gallery_attch_title_attch_id' ) ); ?>/> <?php _e( 'Include Attachment ID', 'ft-gallery' ); ?>
                                (Example: 1234)</label>

                            <div class="clear"></div>

                            <div class="ft-gallery-attch-name-example">
                                <?php
                                $attch_title_output = '';
                                //Attachment Title Gallery Name
                                if (get_option('ft_gallery_attch_title_gallery_name') == '1') {
                                    $attch_title_output .= '<span class="ft_gallery_attch_title_gallery_name">This Gallery Name </span>';
                                }
                                //Attachment Title Gallery ID
                                if (get_option('ft_gallery_attch_title_post_id') == '1') {
                                    $attch_title_output .= '<span class="ft_gallery_attch_title_post_id">20311-</span>';
                                }
                                //Attachment Title Date
                                if (get_option('ft_gallery_attch_title_date') == '1') {
                                    $attch_title_output .= '<span class="ft_gallery_attch_title_date">08-11-17-</span>';
                                }
                                //Attachment Title File Name
                                if (get_option('ft_gallery_attch_title_file_name') == '1') {
                                    $attch_title_output .= '<span class="ft_gallery_attch_title_file_name">my-file-name-</span>';
                                }
                                //Attachment Filename Date
                                if (get_option('ft_gallery_attch_title_attch_id') == '1') {
                                    $attch_title_output .= '<span class="ft_gallery_attch_title_attch_id">1234</span>';
                                }

                                //Output Filename Example
                                echo '<div class="clear"></div><div class="ftg-title-renaming-example"><strong><em>Example Title:</em></strong> ' . $attch_title_output . '</div>';
                                ?>
                            </div>

						</div>

						<div class="clear"></div>
						<h4><?php _e( 'Format Attachment Titles', 'feed-them-gallery' ); ?></h4>

						<?php $options = get_option( 'ft_gallery_format_attachment_titles_options' ); ?>

						<div class="settings-sub-wrap">
							<h5><?php _e( 'Remove Characters', 'ft-gallery' ); ?></h5>
							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_hyphen]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_hyphen'] ) ) {
									checked( '1', $options['ft_gallery_fat_hyphen'] );
							}
							?>
								> <?php _e( 'Hyphen', 'ft-gallery' ); ?> (-)</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_underscore]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_underscore'] ) ) {
									checked( '1', $options['ft_gallery_fat_underscore'] );
							}
							?>
								> <?php _e( 'Underscore', 'ft-gallery' ); ?> (_)</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_period]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_period'] ) ) {
									checked( '1', $options['ft_gallery_fat_period'] );
							}
							?>
								> <?php _e( 'Period', 'ft-gallery' ); ?> (.)</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_tilde]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_title'] ) ) {
									checked( '1', $options['ft_gallery_fat_title'] );
							}
							?>
								> <?php _e( 'Tilde', 'ft-gallery' ); ?> (~)</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_plus]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_plus'] ) ) {
									checked( '1', $options['ft_gallery_fat_plus'] );
							}
							?>
								> <?php _e( 'Plus', 'ft-gallery' ); ?> (+)</label>

							<div class="clear"></div>
							<div class="description"><?php _e( 'This is only for the image title the image file will still contain a hyphen - in the file name.', 'ft-gallery' ); ?></div>

						</div>


						<div class="settings-sub-wrap">
							<h5><?php _e( 'Capitalization Method', 'ft-gallery' ); ?></h5>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_cap_options]" type="radio" value="cap_all" 
							<?php
							if ( isset( $options['ft_gallery_cap_options'] ) ) {
								checked( 'cap_all', $options['ft_gallery_cap_options'] );}
							?>
							> <?php _e( 'Capitalize All Words', 'ft-gallery' ); ?>
							</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_cap_options]" type="radio" value="cap_first" 
							<?php
							if ( isset( $options['ft_gallery_cap_options'] ) ) {
								checked( 'cap_first', $options['ft_gallery_cap_options'] );}
							?>
							> <?php _e( 'Capitalize First Word Only', 'ft-gallery' ); ?>
							</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_cap_options]" type="radio" value="all_lower" 
							<?php
							if ( isset( $options['ft_gallery_cap_options'] ) ) {
								checked( 'all_lower', $options['ft_gallery_cap_options'] );}
							?>
							> <?php _e( 'All Words Lower Case', 'ft-gallery' ); ?>
							</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_cap_options]" type="radio" value="all_upper" 
							<?php
							if ( isset( $options['ft_gallery_cap_options'] ) ) {
								checked( 'all_upper', $options['ft_gallery_cap_options'] );}
							?>
							> <?php _e( 'All Words Upper Case', 'ft-gallery' ); ?>
							</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_cap_options]" type="radio" value="dont_alter" 
							<?php
							if ( isset( $options['ft_gallery_cap_options'] ) ) {
								checked( 'dont_alter', $options['ft_gallery_cap_options'] );}
							?>
							> <?php _e( 'Don\'t Alter (title text isn\'t modified in any way)', 'ft-gallery' ); ?>
							</label>
							<div class="clear"></div>
							<div class="description"><?php _e( 'Capitalization works on individual words separated by spaces. If the title contains NO spaces after formatting then only the first letter will be capitalized.', 'ft-gallery' ); ?></div>
						</div>
						<div class="settings-sub-wrap">

							<div class="clear"></div>

							<h5><?php _e( 'Misc. Options', 'ft-gallery' ); ?></h5>
							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_alt]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_alt'] ) ) {
									checked( '1', $options['ft_gallery_fat_alt'] );
							}
							?>
								> <?php _e( 'Add Title to \'Alternative Text\' Field?', 'ft-gallery' ); ?>
							</label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_caption]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_caption'] ) ) {
									checked( '1', $options['ft_gallery_fat_caption'] );
							}
							?>
								> <?php _e( 'Add Title to \'Caption\' Field?', 'ft-gallery' ); ?></label>

							<label><input name="ft_gallery_format_attachment_titles_options[ft_gallery_fat_description]" type="checkbox" value="1" 
							<?php
							if ( isset( $options['ft_gallery_fat_description'] ) ) {
									checked( '1', $options['ft_gallery_fat_description'] );
							}
							?>
								> <?php _e( 'Add Title to \'Description\' Field?', 'ft-gallery' ); ?></label>
							<div class="clear"></div>
						</div>

						<div class="clear"></div>
						<div class="settings-example-block" style="margin-top: 25px;">
							<strong>Below is an example of what the attachment Titles will look like after
								uploading:</strong> (Click "Save All Changes" to view Example)<br />
							<em>
								<small>NOTE: Title will come from Filename of uploaded attachment. You may still set
									a custom name for each photo after uploaded.
								</small>
							</em>
						</div>

						<div class="ft-gallery-attch-name-example">
							<?php
							$gallery_class = new Gallery();
							// Output Title Example
							echo '<div class="ftg-filename-renaming-example"><strong><em>Example Title:</em></strong> ' . $gallery_class->ft_gallery_format_attachment_title( 'Gallery Image Title' ) . '</div>';
							?>
						</div>
					</div>
					<div class="clear"></div>

					<h4><?php _e( 'Custom CSS Option', 'feed-them-gallery' ); ?></h4>
					<p class="special">
						<input name="ft-gallery-options-settings-custom-css-second" type="checkbox" id="ft-gallery-options-settings-custom-css-second" value="1" <?php echo checked( '1', get_option( 'ft-gallery-options-settings-custom-css-second' ) ); ?>/>
						<?php
						if ( get_option( 'ft-gallery-options-settings-custom-css-second' ) == '1' ) {
							_e( '<strong>Checked:</strong> Custom CSS option is being used now.', 'ft-gallery' );
						} else {
							_e( '<strong>Not Checked:</strong> You are using the default CSS.', 'ft-gallery' );
						}
						?>
					</p>

					<label class="toggle-custom-textarea-show button"><span><?php _e( 'Show', 'ft-gallery' ); ?></span><span class="toggle-custom-textarea-hide"><?php _e( 'Hide', 'ft-gallery' ); ?></span> <?php _e( 'custom CSS', 'ft-gallery' ); ?>
					</label>
					<div class="ft-gallery-custom-css-text"><?php _e( '<p>Add Your Custom CSS Code below.</p>', 'ft-gallery' ); ?></div>
					<textarea name="ft-gallery-settings-admin-textarea-css" class="ft-gallery-settings-admin-textarea-css" id="ft-gallery-main-wrapper-css-input"><?php echo get_option( 'ft-gallery-settings-admin-textarea-css' ); ?></textarea>


					<h4><?php _e( 'Gallery Color Options', 'feed-them-gallery' ); ?></h4>

					<p><label><?php _e( 'Text Color', 'feed-them-gallery' ); ?></label>
						<input type="text" name="ft_gallery_text_color" class="feed-them-social-admin-input fb-text-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:'#eee',pickerFace:3,pickerBorder:0,pickerInsetColor:'white'}" id="fb-text-color-input" placeholder="#222" value="<?php echo get_option( 'ft_gallery_text_color' ); ?>" />
					</p>

					<p><label><?php _e( 'Link Color', 'feed-them-gallery' ); ?></label>
						<input type="text" name="ft_gallery_link_color" class="feed-them-social-admin-input fb-link-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:'#eee',pickerFace:3,pickerBorder:0,pickerInsetColor:'white'}" id="fb-link-color-input" placeholder="#222" value="<?php echo get_option( 'ft_gallery_link_color' ); ?>" />
					</p>

					<p>
						<label><?php _e( 'Link Color Hover', 'feed-them-gallery' ); ?></label>
						<input type="text" name="ft_gallery_link_color_hover" class="feed-them-social-admin-input fb-link-color-hover-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:'#eee',pickerFace:3,pickerBorder:0,pickerInsetColor:'white'}" id="fb-link-color-hover-input" placeholder="#ddd" value="<?php echo get_option( 'ft_gallery_link_color_hover' ); ?>" />
					</p>
					<p>
						<label><?php _e( 'Date Color', 'feed-them-gallery' ); ?></label>
						<input type="text" name="ft_gallery_post_time" class="feed-them-social-admin-input fb-date-color-input color {hash:true,caps:false,required:false,adjust:false,pickerFaceColor:'#eee',pickerFace:3,pickerBorder:0,pickerInsetColor:'white'}" id="ft-gallery-post-time" placeholder="#ddd" value="<?php echo get_option( 'ft_gallery_post_time' ); ?>" />
					</p>



					<div class="clear"></div>

					<div class="ft-gallery-date-settings-options-wrap">
						<h4><?php _e( 'Date Options for Images', 'feed-them-gallery' ); ?></h4>
						<?php

						isset( $ftsDateTimeFormat ) ? $ftsDateTimeFormat : '';
						isset( $ftsTimezone ) ? $ftsTimezone : '';
						isset( $ftsCustomDate ) ? $ftsCustomDate : '';
						isset( $ftsCustomTime ) ? $ftsCustomTime : '';
						$ftsDateTimeFormat = get_option( 'ft-gallery-date-and-time-format' );
						$ftsTimezone       = get_option( 'ft-gallery-timezone' );
						$ftsCustomDate     = get_option( 'ft-gallery-date_format' );
						$ftsCustomTime     = get_option( 'ft-gallery-time-format' );
						$ftsCustomTimezone = get_option( 'ft-gallery-timezone' ) ? get_option( 'ft-gallery-timezone' ) : 'America/Los_Angeles';
						date_default_timezone_set( $ftsCustomTimezone );

						?>
						<div style="float:left; max-width:400px; margin-right:30px;">
							<h5><?php _e( 'Image Date Format', 'feed-them-gallery' ); ?></h5>

							<fieldset>
								<select id="ft-gallery-date-and-time-format" name="ft-gallery-date-and-time-format">
									<option value="l, F jS, Y \a\t g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'l, F jS, Y \a\t g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'l, F jS, Y \a\t g:ia' ); ?></option>
									<option value="F j, Y \a\t g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'F j, Y \a\t g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'F j, Y \a\t g:ia' ); ?></option>
									<option value="F j, Y g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'F j, Y g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'F j, Y g:ia' ); ?></option>
									<option value="F, Y \a\t g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'F, Y \a\t g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'F, Y \a\t g:ia' ); ?></option>
									<option value="M j, Y @ g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'M j, Y @ g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'M j, Y @ g:ia' ); ?></option>
									<option value="M j, Y @ G:i" 
									<?php
									if ( $ftsDateTimeFormat == 'M j, Y @ G:i' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'M j, Y @ G:i' ); ?></option>
									<option value="m/d/Y \a\t g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'm/d/Y \a\t g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'm/d/Y \a\t g:ia' ); ?></option>
									<option value="m/d/Y @ G:i" 
									<?php
									if ( $ftsDateTimeFormat == 'm/d/Y @ G:i' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'm/d/Y @ G:i' ); ?></option>
									<option value="d/m/Y \a\t g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'd/m/Y \a\t g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'd/m/Y \a\t g:ia' ); ?></option>
									<option value="d/m/Y @ G:i" 
									<?php
									if ( $ftsDateTimeFormat == 'd/m/Y @ G:i' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'd/m/Y @ G:i' ); ?></option>
									<option value="Y/m/d \a\t g:ia" 
									<?php
									if ( $ftsDateTimeFormat == 'Y/m/d \a\t g:ia' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'Y/m/d \a\t g:ia' ); ?></option>
									<option value="Y/m/d @ G:i" 
									<?php
									if ( $ftsDateTimeFormat == 'Y/m/d @ G:i' ) {
										echo 'selected="selected"';}
									?>
									><?php echo date( 'Y/m/d @ G:i' ); ?></option>
									<option value="one-day-ago" 
									<?php
									if ( $ftsDateTimeFormat == 'one-day-ago' ) {
										echo 'selected="selected"';}
									?>
									><?php _e( '1 day ago', 'feed-them-gallery' ); ?></option>
									<option value="fts-custom-date" 
									<?php
									if ( $ftsDateTimeFormat == 'fts-custom-date' ) {
										echo 'selected="selected"';}
									?>
									><?php _e( 'Use Custom Date and Time Option Below', 'feed-them-gallery' ); ?></option>
								</select>
							</fieldset>

							<?php
							// Date translate
							$fts_language_second  = get_option( 'ft_gallery_language_second', 'second' );
							$fts_language_seconds = get_option( 'ft_gallery_language_seconds', 'seconds' );
							$fts_language_minute  = get_option( 'ft_gallery_language_minute', 'minute' );
							$fts_language_minutes = get_option( 'ft_gallery_language_minutes', 'minutes' );
							$fts_language_hour    = get_option( 'ft_gallery_language_hour', 'hour' );
							$fts_language_hours   = get_option( 'ft_gallery_language_hours', 'hours' );
							$fts_language_day     = get_option( 'ft_gallery_language_day', 'day' );
							$fts_language_days    = get_option( 'ft_gallery_language_days', 'days' );
							$fts_language_week    = get_option( 'ft_gallery_language_week', 'week' );
							$fts_language_weeks   = get_option( 'ft_gallery_language_weeks', 'weeks' );
							$fts_language_month   = get_option( 'ft_gallery_language_month', 'month' );
							$fts_language_months  = get_option( 'ft_gallery_language_months', 'months' );
							$fts_language_year    = get_option( 'ft_gallery_language_year', 'year' );
							$fts_language_years   = get_option( 'ft_gallery_language_years', 'years' );
							$fts_language_ago     = get_option( 'ft_gallery_language_ago', 'ago' );
							?>

							<div class="custom_time_ago_wrap" style="display:none;">
								<h5><?php _e( 'Translate words for 1 day ago option.', 'feed-them-gallery' ); ?></h5>
								<label for="ft_gallery_language_second"><?php _e( 'second' ); ?></label>
								<input name="ft_gallery_language_second" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_second ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_seconds"><?php _e( 'seconds' ); ?></label>
								<input name="ft_gallery_language_seconds" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_seconds ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_minute"><?php _e( 'minute' ); ?></label>
								<input name="ft_gallery_language_minute" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_minute ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_minutes"><?php _e( 'minutes' ); ?></label>
								<input name="ft_gallery_language_minutes" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_minutes ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_hour"><?php _e( 'hour' ); ?></label>
								<input name="ft_gallery_language_hour" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_hour ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_hours"><?php _e( 'hours' ); ?></label>
								<input name="ft_gallery_language_hours" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_hours ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_day"><?php _e( 'day' ); ?></label>
								<input name="ft_gallery_language_day" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_day ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_days"><?php _e( 'days' ); ?></label>
								<input name="ft_gallery_language_days" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_days ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_week"><?php _e( 'week' ); ?></label>
								<input name="ft_gallery_language_week" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_week ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_weeks"><?php _e( 'weeks' ); ?></label>
								<input name="ft_gallery_language_weeks" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_weeks ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_month"><?php _e( 'month' ); ?></label>
								<input name="ft_gallery_language_month" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_month ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_months"><?php _e( 'months' ); ?></label>
								<input name="ft_gallery_language_months" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_months ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_year"><?php _e( 'year' ); ?></label>
								<input name="ft_gallery_language_year" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_year ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_years"><?php _e( 'years' ); ?></label>
								<input name="ft_gallery_language_years" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_years ) ); ?>" size="25" />
								<br />
								<label for="ft_gallery_language_ago"><?php _e( 'ago' ); ?></label>
								<input name="ft_gallery_language_ago" type="text" value="<?php echo stripslashes( esc_attr( $fts_language_ago ) ); ?>" size="25" />

							</div>
							<script>
								// change the feed type 'how to' message when a feed type is selected

								<?php if ( $ftsDateTimeFormat == 'one-day-ago' ) { ?>
								jQuery('.custom_time_ago_wrap').show();
								<?php } ?>
								jQuery('#ft-gallery-date-and-time-format').change(function () {

									var ftsTimeAgo = jQuery("select#ft-gallery-date-and-time-format").val();
									if (ftsTimeAgo == 'one-day-ago') {
										jQuery('.custom_time_ago_wrap').show();
									}
									else {
										jQuery('.custom_time_ago_wrap').hide();
									}

								});

							</script>
							<h5 style="border-top:0px; margin-bottom:4px !important;"><?php _e( 'Custom Date and Time', 'feed-them-gallery' ); ?></h5>
							<div>
							<?php
							if ( $ftsCustomDate !== '' || $ftsCustomTime !== '' ) {
									echo date( get_option( 'ft-gallery-custom-date' ) . ' ' . get_option( 'ft-gallery-custom-time' ) );
							}
							?>
								</div>
							<p style="margin:12px 0 !important;">
								<input name="ft-gallery-custom-date" style="max-width:105px;" class="fts-color-settings-admin-input" id="ft-gallery-custom-date" placeholder="<?php _e( 'Date', 'feed-them-gallery' ); ?>" value="<?php echo get_option( 'ft-gallery-custom-date' ); ?>" />
								<input name="ft-gallery-custom-time" style="max-width:75px;" class="fts-color-settings-admin-input" id="ft-gallery-custom-time" placeholder="<?php _e( 'Time', 'feed-them-gallery' ); ?>" value="<?php echo get_option( 'ft-gallery-custom-time' ); ?>" />
							</p>
							<div><?php _e( 'This will override the date and time format above.', 'feed-them-gallery' ); ?>
								<br /><a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php _e( 'Options for custom date and time formatting.', 'feed-them-gallery' ); ?></a>
							</div>
						</div>
						<div style="float:left; max-width:330px; margin-right: 30px;">
							<h5><?php _e( 'TimeZone', 'feed-them-gallery' ); ?></h5>
							<fieldset>
								<select id="ft-gallery-timezone" name="ft-gallery-timezone">
									<option value="Pacific/Midway" 
									<?php
									if ( $ftsTimezone == 'Pacific/Midway' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-11:00) Midway Island, Samoa', 'feed-them-gallery' ); ?></option>

									<option value="America/Adak" 
									<?php
									if ( $ftsTimezone == 'America/Adak' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-10:00) Hawaii-Aleutian', 'feed-them-gallery' ); ?></option>

									<option value="Etc/GMT+10" 
									<?php
									if ( $ftsTimezone == 'Etc/GMT+10' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-10:00) Hawaii', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Marquesas" 
									<?php
									if ( $ftsTimezone == 'Pacific/Marquesas' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-09:30) Marquesas Islands', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Gambier" 
									<?php
									if ( $ftsTimezone == 'Pacific/Gambier' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-09:00) Gambier Islands', 'feed-them-gallery' ); ?></option>

									<option value="America/Anchorage" 
									<?php
									if ( $ftsTimezone == 'America/Anchorage' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-09:00) Alaska', 'feed-them-gallery' ); ?></option>

									<option value="America/Ensenada" 
									<?php
									if ( $ftsTimezone == 'America/Ensenada' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-08:00) Tijuana, Baja California', 'feed-them-gallery' ); ?></option>

									<option value="Etc/GMT+8" 
									<?php
									if ( $ftsTimezone == 'Etc/GMT+8' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-08:00) Pitcairn Islands', 'feed-them-gallery' ); ?></option>

									<option value="America/Los_Angeles" 
									<?php
									if ( $ftsTimezone == 'America/Los_Angeles' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-08:00) Pacific Time (US & Canada)', 'feed-them-gallery' ); ?></option>

									<option value="America/Denver" 
									<?php
									if ( $ftsTimezone == 'America/Denver' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-07:00) Mountain Time (US & Canada)', 'feed-them-gallery' ); ?></option>

									<option value="America/Chihuahua" 
									<?php
									if ( $ftsTimezone == 'America/Chihuahua' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-07:00) Chihuahua, La Paz, Mazatlan', 'feed-them-gallery' ); ?></option>

									<option value="America/Dawson_Creek" 
									<?php
									if ( $ftsTimezone == 'America/Dawson_Creek' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-07:00) Arizona', 'feed-them-gallery' ); ?></option>

									<option value="America/Belize" 
									<?php
									if ( $ftsTimezone == 'America/Belize' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-06:00) Saskatchewan, Central America', 'feed-them-gallery' ); ?></option>

									<option value="America/Cancun" 
									<?php
									if ( $ftsTimezone == 'America/Cancun' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-06:00) Guadalajara, Mexico City, Monterrey', 'feed-them-gallery' ); ?></option>

									<option value="Chile/EasterIsland" 
									<?php
									if ( $ftsTimezone == 'Chile/EasterIsland' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-06:00) Easter Island', 'feed-them-gallery' ); ?></option>

									<option value="America/Chicago" 
									<?php
									if ( $ftsTimezone == 'America/Chicago' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-06:00) Central Time (US & Canada)', 'feed-them-gallery' ); ?></option>

									<option value="America/New_York" 
									<?php
									if ( $ftsTimezone == 'America/New_York' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-05:00) Eastern Time (US & Canada)', 'feed-them-gallery' ); ?></option>

									<option value="America/Havana" 
									<?php
									if ( $ftsTimezone == 'America/Havana' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-05:00) Cuba', 'feed-them-gallery' ); ?></option>

									<option value="America/Bogota" 
									<?php
									if ( $ftsTimezone == 'America/Bogota' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-05:00) Bogota, Lima, Quito, Rio Branco', 'feed-them-gallery' ); ?></option>

									<option value="America/Caracas" 
									<?php
									if ( $ftsTimezone == 'America/Caracas' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:30) Caracas', 'feed-them-gallery' ); ?></option>

									<option value="America/Santiago" 
									<?php
									if ( $ftsTimezone == 'America/Santiago' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:00) Santiago', 'feed-them-gallery' ); ?></option>

									<option value="America/La_Paz" 
									<?php
									if ( $ftsTimezone == 'America/La_Paz' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:00) La Paz', 'feed-them-gallery' ); ?></option>

									<option value="Atlantic/Stanley" 
									<?php
									if ( $ftsTimezone == 'Atlantic/Stanley' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:00) Faukland Islands', 'feed-them-gallery' ); ?></option>

									<option value="America/Campo_Grande" 
									<?php
									if ( $ftsTimezone == 'America/Campo_Grande' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:00) Brazil', 'feed-them-gallery' ); ?></option>

									<option value="America/Goose_Bay" 
									<?php
									if ( $ftsTimezone == 'America/Goose_Bay' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:00) Atlantic Time (Goose Bay)', 'feed-them-gallery' ); ?></option>

									<option value="America/Glace_Bay" 
									<?php
									if ( $ftsTimezone == 'America/Glace_Bay' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-04:00) Atlantic Time (Canada)', 'feed-them-gallery' ); ?></option>

									<option value="America/St_Johns" 
									<?php
									if ( $ftsTimezone == 'America/St_Johns' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:30) Newfoundland', 'feed-them-gallery' ); ?></option>

									<option value="America/Araguaina" 
									<?php
									if ( $ftsTimezone == 'America/Araguaina' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:00) UTC-3', 'feed-them-gallery' ); ?></option>

									<option value="America/Montevideo" 
									<?php
									if ( $ftsTimezone == 'America/Montevideo' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:00) Montevideo', 'feed-them-gallery' ); ?></option>

									<option value="America/Miquelon" 
									<?php
									if ( $ftsTimezone == 'America/Miquelon' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:00) Miquelon, St. Pierre', 'feed-them-gallery' ); ?></option>

									<option value="America/Godthab" 
									<?php
									if ( $ftsTimezone == 'America/Godthab' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:00) Greenland', 'feed-them-gallery' ); ?></option>

									<option value="America/Argentina/Buenos_Aires" 
									<?php
									if ( $ftsTimezone == 'America/Argentina/Buenos_Aires' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:00) Buenos Aires', 'feed-them-gallery' ); ?></option>

									<option value="America/Sao_Paulo" 
									<?php
									if ( $ftsTimezone == 'America/Sao_Paulo' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-03:00) Brasilia', 'feed-them-gallery' ); ?></option>

									<option value="America/Noronha" 
									<?php
									if ( $ftsTimezone == 'America/Noronha' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-02:00) Mid-Atlantic', 'feed-them-gallery' ); ?></option>

									<option value="Atlantic/Cape_Verde" 
									<?php
									if ( $ftsTimezone == 'Atlantic/Cape_Verde' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-01:00) Cape Verde Is.', 'feed-them-gallery' ); ?></option>

									<option value="Atlantic/Azores" 
									<?php
									if ( $ftsTimezone == 'Atlantic/Azores' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT-01:00) Azores', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Belfast" 
									<?php
									if ( $ftsTimezone == 'Europe/Belfast' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT) Greenwich Mean Time : Belfast', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Dublin" 
									<?php
									if ( $ftsTimezone == 'Europe/Dublin' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT) Greenwich Mean Time : Dublin', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Lisbon" 
									<?php
									if ( $ftsTimezone == 'Europe/Lisbon' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT) Greenwich Mean Time : Lisbon', 'feed-them-gallery' ); ?></option>

									<option value="Europe/London" 
									<?php
									if ( $ftsTimezone == 'Europe/London' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT) Greenwich Mean Time : London', 'feed-them-gallery' ); ?></option>

									<option value="Africa/Abidjan" 
									<?php
									if ( $ftsTimezone == 'Africa/Abidjan' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT) Monrovia, Reykjavik', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Amsterdam" 
									<?php
									if ( $ftsTimezone == 'Europe/Amsterdam' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Belgrade" 
									<?php
									if ( $ftsTimezone == 'Europe/Belgrade' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Brussels" 
									<?php
									if ( $ftsTimezone == 'Europe/Brussels' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris', 'feed-them-gallery' ); ?></option>

									<option value="Africa/Algiers" 
									<?php
									if ( $ftsTimezone == 'Africa/Algiers' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+01:00) West Central Africa', 'feed-them-gallery' ); ?></option>

									<option value="Africa/Windhoek" 
									<?php
									if ( $ftsTimezone == 'Africa/Windhoek' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+01:00) Windhoek', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Beirut" 
									<?php
									if ( $ftsTimezone == 'Asia/Beirut' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Beirut', 'feed-them-gallery' ); ?></option>

									<option value="Africa/Cairo" 
									<?php
									if ( $ftsTimezone == 'Africa/Cairo' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Cairo', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Gaza" 
									<?php
									if ( $ftsTimezone == 'Asia/Gaza' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Gaza', 'feed-them-gallery' ); ?></option>

									<option value="Africa/Blantyre" 
									<?php
									if ( $ftsTimezone == 'Africa/Blantyre' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Harare, Pretoria', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Jerusalem" 
									<?php
									if ( $ftsTimezone == 'Asia/Jerusalem' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Jerusalem', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Minsk" 
									<?php
									if ( $ftsTimezone == 'Europe/Minsk' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Minsk', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Damascus" 
									<?php
									if ( $ftsTimezone == 'Asia/Damascus' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+02:00) Syria', 'feed-them-gallery' ); ?></option>

									<option value="Europe/Moscow" 
									<?php
									if ( $ftsTimezone == 'Europe/Moscow' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+03:00) Moscow, St. Petersburg, Volgograd', 'feed-them-gallery' ); ?></option>

									<option value="Africa/Addis_Ababa" 
									<?php
									if ( $ftsTimezone == 'Africa/Addis_Ababa' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+03:00) Nairobi', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Tehran" 
									<?php
									if ( $ftsTimezone == 'Asia/Tehran' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+03:30) Tehran', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Dubai" 
									<?php
									if ( $ftsTimezone == 'Asia/Dubai' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+04:00) Abu Dhabi, Muscat', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Yerevan" 
									<?php
									if ( $ftsTimezone == 'Asia/Yerevan' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+04:00) Yerevan', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Kabul" 
									<?php
									if ( $ftsTimezone == 'Asia/Kabul' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+04:30) Kabul', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Yekaterinburg" 
									<?php
									if ( $ftsTimezone == 'Asia/Yekaterinburg' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+05:00) Ekaterinburg', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Tashkent" 
									<?php
									if ( $ftsTimezone == 'Asia/Tashkent' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+05:00) Tashkent', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Kolkata" 
									<?php
									if ( $ftsTimezone == 'Asia/Kolkata' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Katmandu" 
									<?php
									if ( $ftsTimezone == 'Asia/Katmandu' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+05:45) Kathmandu', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Dhaka" 
									<?php
									if ( $ftsTimezone == 'Asia/Dhaka' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+06:00) Astana, Dhaka', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Novosibirsk" 
									<?php
									if ( $ftsTimezone == 'Asia/Novosibirsk' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+06:00) Novosibirsk', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Rangoon" 
									<?php
									if ( $ftsTimezone == 'Asia/Rangoon' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+06:30) Yangon (Rangoon)', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Bangkok" 
									<?php
									if ( $ftsTimezone == 'Asia/Bangkok' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+07:00) Bangkok, Hanoi, Jakarta', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Krasnoyarsk" 
									<?php
									if ( $ftsTimezone == 'Asia/Krasnoyarsk' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+07:00) Krasnoyarsk', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Hong_Kong" 
									<?php
									if ( $ftsTimezone == 'Asia/Hong_Kong' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Irkutsk" 
									<?php
									if ( $ftsTimezone == 'Asia/Irkutsk' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+08:00) Irkutsk, Ulaan Bataar', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Perth" 
									<?php
									if ( $ftsTimezone == 'Australia/Perth' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+08:00) Perth', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Eucla" 
									<?php
									if ( $ftsTimezone == 'Australia/Eucla' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+08:45) Eucla', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Tokyo" 
									<?php
									if ( $ftsTimezone == 'Asia/Tokyo' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+09:00) Osaka, Sapporo, Tokyo', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Seoul" 
									<?php
									if ( $ftsTimezone == 'Asia/Seoul' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+09:00) Seoul', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Yakutsk" 
									<?php
									if ( $ftsTimezone == 'Asia/Yakutsk' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+09:00) Yakutsk', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Adelaide" 
									<?php
									if ( $ftsTimezone == 'Australia/Adelaide' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+09:30) Adelaide', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Darwin" 
									<?php
									if ( $ftsTimezone == 'Australia/Darwin' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+09:30) Darwin', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Brisbane" 
									<?php
									if ( $ftsTimezone == 'Australia/Brisbane' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+10:00) Brisbane', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Hobart" 
									<?php
									if ( $ftsTimezone == 'Australia/Hobart' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+10:00) Sydney', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Vladivostok" 
									<?php
									if ( $ftsTimezone == 'Asia/Vladivostok' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+10:00) Vladivostok', 'feed-them-gallery' ); ?></option>

									<option value="Australia/Lord_Howe" 
									<?php
									if ( $ftsTimezone == 'Australia/Lord_Howe' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+10:30) Lord Howe Island', 'feed-them-gallery' ); ?></option>

									<option value="Etc/GMT-11" 
									<?php
									if ( $ftsTimezone == 'Etc/GMT-11' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+11:00) Solomon Is., New Caledonia', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Magadan" 
									<?php
									if ( $ftsTimezone == 'Asia/Magadan' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+11:00) Magadan', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Norfolk" 
									<?php
									if ( $ftsTimezone == 'Pacific/Norfolk' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+11:30) Norfolk Island', 'feed-them-gallery' ); ?></option>

									<option value="Asia/Anadyr" 
									<?php
									if ( $ftsTimezone == 'Asia/Anadyr' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+12:00) Anadyr, Kamchatka', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Auckland" 
									<?php
									if ( $ftsTimezone == 'Pacific/Auckland' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+12:00) Auckland, Wellington', 'feed-them-gallery' ); ?></option>

									<option value="Etc/GMT-12" 
									<?php
									if ( $ftsTimezone == 'Etc/GMT-12' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+12:00) Fiji, Kamchatka, Marshall Is.', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Chatham" 
									<?php
									if ( $ftsTimezone == 'Pacific/Chatham' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+12:45) Chatham Islands', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Tongatapu" 
									<?php
									if ( $ftsTimezone == 'Pacific/Tongatapu' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+13:00) Nuku\'alofa', 'feed-them-gallery' ); ?></option>

									<option value="Pacific/Kiritimati" 
									<?php
									if ( $ftsTimezone == 'Pacific/Kiritimati' ) {
										echo 'selected="selected"';}
									?>
									 ><?php _e( '(GMT+14:00) Kiritimati', 'feed-them-gallery' ); ?></option>
								</select>
							</fieldset>
						</div>
					</div>


					<div class="clear"></div>
					<div class="ft-gallery-date-settings-options-wrap">
						<h4><?php _e( 'Disable Magnific Popup CSS', 'feed-them-gallery' ); ?></h4>
						<p>
							<input name="ft_gallery_fix_magnific" class="fts-powered-by-settings-admin-input" type="checkbox" id="ft_gallery_fix_magnific" value="1" <?php echo checked( '1', get_option( 'ft_gallery_fix_magnific' ) ); ?>/> <?php _e( 'Check this if your theme is already loading the style sheet for the popup.', 'feed-them-gallery' ); ?>
						</p>

						<div class="clear"></div>

						<h4><?php _e( 'Disable Duplicate Gallery Option', 'feed-them-gallery' ); ?></h4>
						<p>
							<input name="ft_gallery_duplicate_post_show" class="fts-powered-by-settings-admin-input" type="checkbox" id="ft_gallery_duplicate_post_show" value="1" <?php echo checked( '1', get_option( 'ft_gallery_duplicate_post_show' ) ); ?>/> <?php _e( 'Check this if you already have a duplicate post plugin installed.', 'feed-them-gallery' ); ?>
						</p>


						<div class="clear"></div>
						<h4><?php _e( 'Admin Menu Bar Option', 'feed-them-gallery' ); ?></h4>
						<label><?php _e( 'Menu Bar', 'feed-them-gallery' ); ?></label>
						<select id="ft-gallery-admin-bar-menu" name="ft-gallery-admin-bar-menu">
							<option value="show-admin-bar-menu" 
							<?php
							if ( $ssAdminBarMenu == 'show-admin-bar-menu' ) {
								echo 'selected="selected"';}
							?>
							><?php _e( 'Show Admin Bar Menu', 'feed-them-gallery' ); ?></option>
							<option value="hide-admin-bar-menu" 
							<?php
							if ( $ssAdminBarMenu == 'hide-admin-bar-menu' ) {
								echo 'selected="selected"';}
							?>
							><?php _e( 'Hide Admin Bar Menu', 'feed-them-gallery' ); ?></option>
						</select>

						<div class="clear"></div>

						<div class="ft-gallery-date-settings-options-wrap">
							<h4><?php _e( 'Powered by Text', 'feed-them-gallery' ); ?></h4>
							<p>
								<input name="ft-gallery-powered-text-options-settings" class="ft-powered-by-settings-admin-input" type="checkbox" id="ft-gallery-powered-text-options-settings" value="1" <?php echo checked( '1', get_option( 'ft-gallery-powered-text-options-settings' ) ); ?>/>
								<?php
								if ( get_option( 'ft-gallery-powered-text-options-settings' ) == '1' ) {
									?>
									<strong><?php _e( 'Checked: ', 'feed-them-gallery' ); ?></strong> <?php _e( 'You are not showing the Powered by Logo in the popup.', 'feed-them-gallery' ); ?>
																  <?php
								} else {
									?>
									<strong><?php _e( 'Not Checked: ', 'feed-them-gallery' ); ?></strong><?php _e( 'The Powered by text will appear in the popup. Awesome! Thanks so much for sharing.', 'feed-them-gallery' ); ?>
																	  <?php
								}
								?>
							</p>
						</div>

							<div class="ft-gallery-woo-settings-options-wrap">

							<h4><?php _e( 'Woocommerce Options', 'feed-them-gallery' ); ?></h4>

							<?php if ( is_plugin_active( 'feed-them-gallery-premium/feed-them-gallery-premium.php' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
								<div class="settings-sub-wrap">
									<h5><?php _e( 'Product Creation', 'ft-gallery' ); ?></h5>

									<label><input name="ft_gallery_attch_prod_to_gallery_cat" type="checkbox" value="true" <?php echo checked( 'true', get_option( 'ft_gallery_attch_prod_to_gallery_cat' ) ); ?>/> <?php _e( 'Attach Product to a Category named after Gallery', 'ft-gallery' ); ?>
									</label>

									<div class="clear"></div>

									<h5 style="margin-top: 30px;"><?php _e( 'Add to Cart Button Functionality', 'ft-gallery' ); ?></h5>

									<?php $woo_options = get_option( 'ft_gallery_woo_add_to_cart' ) ? get_option( 'ft_gallery_woo_add_to_cart' ) : 0; ?>

									<label><input name="ft_gallery_woo_add_to_cart[ft_gallery_woo_options]" type="radio" value="prod_page" <?php checked( 'prod_page', $woo_options['ft_gallery_woo_options'] ); ?>> <strong><?php _e( '(Default)', 'ft-gallery' ); ?></strong> <?php _e( 'Take Customers to product page. (Doesn\'t add product to cart)', 'ft-gallery' ); ?>
									</label>

									<label><input name="ft_gallery_woo_add_to_cart[ft_gallery_woo_options]" type="radio" value="cart_checkout" <?php checked( 'cart_checkout', $woo_options['ft_gallery_woo_options'] ); ?>> <?php _e( 'Take user directly to checkout. Useful for variable products.', 'ft-gallery' ); ?>
									</label>

									<label><input name="ft_gallery_woo_add_to_cart[ft_gallery_woo_options]" type="radio" value="add_cart" <?php checked( 'add_cart', $woo_options['ft_gallery_woo_options'] ); ?>> <?php _e( 'Add product to cart. (Adds product to cart but doesn\'t take them to checkout.) This will not work if your product has required variations.', 'ft-gallery' ); ?>
									</label>

									<label><input name="ft_gallery_woo_add_to_cart[ft_gallery_woo_options]" type="radio" value="add_cart_checkout" <?php checked( 'add_cart_checkout', $woo_options['ft_gallery_woo_options'] ); ?>> <?php _e( 'Add product to cart and take user directly to checkout. This will not work if your product has required variations.', 'ft-gallery' ); ?>
									</label>

									<div class="clear"></div>
								</div>

								</div>

								<?php
} else {
	echo '<div class="ft-gallery-premium-mesg">Please purchase <a href="https://www.slickremix.com/downloads/feed-them-gallery/" target="_blank">Feed Them Gallery Premium</a> for the Awesome additional features!</div>  ';
}
?>
							<div class="clear"></div>

						<input type="submit" class="ft-gallery-settings-admin-submit button button-primary button-larg" value="<?php _e( 'Save All Changes', 'ft-gallery' ); ?>" />

				</form>
			</div>
			<!--/ft-gallery-settings-admin-wrap-->
			<div class="clear"></div>
		</div><!--/ft-gallery-main-template-wrapper-all-->

		<h1 class="plugin-author-note"><?php _e( 'Plugin Authors Note', 'ft-gallery' ); ?></h1>
		<div class="fts-plugin-reviews">
			<div class="fts-plugin-reviews-rate">Feed Them Gallery was created by 2 Brothers, Spencer and Justin Labadie.
				That’s it, 2 people! We spend all our time creating and supporting our plugins. Show us some love if you
				like our plugin and leave a quick review for us, it will make our day!
				<a href="https://www.facebook.com/pg/SlickRemix/reviews/?ref=page_internal" target="_blank">Leave us a
					Review ★★★★★</a>
			</div>
			<div class="fts-plugin-reviews-support">If you're having troubles getting setup please contact us. We will
				respond within 24hrs, but usually within 1-6hrs.
				<a href="https://www.slickremix.com/support/" target="_blank">Create Support Ticket</a>
				<div class="fts-text-align-center">
					<a class="feed-them-gallery-admin-slick-logo" href="https://www.slickremix.com" target="_blank"></a>
				</div>
			</div>
		</div>

		<!-- These scripts must load in the footer of page -->
		<script>
			jQuery(document).ready(function () {
				jQuery(".toggle-custom-textarea-show").click(function () {
					jQuery('textarea#ft-gallery-main-wrapper-css-input').slideToggle('fast');
					jQuery('.toggle-custom-textarea-show span').toggle();
					jQuery('.ft-gallery-custom-css-text').toggle();
				});
			});
		</script>
		<?php
	}
}//end class
