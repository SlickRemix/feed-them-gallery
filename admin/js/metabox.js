function plu_show_thumbs(e,t){for(var a=jQuery,i=a(".thumb"),l="list_item_"+t,r=a("#"+e).val(),o=r.split(","),n=0;n<o.length;n++)if(console.log("#"+t+":visible"),console.log(o),o[n]){var s=a('<li class="thumb thumb-new" id="'+l+'"><img src="'+o[n]+'" alt="" /><div class="thumbi"><a id="thumbremovelink'+e+n+'" href="#" style="display: none">Delete</a></div> <div class="clear"></div></li>');a("#img1plupload-thumbs").prepend(s),s.find("a").click(function(){var e=a(this).attr("id").replace("thumbremovelink"+l,"");e=parseInt(e);var t=[];r=a("#"+l).val(),o=r.split(",");for(var i=0;i<o.length;i++)i!=e&&(t[t.length]=o[i]);return!1})}jQuery("#img1plupload-thumbs").sortable({items:"li",opacity:1,cursor:"move",update:function(){var e=jQuery(this).data("post-id"),t=jQuery(this).sortable("serialize")+"&action=list_update_order";jQuery.post(ajaxurl,t,function(t){console.log(t),console.log(e)})}}),i.disableSelection()}jQuery(document).ready(function(e){e("a#dgd_library_button").click(function(e){var t=wp.media.controller.Library.extend({defaults:_.defaults({id:"insert-image",title:"Insert Image Url",allowLocalEdits:!0,displaySettings:!0,displayUserSettings:!0,multiple:!0,type:"image"},wp.media.controller.Library.prototype.defaults)}),a=wp.media({button:{text:"Select"},state:"insert-image",states:[new t]});a.on("close",function(){a.state("insert-image").get("selection").length}),a.on("select",function(){var e=a.state("insert-image"),t=e.get("selection");console.log(t),t&&t.each(function(t){var a,i,l=e.display(t).toJSON(),r=t.toJSON(),o=r.caption;if(wp.media.view.settings.captions||delete r.caption,l=wp.media.string.props(l,r),a={id:r.id,post_content:r.description,post_excerpt:o},l.linkUrl&&(a.url=l.linkUrl),"image"===r.type?(i=wp.media.string.image(l),_.each({align:"align",size:"image-size",alt:"image_alt"},function(e,t){l[t]&&(a[e]=l[t])})):"video"===r.type?i=wp.media.string.video(l,r):"audio"===r.type?i=wp.media.string.audio(l,r):(i=wp.media.string.link(l),a.post_title=l.title),t.attributes.nonce=wp.media.view.settings.nonce.sendToEditor,t.attributes.attachment=a,t.attributes.html=i,t.attributes.post_id=wp.media.view.settings.post.id,t.attributes.sizes.ft_gallery_thumb)var n=t.attributes.sizes.ft_gallery_thumb.url;else n=t.attributes.sizes.thumbnail.url;jQuery("#img1plupload-thumbs").prepend('<li class="thumb" id="list_item_'+t.id+'" data-image-id="'+t.id+'"><img src="'+n+'" alt="" /><div class="clear"></div></li>'),jQuery.ajax({data:{action:"ft_gallery_edit_image_ajax",id:t.id,postID:jQuery("#img1plupload-thumbs").attr("data-post-id"),nonce:"attach_image"},type:"post",url:ssAjax.ajaxurl,success:function(e){console.log("Well Done and got this from sever: "+e)},error:function(){alert("Error, please contact us at https://slickremix.com/ for help.")}})})}),a.on("open",function(){var e=a.state("insert-image").get("selection");e.each(function(t){var a=wp.media.attachment(t.attributes.id);a.fetch(),e.remove(a?[a]:[])})}),a.open()}),e("input#ft-watermark-image").click(function(e){e.preventDefault();var t=wp.media({id:"dgd_featured_image",title:dgd_strings.panel.title,multiple:!1,library:{type:"image"},button:{text:dgd_strings.panel.button}});t.on("select",function(){var e=t.state().get("selection").first().toJSON();return jQuery("#ft_watermark_image_input").val(e.url),jQuery("#ft_watermark_image_id").val(e.id),jQuery(".ft-global-option-wrap-ft-watermark-image").html('<img src="'+e.url+'" class="ft-watermark-image-thumb" /><br/>'),jQuery.ajax({data:{action:"ft_gallery_update_image_ajax",id:e.id,nonce:e.id,ft_gallery_img_remove:"true"},type:"POST",url:ssAjax.ajaxurl,beforeSend:function(){},success:function(e){console.log("Well Done and got this from sever: "+e)},error:function(){alert("Error, please contact us at http://slickremix.com/support-forum for help.")}}),!1}),t.open()}),""!==jQuery("#ft-watermark-image").val()&&jQuery(".ft-global-option-wrap-ft-watermark-image").html('<img src="'+jQuery("#ft_watermark_image_input").val()+'" class="ft-watermark-image-thumb ft-watermark-existing" /><br/>'),e("a.wp-post-thumbnail").live("click",function(e){parent.tb_remove(),parent.location.reload(1)}),e("a#insert-media-button").live("click",function(){if("undefined"!=typeof wp){var t=e(".wp-media-buttons:eq(0) .add_media").attr("data-editor"),a=wp.media.editor.get(t);(a=void 0!==a?a:wp.media.editor.add(t))&&a.on("select",function(){"featured-image"===a.state().id&&doFetchFeaturedImage()})}})}),jQuery.fn.exists=function(){return jQuery(this).length>0},jQuery(document).ready(function(e){if(e(".plupload-upload-uic").exists()){var t=!1;e(".plupload-upload-uic").each(function(){var a=e(this),i=a.attr("id").replace("plupload-upload-ui","");if(plu_show_thumbs(i),(t=JSON.parse(JSON.stringify(base_plupload_config))).browse_button=i+t.browse_button,t.container=i+t.container,t.drop_element=t.drop_element,t.file_data_name=i+t.file_data_name,t.multipart_params.imgid=i,t.multipart_params._ajax_nonce=a.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu",""),a.hasClass("plupload-upload-uic-multiple")&&(t.multi_selection=!0),a.find(".plupload-resize").exists()){var l=parseInt(a.find(".plupload-width").attr("id").replace("plupload-width","")),r=parseInt(a.find(".plupload-height").attr("id").replace("plupload-height",""));t.resize={width:l,height:r,quality:100}}var o=new plupload.Uploader(t);o.bind("Init",function(t){var a=e("#plupload-upload-ui");t.features.dragdrop?(a.addClass("drag-drop"),e("#drag-drop-area").bind("dragover.wp-uploader",function(){a.addClass("drag-over")}).bind("dragleave.wp-uploader, drop.wp-uploader",function(){a.removeClass("drag-over")})):(a.removeClass("drag-drop"),e("#drag-drop-area").unbind(".wp-uploader"))}),o.init(),o.bind("FilesAdded",function(t,i){e.each(i,function(e,t){a.find(".filelist").append('<div class="file" id="'+t.id+'"><b>'+t.name+'</b> (<span class="ftg-file-size">'+plupload.formatSize(0)+"</span>/"+plupload.formatSize(t.size)+')  <span class="ftg-finishing"></span><div class="fileprogress"></div></div>')}),t.refresh(),t.start()}),o.bind("UploadProgress",function(t,a){e("#"+a.id+" .fileprogress").width(a.percent+"%"),e("#"+a.id+" span.ftg-file-size").html(plupload.formatSize(parseInt(a.size*a.percent/100))),setTimeout(function(){100==a.percent&&e("#"+a.id+" span.ftg-finishing").html("<strong>Finishing up, please be patient</strong>")},5e3)}),o.bind("FileUploaded",function(t,l,r){if(e("#"+l.id).fadeOut(),e("#"+l.id).addClass("ftg-upload-complete"),r=JSON.parse(r.response),console.log(r.url),a.hasClass("plupload-upload-uic-multiple")){var o=e.trim(e("#"+i).val());o=r.url,e("#"+i).val(o)}else e("#"+i).val(r.url+"");plu_show_thumbs(i,r.id);var n=jQuery("select#ft_gallery_image_to_woo_model_prod").val(),s=jQuery("select#ft_gallery_landscape_to_woo_model_prod").val(),d=jQuery("select#ft_gallery_square_to_woo_model_prod").val(),u=jQuery("select#ft_gallery_portrait_to_woo_model_prod").val(),p=jQuery("#ft_gallery_smart_image_orient_prod").is(":checked");console.log(n),(jQuery("#uploaderSection").hasClass("ftg-auto-create-product-on-upload")||jQuery("#ft_gallery_auto_image_woo_prod").is(":checked"))&&(n||s&&d&&u&&p?(ft_gallery_image_to_woo_on_upload(r.id,jQuery("#img1plupload-thumbs").attr("data-post-id")),jQuery("#ftg-tab-content1 .ft-gallery-notice").removeClass("error"),jQuery("#ftg-tab-content1 .ft-gallery-notice").removeClass("ftg-block"),jQuery("#ftg-tab-content1 .ft-gallery-notice").html("")):(jQuery("#ftg-tab-content1 .ft-gallery-notice").addClass("error"),jQuery("#ftg-tab-content1 .ft-gallery-notice").addClass("ftg-block"),jQuery("#ftg-tab-content1 .ft-gallery-notice").html(ftg_woo.must_have_option_selected_to_create_products+'<div class="ft-gallery-notice-close"></div>'),console.log("error")))})})}});