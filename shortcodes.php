<?php namespace feed_them_gallery;

/**
 * Shortcodes for Feed Them Gallery
 */
class Shortcodes extends Gallery {

    function __construct() {
        //Add to show ALL Galleries that have these Categories
        add_shortcode('ft-gallery-album', array($this, 'ft_gallery_album'));
    }

    function ft_gallery_album($atts) {

        //echo 'testing';

        $shortcode_atts = shortcode_atts(array(
            //All We need is ID of Gallery Post the rest will be passed through a rest call
            'id' => '',
        ), $atts);

        $album_gallery_ids = get_post_meta($shortcode_atts['id'], 'ft_gallery_album_gallery_ids', true);

        $album_output = '';

        if(isset($album_gallery_ids) && !empty($album_gallery_ids)){

            $albums_class = new Albums();

            $album_output = '<div class="ftg-album-wrap">';

                foreach ($album_gallery_ids as $key => $gallery) {

                    $gallery_meta = get_post($gallery);

                    /*echo '<pre>';
                    print_r($gallery_meta);
                    echo '</pre>';*/

                    if($gallery_meta){
                        $gallery_img_url = $albums_class->gallery_featured_first($gallery_meta->ID);

                        $gallery_edit_url = get_edit_post_link($gallery_meta->ID);
                        $gallery_post_link = get_post_permalink($gallery_meta->ID);
                        $gallery_attachments_count = $albums_class->ft_gallery_count_gallery_attachments($gallery_meta->ID);

                        $album_output .= '<div class="ftg-album-item-wrap">';

                        $album_output .= '<a href="'.$gallery_post_link.'"><img src="' . $gallery_img_url . '"/></a>';

                        $album_output .= '<a href="'.$gallery_post_link.'"><span>'.$gallery_meta->post_title.' '.(!empty($gallery_attachments_count) ? '('.$gallery_attachments_count.')': '' ).'</span></a>';

                        $album_output .= '</div>';
                    }
                }
            $album_output .= '</div>';

            echo $album_output;
        }
        else {
            if(empty($ignore_echos) || !empty($ignore_echos) && $ignore_echos !== 'true') {
                $album_output = __('No Galleries in this Album. Please attach Galleries to use this feature', 'feed-them-gallery');
            }
        }
    }
}