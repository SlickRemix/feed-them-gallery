//SLICKREMIX START OUR CUSTOM POPUPS
jQuery(document).ready(function () {

    jQuery('#fts-gallery-checkAll').toggle(function (event) {
        event.preventDefault(); // stop post action
        jQuery('#img1plupload-thumbs input:checkbox').attr('checked', 'checked');
        jQuery(this).html('Clear All')
        jQuery(".wp-core-ui .button-primary.ft-gallery-download-selection-option").show();
    }, function () {
        jQuery('#img1plupload-thumbs input:checkbox').removeAttr('checked');
        jQuery(".wp-core-ui .button-primary.ft-gallery-download-selection-option").hide();
        jQuery(this).html('Select All');
    });


    jQuery('#img1plupload-thumbs img, #img1plupload-thumbs .ft-gallery-myCheckbox').toggle(function (event) {
        event.preventDefault(); // stop post action
        if (jQuery("#img1plupload-thumbs input").length > 0) {
            jQuery(".wp-core-ui .button-primary.ft-gallery-download-selection-option").show();
        }
        jQuery(this).parents('.thumb').find('input:checkbox').attr('checked', 'checked');
    }, function () {
        jQuery(this).parents('.thumb').find('input:checkbox').removeAttr('checked');
        if (!jQuery("#img1plupload-thumbs input").is(":checked")) {

            jQuery(".wp-core-ui .button-primary.ft-gallery-download-selection-option").hide();
        }
    });


    // SLICKREMIX: MUST HAVE THIS IN PLACE TO BE ABLE TO CHECK WHAT KIND OF VIDEOS ARE BEING CLICKED ON
    jQuery('body').on('click', '#fts-photo-prev, #fts-photo-next, .ft-gallery-popup .mfp-image-holder .fts-popup-image-position', function (e) {
        // alert('test');
        jQuery("body").addClass("fts-using-arrows");

        setTimeout(function () {

            if (/fbcdn.net/i.test(jQuery(".fts-iframe-popup-element").attr("src")) || /scontent.cdninstagram.com/i.test(jQuery(".fts-iframe-popup-element").attr("src"))) {

                // alert(jQuery(".fts-iframe-popup-element").attr("src"));
                jQuery("body").addClass("fts-video-iframe-choice");
                jQuery(".fts-video-popup-element").show();
                jQuery(".fts-iframe-popup-element").attr("src", "").hide();

            }
            else {
                //  alert('wtf');
                jQuery("body").removeClass("fts-video-iframe-choice, .fts-using-arrows");
                jQuery(".fts-video-popup-element").attr("src", "").hide();
                jQuery(".fts-iframe-popup-element").show();
            }
        }, 10);
    });
    // CLOSE SLICKREMIX


// Facebook Posts
    var e = jQuery.magnificPopup.instance;
    jQuery("body").on("click", "#fts-photo-prev", function () {
        e.prev(), jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height() ? jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height()) : jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height())
    }), jQuery("body").on("click", "#fts-photo-next", function () {
        e.next(), jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height() ? jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height()) : jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height())
    }), jQuery("body").on("click", ".fts-facebook-popup .mfp-image-holder .fts-popup-image-position", function () {
        e.next(), jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height() ? jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height()) : jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height())
    }), jQuery.fn.slickFTGalleryPopUpFunction = function () {

        jQuery('.plupload-thumbs').each(function () {
            var $container = jQuery(this);
            var $imageLinks = $container.find('a.ft-gallery-edit-img-popup');


            var items = [];
            $imageLinks.each(function () {
                var $item = jQuery(this);
                var type = 'image';
                if ($item.hasClass('fts-jal-fb-vid-image')) {
                    type = 'iframe';
                }
                var magItem = {
                    src: $item.attr('href'),
                    type: type,
                    delegate: '.thumb:not(.hidden)',
                };

                if (jQuery("div").hasClass("ft-gallery-woo-btns-wrap-for-popup")) {
                    var woo_option = jQuery(this).parents('.thumb').find('.ft-gallery-woo-btns-wrap-for-popup').html();

                }
                else {
                    var woo_option = '';
                }


                // SLICKREMIX: THIS ADDS THE LIKES, COMMENTS, DESCRIPTION, DATES ETC TO THE POPUP
                magItem.title = jQuery(this).parents('.thumb').find('.ft-image-id-for-popup').html() + jQuery('.ft-gallery-popup-form').html() + woo_option;

                items.push(magItem);
            });

            $imageLinks.magnificPopup({
                mainClass: 'ft-gallery-popup ft-gallery-styles-popup',
                items: items,
                removalDelay: 150,
                preloader: false,
                closeOnContentClick: false,
                closeOnBgClick: true,
                closeBtnInside: true,
                showCloseBtn: false,
                enableEscapeKey: true,
                autoFocusLast: false,
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    tCounter: '<span class="mfp-counter">%curr% of %total%</span>', // markup of counter
                    preload: [0, 1], // Will preload 0 - before current, and 1 after the current
                    arrowMarkup: '', // markup of an arrow button (slickremix = leave blank so we can show our custom buttons inside the framework)
                },

                callbacks: {
                    beforeOpen: function () {
                        var index = $imageLinks.index(this.st.el);
                        if (-1 !== index) {
                            this.goTo(index);
                        }

                    },
                    open: function () {
                        console.log('Popup is opened');


                        jQuery(".fts-popup-image-position, #fts-photo-next").click(function (event) {

                            event.preventDefault(); // stop post action

                            var id = jQuery('.fts-popup-wrap').find('.fts-next-image').val();
                            var nonce = jQuery('.fts-popup-wrap').find('.fts-next-image').data('nonce');


                            //   var id = jQuery(this).data('id');
                            //   var nonce = jQuery(this).data('nonce');


                            //  alert(id + ' ' + nonce);

                            jQuery.ajax({
                                data: {
                                    'action': "ft_gallery_update_image_information_ajax",
                                    // submit our values to function simple_das_fep_add_post
                                    'id': id,
                                    'nonce': nonce
                                },
                                type: 'post',
                                url: ssAjax.ajaxurl,
                                beforeSend: function () {
                                    // alert('before')
                                    //  jQuery('#new_post .sidebar-sup-submit-wrap').append('<div class="fa fa-cog fa-spin fa-3x fa-fw sidebar-sup-loader"></div>');
                                    //  jQuery("#new_post .sidebar-sup-success").remove();
                                },
                                success: function (response) {
                                    // Complete Sucess
                                    var jsArray = JSON.parse(response);
                                    console.log('Well Done and got this from sever: ' + response);


                                    jQuery('.fts-gallery-title').val(jsArray['title']);
                                    jQuery('.fts-gallery-alttext').show().val(jsArray['alt']);
                                    jQuery('.fts-gallery-description').show().val(jsArray['description']);

                                    //  jQuery(thisDelete).parents('.thumb').fadeOut();

                                },
                                error: function () {
                                    alert('Error, please contact us at http://slickremix.com/support-forum for help.')
                                }
                            }); // end of ajax()
                            //  return false;


                        }); // end of form.submit


                        jQuery(".fts-popup-half").on("click", "#fts-photo-prev", function (event) {

                            event.preventDefault(); // stop post action

                            var id = jQuery('.fts-popup-wrap').find('.fts-prev-image').val();
                            var nonce = jQuery('.fts-popup-wrap').find('.fts-prev-image').data('nonce');


                            //   var id = jQuery(this).data('id');
                            //   var nonce = jQuery(this).data('nonce');


                            //  alert(id + ' ' + nonce);

                            jQuery.ajax({
                                data: {
                                    'action': "ft_gallery_update_image_information_ajax",
                                    // submit our values to function simple_das_fep_add_post
                                    'id': id,
                                    'nonce': nonce
                                },
                                type: 'post',
                                url: ssAjax.ajaxurl,
                                beforeSend: function () {
                                    // alert('before')
                                    //  jQuery('#new_post .sidebar-sup-submit-wrap').append('<div class="fa fa-cog fa-spin fa-3x fa-fw sidebar-sup-loader"></div>');
                                    //  jQuery("#new_post .sidebar-sup-success").remove();
                                },
                                success: function (response) {
                                    // Complete Sucess
                                    var jsArray = JSON.parse(response)
                                    console.log('Well Done and got this from sever: ' + response);


                                    jQuery('.fts-gallery-title').val(jsArray['title']);
                                    jQuery('.fts-gallery-alttext').show().val(jsArray['alt']);
                                    jQuery('.fts-gallery-description').show().val(jsArray['description']);

                                    //  jQuery(thisDelete).parents('.thumb').fadeOut();

                                },
                                error: function () {
                                    alert('Error, please contact us at http://slickremix.com/support-forum for help.')
                                }
                            }); // end of ajax()
                            //  return false;


                        }); // end of form.submit


                        jQuery(".mfp-title").on("click", "#ft-gallery-edit-img-ajax", function (event) {
                            event.preventDefault(); // stop post action

                            //  if(side_sup_double_check_function() == 'You pressed Cancel!'){return false};

                            var id = jQuery(this).parents('.mfp-title').find('.fts-gallery-id').val();
                            var nonce = jQuery(this).data('nonce');
                            //  var remove = jQuery(this).data('ft-gallery-img-edit');

                            var title = jQuery(this).parents('.mfp-title').find('.fts-gallery-title').val();
                            var alttext = jQuery(this).parents('.mfp-title').find('.fts-gallery-alttext').val();
                            var description = jQuery(this).parents('.mfp-title').find('.fts-gallery-description').val();

                            jQuery.ajax({
                                data: {
                                    'action': "ft_gallery_edit_image_ajax",
                                    // submit our values to function simple_das_fep_add_post
                                    'id': id,
                                    'nonce': nonce,
                                    //  'ft_gallery_img_edit': remove,
                                    'title': title,
                                    'alttext': alttext,
                                    'description': description
                                },
                                type: 'POST',
                                url: ssAjax.ajaxurl,
                                beforeSend: function () {
                                    // alert('before');
                                    jQuery('.ft-submit-wrap').append('<div class="fa fa-cog fa-spin fa-3x fa-fw ft-gallery-loader"></div>');
                                    jQuery(".ft-gallery-success").remove();
                                },
                                success: function (response) {
                                    // Complete Sucess
                                    console.log('Well Done and got this from sever: ' + response);

                                    var thumbimg = '#thumbimg' + id;
                                    jQuery(thumbimg).find('.fts-gallery-title').val(title);
                                    jQuery(thumbimg).find('.fts-gallery-alttext').val(alttext);
                                    jQuery(thumbimg).find('.fts-gallery-description').val(description);


                                    //     jQuery('.mfp-title').find('.fts-gallery-title').val(title);
                                    //     jQuery('.mfp-title').find('.fts-gallery-alttext').val(alttext);
                                    //     jQuery('.mfp-title').find('.fts-gallery-description').val(description);


                                    //   alert(jQuery('.mfp-title').find('.fts-gallery-title').val());
                                    // jQuery(thisDelete).parents('.thumb').fadeOut();

                                    jQuery('.ft-gallery-loader').remove();

                                    jQuery('.ft-submit-wrap').append('<div class="fa fa-check-circle fa-3x fa-fw ft-gallery-success" ></div>');

                                    setTimeout("jQuery('.ft-gallery-success').fadeOut();", 2000);

                                },
                                error: function () {
                                    alert('Error, please contact us at http://slickremix.com/support-forum for help.')
                                }
                            }); // end of ajax()
                            return false;
                        }); // end of form.submit


                        if (jQuery(".fts-popup-half .mfp-iframe-scaler")[0]) {
                            jQuery(".fts-popup-image-position").css("height", '591px');
                        }
                        jQuery(window).resize(function () {

                            jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height());

                            jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height());
                        });
                        jQuery(window).trigger('resize');

                        // slickremix trick to get the poster url from a tag we are clicking and pass it to the video player.
                        // We only want to load the poster if the size is mobile because tablets and desktops can/will play video automatically on popup
                        if (matchMedia('only screen and (max-device-width: 736px)').matches) {
                            var atagvideo = event.target.id;
                            var videoposter = jQuery('#' + atagvideo).data('poster');
                            var video = jQuery('.fts-fb-vid-popup video');
                            video.attr('poster', videoposter);
                            //	alert(videoposter);
                            console.log(videoposter);
                        }
                        // SLICKREMIX: MUST HAVE THIS IN PLACE TO BE ABLE TO ADD THE CLASS TO BODY SO WE CAN DO ACTIONS ON OUR CUSTOM PREV AND NEXT BUTTONS
                        // alert('added fts-using-arrows class on popup open')
                        jQuery("body").addClass("fts-using-arrows");


                    },
                    change: function () {
                        console.log('Content changed');
                        console.log(this.content); // Direct reference to your popup element
                        if (jQuery("body").hasClass("fts-using-arrows")) {


                            if (jQuery(".fts-popup-half .mfp-iframe-scaler")[0]) {
                                jQuery(".fts-popup-image-position").css("height", '591px');
                                //  alert('iframe-scaler');
                            }
                            else {
                                if (jQuery(".fts-popup-image-position").css("height") == "auto") {
                                    jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height());
                                    alert('image');

                                }
                            }


                        }


                    },

                    imageLoadComplete: function () {
                        // fires when image in current popup finished loading
                        // avaiable since v0.9.0

                        if (jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height()) {
                            jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height());
                            // alert('image');

                        }
                        else {
                            jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height());
                            //  alert('change');
                        }

                    },
                    markupParse: function (template, values, item) {
                        // Triggers each time when content of popup changes 
                        console.log('Parsing:', template, values, item);

                        // SLICKREMIX: MUST HAVE THIS IN PLACE TO BE ABLE TO CHECK WHAT KIND OF VIDEOS ARE BEING CLICKED ON
                        if (!jQuery("body").hasClass("fts-using-arrows")) {

                            var ftsLinkCheck = item.src;

                            if (/fbcdn.net/i.test(ftsLinkCheck) && item.type !== 'image') {
                                // alert('FB Video Change photo Trigger from MP');
                                jQuery("body").addClass("fts-video-iframe-choice");
                            }
                            else if (!jQuery("body").hasClass("fts-using-arrows")) {
                                //  alert('Not using arrows open photo Trigger from MP');
                                jQuery("body").removeClass("fts-video-iframe-choice");
                            }

                        }
                        // CLOSE SLICKREMIX

                    },
                    afterClose: function () {
                        jQuery("body").removeClass("fts-using-arrows");
                        console.log('Popup is completely closed');
                    },
                },
                image: {
                    markup: '' +
                    '<div class="mfp-figure"><div class="mfp-close">X</div>' +
                    '<div class="fts-popup-wrap">' +
                    '    <div class="fts-popup-half ">' +
                    '               <button title="previous" type="button" id="fts-photo-prev" class="mfp-arrow mfp-arrow-left mfp-prevent-close"></button>' +
                    '           <div class="fts-popup-image-position" style="height:591px;">' +
                    '                   <span class="fts-position-helper"></span><div class="mfp-img"></div>' +
                    '       </div>' +
                    '               <button title="next" type="button" id="fts-photo-next" class="mfp-arrow mfp-arrow-right mfp-prevent-close"></button>' +
                    '    </div>' +
                    '<div class="fts-popup-second-half">' +
                    '<div class="mfp-bottom-bar">' +
                    '<div class="mfp-title"></div>' +
                    '<a class="fts-powered-by-text" href="https://slickremix.com" target="_blank">Powered by Feed Them Gallery</a>' +
                    '<div class="mfp-counter"></div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>', // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button

                    tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',

                },
                iframe: {
                    markup: '' +
                    '<div class="mfp-figure"><div class="mfp-close">X</div>' +
                    '<div class="fts-popup-wrap">' +
                    '    <div class="fts-popup-half ">' +
                    '               <button title="previous" type="button" id="fts-photo-prev" class="mfp-arrow mfp-arrow-left mfp-prevent-close"></button>' +
                    '           <div class="fts-popup-image-position">' +
                    '                           <div class="mfp-iframe-scaler"><iframe class="mfp-iframe fts-iframe-popup-element" frameborder="0" allowfullscreen></iframe><video class="mfp-iframe fts-video-popup-element" allowfullscreen autoplay controls></video>' +
                    '                           </div>' +
                    '               <button title="next" type="button" id="fts-photo-next" class="mfp-arrow mfp-arrow-right mfp-prevent-close"></button>' +
                    '<script>' +
                    // SLICKREMIX: MUST HAVE THIS IN PLACE TO BE ABLE TO CHECK WHAT KIND OF VIDEOS ARE BEING CLICKED ON WHEN FIRST LOADED, AFTER THEY ARE LOADED REFER TO THE CLICK FUNCTION FOR THE ERRORS ABOVE
                    'if(jQuery("body").hasClass("fts-video-iframe-choice")){jQuery(".fts-iframe-popup-element").attr("src", "").hide(); } else if(!jQuery("body").hasClass("fts-using-arrows")){jQuery(".fts-video-popup-element").attr("src", "").hide(); };  jQuery(".ft-gallery-popup video").click(function(){jQuery(this).trigger(this.paused ? this.paused ? "play" : "play" : "pause")});</script>' +
                    '       </div>' +
                    '    </div>' +
                    '<div class="fts-popup-second-half">' +
                    '<div class="mfp-bottom-bar">' +
                    '<div class="mfp-title"></div>' +
                    '<a class="fts-powered-by-text" href="https://slickremix.com" target="_blank">Powered by Feed Them Social</a>' +
                    '<div class="mfp-counter"></div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>', // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button

                    srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".  
                }
            });

        });
    }
