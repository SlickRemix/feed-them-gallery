function ft_gallery_image_to_woo(gallery_id){

    //Selected Media
    var selectedmedia = [];
    jQuery('#tab-content1 section li div.ft-gallery-select-thumbn input[type=checkbox]').each(function () {
        if (jQuery(this).attr('checked')) {
            selectedmedia.push(jQuery(this).attr('rel'));
        }
    });

    if (selectedmedia.length) {
        selectedmedia = JSON.stringify(selectedmedia);
    }

    jQuery.ajax({
        data: {
            action: "ft_gallery_image_to_woo_prod",
            GalleryID: gallery_id,
            selectedMedia: selectedmedia
        },
        type: 'POST',
        async: true,
        url: ftgallerytoWooAjax.ajaxurl,
        beforeSend: function () {
            jQuery('.ft-gallery-notice').empty().removeClass('ftg-block');
            jQuery('.ft-gallery-notice').removeClass('updated').addClass('ftg-block');
            jQuery('.ft-gallery-notice').prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>');
        },
        success: function (response) {
            console.log('Well Done and got this from sever: ' + response);

            //'Woocommerce Product created from Image(s)! '
            jQuery('.ft-gallery-notice').html(response);
            jQuery('.ft-gallery-notice').addClass('updated');

            jQuery('.ft_gallery_download_button').removeAttr('disabled').removeClass('ft_gallery_download_button_loading');

            return false;
        }
    }); // end of ajax()
    return false;
} // end of form.submit

function ft_gallery_zip_to_woo(gallery_id,zipID){
    //jQuery('.ft_gallery_download_button').attr('disabled', '').addClass('ft_gallery_download_button_loading');

    jQuery.ajax({
        data: {action: "ft_gallery_zip_to_woo_prod", GalleryID: gallery_id, ZIP_ID: zipID},
        type: 'POST',
        async: true,
        url: ftgallerytoWooAjax.ajaxurl,
        beforeSend: function () {
            jQuery('.ft-gallery-notice').empty().removeClass('ftg-block');
            jQuery('.ft-gallery-notice').removeClass('updated').addClass('ftg-block');
            jQuery('.ft-gallery-notice').prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>');
        },
        success: function (response) {
            console.log('Well Done and got this from sever: ' + response);

            //'Woocommerce Product created from Image(s)! '
            jQuery('.ft-gallery-notice').html(response);
            jQuery('.ft-gallery-notice').addClass('updated');

            jQuery('.ft_gallery_download_button').removeAttr('disabled').removeClass('ft_gallery_download_button_loading');

            return false;
        }
    }); // end of ajax()
    return false;
} // end of form.submit