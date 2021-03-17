jQuery.fn.ftg_apply_quant_btn = function() {
	//s  jQuery( ".ft-wp-gallery .quantity input[type=button]" ).remove();

	setTimeout(function () {

		jQuery(".ft-wp-gallery .quantity input[type=number]").each(function () {
			var number = jQuery(this),
				max = parseFloat(number.attr('max')),
				min = parseFloat(number.attr('min')),
				step = parseInt(number.attr('step'), 10),
				newNum = jQuery(jQuery('<div />').append(number.clone(true)).html().replace('number', 'text')).insertAfter(number);
			number.remove();

			setTimeout(function () {
				if (newNum.next('.plus').length === 0) {
					var minus = jQuery('<input type="button" value="-" class="minus">').insertBefore(newNum),
						plus = jQuery('<input type="button" value="+" class="plus">').insertAfter(newNum);

					minus.on('click', function () {
						var the_val = parseInt(newNum.val(), 10) - step;
						the_val = the_val < 0 ? 0 : the_val;
						the_val = the_val < min ? min : the_val;
						newNum.val(the_val).trigger("change");
					});
					plus.on('click', function () {
						var the_val = parseInt(newNum.val(), 10) + step;
						the_val = the_val > max ? max : the_val;
						newNum.val(the_val).trigger("change");

					});
				}
			}, 10);
		});
	}, 150);
};
jQuery.fn.ftg_apply_quant_btn();


// wc_add_to_cart_params is required to continue, ensure the object exists
//  if ( typeof wc_add_to_cart_params === 'undefined' )
//      return false;

jQuery.fn.ftg_ajax_cart = function() {

	jQuery(document).on('click', '.ft-wp-gallery .single_add_to_cart_button', function (e) {

		e.preventDefault();

		$variation_form = jQuery(this).closest('.variations_form');
		var var_id = $variation_form.find('input[name=variation_id]').val();

		if(var_id){
			var product_id = $variation_form.find('input[name=product_id]').val();
			var quantity = $variation_form.find('input[name=quantity]').val();
		}
		else {
			// I NEED TO GET THE ID AND QUANTITY FOR THIS CART OPTION
			$simple_form = jQuery(this).closest('.ft-gallery-simple-cart .cart');
			var product_id = $simple_form.find('button[name=add-to-cart]').val();
			var quantity = $simple_form.find('input[name=quantity]').val();
			//  console.log(product_id)
		}

		//attributes = [];
		jQuery('.ajaxerrors').remove();
		var item = {},
			check = true;

		variations = $variation_form.find('select[name^=attribute]');

		/* Updated code to work with radio button - mantish - WC Variations Radio Buttons - 8manos */
		if (!variations.length) {
			variations = $variation_form.find('[name^=attribute]:checked');
		}

		/* Backup Code for getting input variable */
		if (!variations.length) {
			variations = $variation_form.find('input[name^=attribute]');
		}

		variations.each(function () {

			var $this = jQuery(this),
				attributeName = $this.attr('name'),
				attributevalue = $this.val(),
				index,
				attributeTaxName;

			$this.removeClass('error');

			if (attributevalue.length === 0) {
				index = attributeName.lastIndexOf('_');
				attributeTaxName = attributeName.substring(index + 1);

				$this
					//.css( 'border', '1px solid red' )
					.addClass('required error')
					//.addClass( 'barizi-class' )
					.before('<div class="ajaxerrors"><p>Please select ' + attributeTaxName + '</p></div>')

				check = false;
			} else {
				item[attributeName] = attributevalue;
			}

			// Easy to add some specific code for select but doesn't seem to be needed
			// if ( $this.is( 'select' ) ) {
			// } else {
			// }

		});

		if (!check) {
			return false;
		}

		//item = JSON.stringify(item);
		//alert(item);
		//return false;

		// AJAX add to cart request

		var $thisbutton = jQuery(this);

		if ($thisbutton.is('.ft-wp-gallery .single_add_to_cart_button')) {

			$thisbutton.removeClass('added');
			$thisbutton.addClass('loading');

			var var_id = $variation_form.find('input[name=variation_id]').val();

			var data = {
				action: 'woocommerce_add_to_cart_variable_rc',
				product_id: product_id,
				quantity: quantity,
				variation_id: var_id,
				variation: item
			};

			// Trigger event
			jQuery('body').trigger('adding_to_cart', [$thisbutton, data]);

			// Ajax action
			jQuery.post(wc_add_to_cart_params.ajax_url, data, function (response) {


				if (!response)
					return;

				console.log(response);
				console.log('made it');

				var this_page = window.location.toString();

				this_page = this_page.replace('add-to-cart', 'added-to-cart');

				if (response.error && response.product_url) {
					window.location = response.product_url;
					return;
				}

				if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {

					window.location = wc_add_to_cart_params.cart_url;
					return;

				} else {

					$thisbutton.removeClass('loading');

					var fragments = response.fragments;
					var cart_hash = response.cart_hash;

					// Block fragments class
					if (fragments) {
						jQuery.each(fragments, function (key) {
							jQuery(key).addClass('updating');
						});
					}


					// Block widgets and fragments
					jQuery('.shop_table.cart, .updating, .cart_totals').fadeTo('400', '0.6').block({
						message: null,
						overlayCSS: {
							opacity: 0.6
						}
					});

					// Changes button classes
					$thisbutton.addClass('added');

					// View cart text
					if (!wc_add_to_cart_params.is_cart && $thisbutton.hasClass('added') && $thisbutton.parent().parent().parent().parent().parent().find( '.ftg-completed-view-cart').length == 0 ) {

						$thisbutton.parent().parent().parent().parent().parent().find('.ft-gallery-simple-price .woocommerce-Price-amount, .ft-gallery-variations-text .woocommerce-Price-amount').addClass('added-to-cart-color');
						// add the view cart text/link
						$thisbutton.parent().parent().parent().parent().parent().find('.ft-gallery-simple-price, .ft-gallery-variations-text .woocommerce-Price-amount').after(' <span class="ftg-completed-view-cart"> / <a href="' + wc_add_to_cart_params.cart_url + '" class="ftg_added_to_cart wc-forward" title="' +
							wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a></span>');
					}

					// Replace fragments
					if (fragments) {
						jQuery.each(fragments, function (key, value) {
							jQuery(key).replaceWith(value);
						});
					}

					// Unblock
					jQuery('.widget_shopping_cart, .updating').stop(true).css('opacity', '1').unblock();

					// Cart page elements
					jQuery('.shop_table.cart').load(this_page + ' .shop_table.cart:eq(0) > *', function () {

						jQuery('.shop_table.cart').stop(true).css('opacity', '1').unblock();

						jQuery(document.body).trigger('cart_page_refreshed');
					});

					jQuery('.cart_totals').load(this_page + ' .cart_totals:eq(0) > *', function () {
						jQuery('.cart_totals').stop(true).css('opacity', '1').unblock();
					});

					// Trigger event so themes can refresh other areas
					jQuery(document.body).trigger('added_to_cart', [fragments, cart_hash, $thisbutton]);
				}
			});

			return false;

		} else {
			return true;
		}

	});

};
jQuery.fn.ftg_ajax_cart();
