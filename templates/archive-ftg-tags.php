<?php
/**
 * Template Name: archive-ftg-tags
 *
 * @link https://feedthemgallery.com/
 *
 * @package Feed Them Gallery
 * @since 1.1.6
 * @version 1.1.6
 */

get_header();

$gallery_tag      = isset( $_GET['ftg-tags'] ) ? sanitize_text_field( wp_unslash( $_GET['ftg-tags'] ) ) : null;
$image_or_gallery = isset( $_GET['ftg-tags'], $_GET['type'] ) && 'page' === $_GET['type'] ? 'Galleries' : 'Images'; ?>

	<div id="top">
		<div class="title_container">
			<div class="page-header container">
				Displaying <?php echo esc_html( $image_or_gallery ); ?> with the tag:
										<?php
										echo esc_html( $gallery_tag )
										?>
			</div><!-- .page-header -->
		</div>
	</div>

	<div id="primary" class="content-area container">
		<main id="main" class="site-main" role="main">
			<?php
			// Still need to create a select option that shows all the galleries in the list and defaults to the first one below if no gallery id selected.
			if ( ! empty( $gallery_tag ) ) {

				print do_shortcode( '[feed-them-gallery id=tags]' );
			}
			?>
		</main>
		<!-- #main -->
	</div>
	<!-- #primary -->

<?php
get_footer();

