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

$gallery_tag = isset($_GET['ftg-tags']) ? $_GET['ftg-tags'] : NULL;
$image_or_gallery = isset($_GET['ftg-tags'], $_GET['type']) && 'page' === $_GET['type'] ? 'Galleries' : 'Images'; ?>

    <div id="top">
        <div class="title_container">
            <div class="page-header container">
                Displaying <?php echo $image_or_gallery ?> with the tag: <?php
                echo $gallery_tag
                ?>
            </div><!-- .page-header -->
        </div>
    </div>

    <div id="primary" class="content-area container">
        <main id="main" class="site-main" role="main">
            <?php
            // Still need to create a select option that shows all the galleries in the list and defaults to the first one below if no gallery id selected.
            if(!empty($gallery_tag)){

                print do_shortcode('[feed-them-gallery id=tags]');
            }
            ?>
        </main>
        <!-- #main -->
    </div>
    <!-- #primary -->

<?php
get_footer();

