function ft_gallery_add_galleries_to_album(album_id){

    //Selected Media
    var addselectedgalleries = [];
    jQuery('#ftg-tab-content1 li.out-album div.ft-gallery-select-thumbn input[type=checkbox]').each(function () {
        if (jQuery(this).attr('checked')) {
            addselectedgalleries.push(jQuery(this).attr('rel'));
        }
    });

    if (addselectedgalleries.length) {
        addselectedgalleries = JSON.stringify(addselectedgalleries);
    }

    jQuery.ajax({
        data: {
            action: "ft_gallery_add_galleries_to_album",
            AlbumID: album_id,
            addselectedGalleries: addselectedgalleries
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
            jQuery('.ft-gallery-notice').append('<div class="ft-gallery-notice-close"></div>');


            jQuery('.ft_gallery_download_button').removeAttr('disabled').removeClass('ft_gallery_download_button_loading');

            return false;
        }
    }); // end of ajax()
    return false;
} // end of form.submit



// function ft_gallery_delete_galleries_from_album(album_id){
//
//     //Selected Media
//     var deleteselectedgalleries = [];
//     jQuery('#ftg-tab-content1 li.in-album div.ft-gallery-select-thumbn input[type=checkbox]').each(function () {
//         if (jQuery(this).attr('checked')) {
//             deleteselectedgalleries.push(jQuery(this).attr('rel'));
//         }
//     });
//
//     if (deleteselectedgalleries.length) {
//         deleteselectedgalleries = JSON.stringify(deleteselectedgalleries);
//     }
//
//     jQuery.ajax({
//         data: {
//             action: "ft_gallery_delete_galleries_from_album",
//             AlbumID: album_id,
//             deleteselectedGalleries: deleteselectedgalleries
//         },
//         type: 'POST',
//         async: true,
//         url: ftgallerytoWooAjax.ajaxurl,
//         beforeSend: function () {
//             jQuery('.ft-gallery-notice').empty().removeClass('ftg-block');
//             jQuery('.ft-gallery-notice').removeClass('updated').addClass('ftg-block');
//             jQuery('.ft-gallery-notice').prepend('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>');
//         },
//         success: function (response) {
//             console.log('Well Done and got this from sever: ' + response);
//
//             //'Woocommerce Product created from Image(s)! '
//             jQuery('.ft-gallery-notice').html(response);
//             jQuery('.ft-gallery-notice').addClass('updated');
//             jQuery('.ft-gallery-notice').append('<div class="ft-gallery-notice-close"></div>');
//
//
//             jQuery('.ft_gallery_download_button').removeAttr('disabled').removeClass('ft_gallery_download_button_loading');
//
//             return false;
//         }
//     }); // end of ajax()
//     return false;
// } // end of form.submit