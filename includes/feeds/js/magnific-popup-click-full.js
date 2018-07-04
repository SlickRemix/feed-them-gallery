jQuery(document).ready(function () {

var e = jQuery.magnificPopup.instance;
    jQuery("body").on("click", "#fts-photo-prev", function() {
        e.prev(), jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height() ? jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height()) : jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height())
    }), jQuery("body").on("click", "#fts-photo-next", function() {
        e.next(), jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height() ? jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height()) : jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height())
    }), jQuery("body").on("click", ".fts-facebook-popup .mfp-image-holder .fts-popup-image-position", function() {
        e.next(), jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").height() < jQuery(".mfp-img").height() ? jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".mfp-img").height()) : jQuery(".fts-popup-second-half .mfp-bottom-bar").css("height", jQuery(".fts-popup-image-position").height())
    }), jQuery("body").on("click", "#fts-photo-prev, #fts-photo-next, .fts-facebook-popup .mfp-image-holder .fts-popup-image-position", function(e) {
        jQuery("body").addClass("fts-using-arrows"), setTimeout(function() {
            jQuery.fn.ftsShare(), /fbcdn.net/i.test(jQuery(".fts-iframe-popup-element").attr("src")) || /scontent.cdninstagram.com/i.test(jQuery(".fts-iframe-popup-element").attr("src")) ? (jQuery("body").addClass("fts-video-iframe-choice"), jQuery(".fts-video-popup-element").show(), jQuery(".fts-iframe-popup-element").attr("src", "").hide()) : (jQuery("body").removeClass("fts-video-iframe-choice, .fts-using-arrows"), jQuery(".fts-video-popup-element").attr("src", "").hide(), jQuery(".fts-iframe-popup-element").show())
        }, 10)
    });

    // Feed Them Gallery Posts
    jQuery.fn.slickWordpressPopUpFunction = function() {

        jQuery('.ft-wp-gallery').each(function () {
            var $container = jQuery(this);
            var $imageLinks = $container.find('.ft-gallery-link-popup-click-action');


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
                };

                var $aSelected = jQuery($item).find('div');

                if($aSelected.hasClass( "ft-image-overlay" )){
                    var overlay_option = jQuery(this).parents('.fts-feed-type-wp_gallery').find('.ft-image-overlay').html();
                    // alert('wtf');
                }
                else{
                    var overlay_option = '';
                }

                if(jQuery( "div" ).hasClass( "fts-mashup-count-wrap" )){
                    var share_option = jQuery(this).parents('.fts-feed-type-wp_gallery').find('.fts-mashup-count-wrap').html();

                }
                else{
                    var share_option = '';
                }

                // SLICKREMIX: THIS ADDS THE LIKES, COMMENTS, DESCRIPTION, DATES ETC TO THE POPUP
                magItem.title = jQuery(this).parents('.fts-feed-type-wp_gallery').find('.ft-text-for-popup').html() + share_option + overlay_option;

                items.push(magItem);


            });
            $imageLinks.magnificPopup({
                mainClass: 'ft-gallery-popup ft-gallery-styles-popup',
                items: items,
                //  delegate: '.thumb:not(.hidden)',
                removalDelay: 150,
                preloader: false,
                closeOnContentClick: false,
                closeOnBgClick: true,
                closeBtnInside: true,
                showCloseBtn: false,
                enableEscapeKey: true,
                autoFocusLast: false,
                gallery:{
                    enabled: true,
                    navigateByImgClick: true,
                    tCounter: '<span class="mfp-counter">%curr% of %total%</span>', // markup of counter
                    preload: [0,1], // Will preload 0 - before current, and 1 after the current
                    arrowMarkup: '', // markup of an arrow button (slickremix = leave blank so we can show our custom buttons inside the framework)
                },
                type: 'image',
                callbacks: {
                    beforeOpen: function() {
                        var index = $imageLinks.index(this.st.el);
                        if (-1 !== index) {
                            this.goTo(index);
                        }

                    },
                    open: function() {
                        console.log('Popup is opened');


                        //we are loading logo in the right panel, but need it in the image panel so we clone it and append it where we want it.
                        jQuery('.fts-popup-second-half .fts-watermark-inside').clone().appendTo('.fts-popup-half');

                        //make it so you can't just drag the image to your desktop
                        window.ondragstart = function() { return false; }

                        if(jQuery(".fts-popup-half .mfp-iframe-scaler")[0]){
                            jQuery( ".fts-popup-image-position" ).css("height", '591px');
                        }
                        jQuery(window).resize(function() {

                            jQuery( ".fts-popup-second-half .mfp-bottom-bar" ).css("height", jQuery( ".fts-popup-image-position" ).height());

                            jQuery( ".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar" ).css("height", jQuery( ".mfp-img" ).height());
                        });
                        jQuery(window).trigger('resize');

                        // slickremix trick to get the poster url from a tag we are clicking and pass it to the video player.
                        // We only want to load the poster if the size is mobile because tablets and desktops can/will play video automatically on popup
                        if (matchMedia('only screen and (max-device-width: 736px)').matches) {
                            var atagvideo = event.target.id;
                            var videoposter = jQuery('#'+atagvideo).data('poster');
                            var video = jQuery('.fts-fb-vid-popup video');
                            video.attr('poster', videoposter);
                            //	alert(videoposter);
                            console.log(videoposter);
                        }
                        // SLICKREMIX: MUST HAVE THIS IN PLACE TO BE ABLE TO ADD THE CLASS TO BODY SO WE CAN DO ACTIONS ON OUR CUSTOM PREV AND NEXT BUTTONS
                        // alert('added fts-using-arrows class on popup open')
                        jQuery("body").addClass("fts-using-arrows");


                    },
                    change: function() {
                        console.log('Content changed');
                        console.log(this.content); // Direct reference to your popup element
                        if(jQuery("body").hasClass("fts-using-arrows")) {



                            if(jQuery(".fts-popup-half .mfp-iframe-scaler")[0]){
                                jQuery( ".fts-popup-image-position" ).css("height", '591px');
                                //  alert('iframe-scaler');
                            }
                            else{
                                if(jQuery(".fts-popup-image-position" ).css("height") == "auto"){
                                    jQuery( ".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar" ).css("height", jQuery( ".mfp-img" ).height());
                                    alert('image');

                                }
                            }


                        }
                    },

                    imageLoadComplete: function() {
                        // fires when image in current popup finished loading
                        // avaiable since v0.9.0

                        if(jQuery(".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar" ).height() < jQuery( ".mfp-img" ).height()){
                            jQuery( ".fts-popup-image-position, .fts-popup-second-half .mfp-bottom-bar" ).css("height", jQuery( ".mfp-img" ).height());
                            // alert('image');
                        }
                        else {
                            jQuery( ".fts-popup-second-half .mfp-bottom-bar" ).css("height", jQuery( ".fts-popup-image-position" ).height());
                            //  alert('change');
                        }




                        jQuery(".mfp-title .ft-gallery-link-popup").on('click', function () {
                            //Share toolip function
                            jQuery('.mfp-title .ft-gallery-share-wrap').toggle();
                        });












                    },
                    markupParse: function(template, values, item) {
                        // Triggers each time when content of popup changes 
                        console.log('Parsing:', template, values, item);

                        // SLICKREMIX: MUST HAVE THIS IN PLACE TO BE ABLE TO CHECK WHAT KIND OF VIDEOS ARE BEING CLICKED ON
                        if(!jQuery("body").hasClass("fts-using-arrows")) {

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
                    afterClose: function() {
                        jQuery("body").removeClass("fts-using-arrows");
                        console.log('Popup is completely closed');
                    },
                },
                image: {
                    markup: '' +
                    '<div class="mfp-figure"><div class="mfp-close">X</div>'+
                    '<div class="fts-popup-wrap">' +
                    '    <div class="fts-popup-half ">' +
                    '               <button title="previous" type="button" id="fts-photo-prev" class="mfp-arrow mfp-arrow-left mfp-prevent-close"></button>' +
                    '           <div class="fts-popup-image-position" style="height:591px;">' +
                    '                   <span class="fts-position-helper"></span><div class="mfp-img"></div>' +
                    '       </div>' +
                    '               <button title="next" type="button" id="fts-photo-next" class="mfp-arrow mfp-arrow-right mfp-prevent-close"></button>' +
                    '    </div>'+
                    '<div class="fts-popup-second-half">' +
                    '<div class="mfp-bottom-bar">'+
                    '<div class="mfp-title"></div>' +
                    '<a class="fts-powered-by-text" href="https://slickremix.com" target="_blank">Powered by Feed Them Gallery</a>'+
                    '<div class="mfp-counter"></div>'+
                    '</div>' +
                    '</div>' +
                    '</div>'+
                    '</div>', // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button

                    tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',

                },
                iframe: {
                    markup: '' +
                    '<div class="mfp-figure"><div class="mfp-close">X</div>'+
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
                    '    </div>'+
                    '<div class="fts-popup-second-half">' +
                    '<div class="mfp-bottom-bar">'+
                    '<div class="mfp-title"></div>' +
                    '<a class="fts-powered-by-text" href="https://slickremix.com" target="_blank">Powered by Feed Them Gallery</a>'+
                    '<div class="mfp-counter"></div>'+
                    '</div>' +
                    '</div>' +
                    '</div>'+
                    '</div>', // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button

                    srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".  
                }
            });

        });
    }
//Return the function right away
    jQuery.fn.slickWordpressPopUpFunction();

    // TEST POPUP WITH ALERTS FOR DEBUGGIN
    // 'if(jQuery("body").hasClass("fts-video-iframe-choice")){alert("fts-video-choice not using arrows"); jQuery(".fts-iframe-popup-element").attr("src", "").hide(); } else if(!jQuery("body").hasClass("fts-using-arrows")){alert("fts-iframe-choice not using arrows"); jQuery(".fts-video-popup-element").attr("src", "").hide(); };  jQuery(".ft-gallery-popup video").click(function(){jQuery(this).trigger(this.paused ? this.paused ? "play" : "play" : "pause")});</script>' +


}); // end document ready