<?php

/**
 * Template Name: Feed Them Gallery Album Template
 *
 * @link https://feedthemgallery.com/
 *
 * @package Feed Them Gallery
 * @since 1.1.6
 * @version 1.1.6
 */

get_header(); ?>

            <?php   while ( have_posts() ) : the_post(); ?>

    <div id="top">
        <div class="title_container">
                <div class="page-header container">
                    <h2><?php
                        the_title( );
                        ?></h2>
                </div><!-- .page-header -->
        </div>
    </div>
<?php endwhile;  // End of the loop. ?>

    <div id="primary" class="content-area container">
    <main id="main" class="site-main" role="main">
                <?php

                    global $post;

                    $album_id = $post->ID;
                    if(!empty($album_id)){
                        print do_shortcode('[ft-gallery-album id="'.$album_id.'"]');
                    }

                ?>
        </main>
        <!-- #main -->
    </div>
    <!-- #primary -->

<?php
get_footer();