//Return the function right away
    jQuery.fn.slickFTGalleryPopUpFunction();

    // TEST POPUP WITH ALERTS FOR DEBUGGIN
    // 'if(jQuery("body").hasClass("fts-video-iframe-choice")){alert("fts-video-choice not using arrows"); jQuery(".fts-iframe-popup-element").attr("src", "").hide(); } else if(!jQuery("body").hasClass("fts-using-arrows")){alert("fts-iframe-choice not using arrows"); jQuery(".fts-video-popup-element").attr("src", "").hide(); };  jQuery(".ft-gallery-popup video").click(function(){jQuery(this).trigger(this.paused ? this.paused ? "play" : "play" : "pause")});</script>' +


    function side_sup_double_check_function() {
        var x;
        if (confirm("You are about to permanently delete this Topic. The post(s) will not be deleted but moved to the bottom of the page where it says, Items below have no Topics. 'Cancel' to stop, 'OK' to delete.") == true) {
            x = "You pressed OK!";
        } else {
            x = "You pressed Cancel!";
        }
        return x;
    }


    jQuery(".ft-gallery-remove-thumb-btn").on("click", ".ft-gallery-remove-img-ajax", function (event) {
        event.preventDefault(); // stop post action

        //  if(side_sup_double_check_function() == 'You pressed Cancel!'){return false};

        var id = jQuery(this).data('id');
        var nonce = jQuery(this).data('nonce');
        var remove = jQuery(this).data('ft-gallery-img-remove');
        var thisDelete = jQuery(this);


        jQuery.ajax({
            data: {
                'action': "ft_gallery_update_image_ajax",
                // submit our values to function simple_das_fep_add_post
                'id': id,
                'nonce': nonce,
                'ft_gallery_img_remove': remove
            },
            type: 'POST',
            url: ssAjax.ajaxurl,
            beforeSend: function () {
                //  alert('Are sure you want to do this? You cannot undo this operation.')
                //  jQuery('#new_post .sidebar-sup-submit-wrap').append('<div class="fa fa-cog fa-spin fa-3x fa-fw sidebar-sup-loader"></div>');
                //  jQuery("#new_post .sidebar-sup-success").remove();
            },
            success: function (response) {
                // Complete Sucess
                console.log('Well Done and got this from sever: ' + response);
                jQuery(thisDelete).parents('.thumb').hide();

            },
            error: function () {
                alert('Error, please contact us at https://slickremix.com/ for help.')
            }
        }); // end of ajax()
        return false;
    }); // end of form.submit


    jQuery(".ft-gallery-delete-thumb-btn").on("click", ".ft-gallery-force-delete-img-ajax", function (event) {
        event.preventDefault(); // stop post action

        var r = confirm('You are about to permanently delete this item from your site.\nThis action cannot be undone.\n\n"Cancel" to stop, "OK" to delete.');
        if (r == true) {
            txt = "You pressed OK!";
        } else {
            return false;
        }


        var id = jQuery(this).data('id');
        var nonce = jQuery(this).data('nonce');
        var thisDelete = jQuery(this);

        jQuery.ajax({
            data: {
                'action': "ft_gallery_delete_image_ajax",
                // submit our values to function simple_das_fep_add_post
                'id': id,
                'nonce': nonce
            },
            type: 'POST',
            url: ssAjax.ajaxurl,
            beforeSend: function () {


                // alert('before')
                //  jQuery('#new_post .sidebar-sup-submit-wrap').append('<div class="fa fa-cog fa-spin fa-3x fa-fw sidebar-sup-loader"></div>');
                //  jQuery("#new_post .sidebar-sup-success").remove();
            },
            success: function (response) {
                // Complete Sucess
                console.log('Well Done and got this from sever: ' + response);
                jQuery(thisDelete).parents('.thumb').addClass('hidden').hide();


            },
            error: function () {
                alert('Error, please contact us at http://slickremix.com/support-forum for help.')
            }
        }); // end of ajax()
        return false;
    }); // end of form.submit


    jQuery(".ft-gallery-edit-thumb-btn").on("click", ".ft-gallery-edit-img-popup", function (event) {

        event.preventDefault(); // stop post action

        //  var id = jQuery(this).parents('.mfp-title').find('.fts-gallery-id').val();
        //  var nonce = jQuery(this).parents('.mfp-title').find('.fts-gallery-id').data('nonce');

        var id = jQuery(this).data('id');
        var nonce = jQuery(this).data('nonce');


        //  alert(id + ' ' + nonce);

        jQuery.ajax({
            data: {
                'action': "ft_gallery_update_image_information_ajax",
                // submit our values to function simple_das_fep_add_post
                'id': id,
                'nonce': nonce
            },
            type: 'post',
            url: ssAjax.ajaxurl,
            beforeSend: function () {
                // alert('before')
                //  jQuery('#new_post .sidebar-sup-submit-wrap').append('<div class="fa fa-cog fa-spin fa-3x fa-fw sidebar-sup-loader"></div>');
                //  jQuery("#new_post .sidebar-sup-success").remove();
            },
            success: function (response) {
                // Complete Sucess
                var jsArray = JSON.parse(response)
                console.log('Well Done and got this from sever: ' + response);


                jQuery('.fts-gallery-title').val(jsArray['title']);
                jQuery('.fts-gallery-alttext').show().val(jsArray['alt']);
                jQuery('.fts-gallery-description').show().val(jsArray['description']);

                //  jQuery(thisDelete).parents('.thumb').fadeOut();


            },
            error: function () {
                alert('Error, please contact us at http://slickremix.com/support-forum for help.')
            }
        }); // end of ajax()
        return false;


    }); // end of form.submit

    function get_tinymce_content(id) {
        var content;
        var inputid = 'editpost';
        var editor = tinyMCE.get(inputid);
        var textArea = jQuery('textarea#' + inputid);
        if (textArea.length > 0 && textArea.is(':visible')) {
            content = textArea.val();
            if (content == null) {
                return false;
            }
        } else {
            content = editor.getContent();
        }
        return content;
    }

}); // close document ready