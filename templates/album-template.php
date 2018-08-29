<?php
/**
 * The template for displaying Feed Them Gallery Album pages
 *
 * @link http://feedthemgallery.com/
 *
 * @package Feed Them Gallery
 * @since 1.0.8
 * @version 1.0.8
 */

get_header();?>

    <div class="wrap">

        <?php if ( have_posts() ) : ?>
            <header class="page-header">
                <?php
                the_archive_title( '<h1 class="page-title">', '</h1>' );
                the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
            </header><!-- .page-header -->
        <?php endif; ?>

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php
                global $post;

                $Album_ID = $post->ID;

                if(!empty($Album_ID)){
                   echo do_shortcode('[ft-gallery-album id="298"]');
                }

                ?>

            </main><!-- #main -->
        </div><!-- #primary -->
        <?php get_sidebar(); ?>
    </div><!-- .wrap -->

<?php
get_footer();
