<?php
/**
 * The template for displaying the Feed Them Gallery - Gallery page
 *
 * @link http://feedthemgallery.com/
 *
 * @package Feed Them Gallery
 * @since 1.0.8
 * @version 1.0.8
 */

get_header();


global $post;

$Gallery_ID = $post->ID;

?>

    <div class="wrap">

        <?php if ( have_posts() ) : ?>
            <header class="page-header">
               <h1 class="page-title"> <?php
                the_title(); ?></h1>
                <?php
                the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
            </header><!-- .page-header -->
        <?php endif; ?>

        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php

                if(!empty($Gallery_ID)){
                   echo do_shortcode('[feed-them-gallery id="'.$Gallery_ID.'"]');
                }

                ?>

            </main><!-- #main -->
        </div><!-- #primary -->
        <?php get_sidebar(); ?>
    </div><!-- .wrap -->

<?php
get_footer();
