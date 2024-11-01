(function ($) {
	var gallerySwiper, stepNavSwiper, optionNavSwiper, noticeTimeout;

	var pricetotal     = {};
	var altpricetotal  = {};
	var lastImages     = {};
	var zIndex         = 0;
	var minStep        = 1;
	var activeStep     = 1;
	var visitedStep    = 1;
	var maxStep        = 1;
	var shared_config  = {};
	var wishlist_items = [];
	var repeater_items = {};
	var sgg_canvasses  = {};

	// Start at top.
	window.onbeforeunload = function () {
		window.scrollTo(0, 0);
	}

	// Fix: Added non-passive event listener to a scroll-blocking 'touchstart' event.
	// if (typeof EventTarget !== "undefined") {
    //     let func = EventTarget.prototype.addEventListener;
    //     EventTarget.prototype.addEventListener = function (type, fn, capture) {
    //         this.func = func;
    //         if(typeof capture !== "boolean"){
    //             capture = capture || {};
    //             capture.passive = false;
    //         }
    //         this.func(type, fn, capture);
    //     };
    // };

	$(document).ready(function () {

		if ( typeof SUMMARY_SHOW_NOTES === 'undefined' ) {
			var SUMMARY_SHOW_NOTES = false;
		}

		if ( ! SHOW_PRODUCT_PRICE && $('.option-group.intro .price').length ) {
			$('.option-group.intro .price').remove();
		}

		var queryString = window.location.search;
		if ( queryString && ( queryString.includes('configuration=') || queryString.includes('staggs_summary=') ) && ! queryString.includes('preview=true') ) {
			var optionsPart = queryString.split('&')[0];
			if ( queryString.includes('configuration=') ) { 
				var filename = optionsPart.replace('?configuration=', '');
			} else {
				var filename = optionsPart.replace('?staggs_summary=', '');
			}

			$.ajax({
				type: 'post',
				url: AJAX_URL,
				async: false,
				cache: false,
				data: {
					action: 'get_configuration_file',
					filename: filename,
				},
				success: function (data) {
					var configuration = JSON.parse(data);
					shared_config = configuration;
				}
			});
		}

		if ( queryString && queryString.includes('options=') && ! queryString.includes('preview=true') ) {
			var optionsPart = queryString.split('&')[0];

			if ( optionsPart ) {
				optionsPart = optionsPart.replace('?options=', '');
				var options = decodeURIComponent(optionsPart);
				options = options.replaceAll('\'', '"');

				shared_config = {
					values: JSON.parse(options)
				}

				if ( queryString.includes('progress=') ) {
					var progressPart = queryString.split('&')[1];
					if ( progressPart ) {
						progressPart = progressPart.replace('progress=', '');
						var progress = decodeURIComponent(progressPart);
						progress = progress.replaceAll('\'', '"');
						shared_config.progress =  JSON.parse(progress);
					}
				}
			}
		}

		if ( queryString && queryString.includes('staggs_summary=') && ! queryString.includes('preview=true') ) {
			$('.staggs-configurator-main').addClass('summary-visible');

			if ( $('.staggs-summary-template').offset().top > 300 ) {
				// Scroll into view.
				$('html,body').scrollTop(
					$('.staggs-summary-template').offset().top
				);
			}
		}

		// Add notices wrapper.
		if ( ! DISABLE_MESSAGE_WRAPPER && ! $('.staggs-message-wrapper').length ) {
			$('body').append('<div class="staggs-message-wrapper"><div class="woocommerce-notices-wrapper"></div></div>');
		}

		if (!gallerySwiper && $('.staggs-view-gallery.swiper').length) {
			// Activate Swiper.
			$('.view-nav-buttons button[data-key=0]').addClass('selected');

			gallerySwiper = new Swiper('.staggs-view-gallery.swiper', {
				slidePerView: 1,
				allowTouchMove: true,
				breakpoints:{
					768: {
						allowTouchMove: false,
					},
				},
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
			});
			
			gallerySwiper.on('slideChange', function () {
				if ($('.staggs-view-gallery').data('backgrounds')) {
					var backgrounds = $('.staggs-view-gallery').data('backgrounds').split('|');
					var backgroundUrl = backgrounds[gallerySwiper.activeIndex] ? backgrounds[gallerySwiper.activeIndex] : backgrounds[backgrounds.length - 1];

					$('.product-view-inner').css('background-image', 'url(' + backgroundUrl + ')');
				}

				$('.view-nav-buttons button').removeClass('selected');
				$('.view-nav-buttons button[data-key=' + gallerySwiper.activeIndex + ']').addClass('selected');
			});

			if ($('.staggs-view-gallery').data('backgrounds')) {
				var backgrounds = $('.staggs-view-gallery').data('backgrounds').split('|');
				var backgroundUrl = backgrounds[gallerySwiper.activeIndex] ? backgrounds[gallerySwiper.activeIndex] : backgrounds[backgrounds.length - 1];

				$('.product-view-inner').css('background-image', 'url(' + backgroundUrl + ')');
			}
		} else {
			$('.staggs-product-view .swiper-button-prev, .staggs-product-view .swiper-button-next').addClass('disabled');
		}

		if ( $('.staggs-configurator-steps-nav').length ) {
			stepNavSwiper = new Swiper('.staggs-configurator-steps-nav', {
				slidesPerView: 'auto',
				initialSlide: 0,
				spaceBetween: 10,
			});

			var slidesWidth = 0;
			$('.staggs-configurator-steps-nav').find('.swiper-slide').each(function(index,slide){
				slidesWidth += $(slide).width();
			});

			if ( slidesWidth < $('.staggs-configurator-steps-nav').width() ) {
				$('.staggs-configurator-steps-nav').find('.swiper-wrapper').addClass('centered');
			}
		}

		if ( $('.staggs-configurator-main .swiper-options-nav').length ) {
			optionNavSwiper = new Swiper('.swiper-options-nav', {
				slidesPerView: 'auto',
				initialSlide: 0,
				spaceBetween: 10,
			});
		}

		// Set initial values.
		if ( ! $('.option-group.total .totals-list').length && SHOW_PRODUCT_PRICE ) {
			$('.option-group.total .cart button').append('<span id="totalprice"></span>');
			$('.option-group.total .cart button').addClass('inline_price');
		}

		if ( ! $('.staggs-configurator-bottom-bar .totals-list').length && SHOW_PRODUCT_PRICE ) {
			$('.staggs-configurator-bottom-bar .cart button').append('<span id="totalprice"></span>');
			$('.staggs-configurator-bottom-bar .cart button').addClass('inline_price');
		}

		if ( ! $('.staggs-message-wrapper .woocommerce-notices-wrapper').length ) {
			$('.staggs-message-wrapper').append('<div class="woocommerce-notices-wrapper"></div>');
		}

		if ( ! $('form.cart .quantity').hasClass('hidden') && $('form.cart .quantity input[type=hidden]').length ) {
			$('form.cart .quantity').addClass('hidden');
		}

		if ( $('.option-group-spacer').length ) {
			$('.option-group-spacer').height( $('.option-group.total').height() + 40 );
		}

		if ($('.staggs-view-gallery').data('backgrounds')) {
			$('.staggs-configurator-main').addClass('has-bg');
		}

		/**
		 * Gallery height
		 */
		// Initial height.
		var initialHeight = $('html').width();
		if ( $(window).width() > 767 && $(window).width() < 992 && $('.staggs-product-view').height() > 10 ) {
			initialHeight = $('.staggs-product-view').height();
		}

		var maxHeight = $(window).height() / 2;
		if ( initialHeight > maxHeight ) {
			initialHeight = maxHeight;
		}

		var marginHeight = initialHeight;
		if ( $('.staggs-configurator-topbar').length ) {
			marginHeight += $('.staggs-configurator-topbar').get(0).clientHeight;
		}

		if ( $('.staggs-configurator-steps-nav').length && ! $('.staggs-stepper').length ) {
			marginHeight += $('.staggs-configurator-steps-nav').get(0).clientHeight;
		}

		if ( MOBILE_HEADER_HEIGHT ) {
			marginHeight += parseInt( MOBILE_HEADER_HEIGHT );
		}

		if ( $(window).width() < 992 
			&& $('.staggs-configurator').length 
			&& ! $('.staggs-configurator.has-theme-header').length 
			&& ! $('.staggs-configurator-popup.popup-horizontal').length 
			&& ! $('.staggs-configurator-contained').length
			&& ! $('.staggs-configurator-inline').length 
			) {

			// No inline items. Calculate fixed positions.

			if ( $('#staggs-preview .swiper-slide').length ) {

				$('#staggs-preview .swiper-slide').css('height', initialHeight);
				$('#staggs-preview .swiper-slide').css('maxHeight', initialHeight);

			} else if ( $('.staggs-view-gallery model-viewer').length ) {

				$('.staggs-product-view').height( initialHeight );

			}

			if ( $('.staggs-configurator-steps-nav').length && ! $('.staggs-stepper').length ) {
				$('.staggs-product-view').css( 'top', $('.staggs-configurator-steps-nav').get(0).clientHeight );
			}

			if ( ! $('.staggs-configurator-popup').length && ! $('.staggs-product-view.mobile-inline').length ) {
				$('.option-group-wrapper').css('marginTop', marginHeight);
			}

			if ( $('.staggs-configurator-bottom-bar-spacer').length ) {
				$('.staggs-configurator-bottom-bar-spacer').height( $('.staggs-configurator-bottom-bar').get(0).clientHeight );
			}
		}

		if ( ( 
			$('.staggs-configurator-inline .gallery-sticky .staggs-product-view').length 
			|| $('.staggs-configurator.has-theme-header .gallery-sticky .staggs-product-view').length 
			|| $('.staggs-contained.gallery-sticky .staggs-product-view').length 
			) && $(window).width() < 992 ) {
			
			// Inline configurator sticky mobile.

			if ( $('.staggs-configurator-inline .gallery-sticky .staggs-product-view').length ) {

				// Shortcode gallery

				var $wrapper = $('.staggs-configurator-inline');
				var $element = $('.staggs-configurator-inline .gallery-sticky .staggs-product-view');
				var elementPosition = $element.offset();
				var heightDiff = 0;

			} else {

				// Full page gallery with header/footer enabled.

				if ( $('.staggs-configurator.has-theme-header .gallery-sticky .staggs-product-view').length ) {
					var $wrapper = $('.staggs-configurator.has-theme-header');
					var $element = $('.gallery-sticky .staggs-product-view');
				} else {
					var $wrapper = $('.staggs-contained');
					var $element = $('.staggs-contained.gallery-sticky .staggs-product-view');
				}

				var elementPosition = $element.offset();
				var heightDiff = 0;
			}

			if ( $('.staggs-configurator-steps-nav').length && ! $('.staggs-stepper').length ) {
				heightDiff = $('.staggs-configurator-steps-nav').get(0).clientHeight;
				$('.staggs-product-view').css( 'top', heightDiff );
			}

			if ( $('#staggs-preview .swiper-slide').length ) {
				// Images
				initialHeight = $element.height();
				marginHeight = $element.height();
			}

			var maxHeight = ($(window).height() * 0.4);
			if ( initialHeight > maxHeight ) {
				initialHeight = maxHeight;
				marginHeight = maxHeight;
			}

			if ( MOBILE_HEADER_HEIGHT ) {
				marginHeight += parseInt( MOBILE_HEADER_HEIGHT );
			}

			if ( initialHeight > 0 ) {
				$element.height(initialHeight);
				$element.css('position', 'absolute');
			}

			if ( marginHeight > initialHeight ) {
				heightDiff = marginHeight - initialHeight;
				$element.css('top', heightDiff + 'px');
			}

			if ( $('.staggs-contained model-viewer').length ) {
				$('.staggs-contained model-viewer').height( $('.staggs-contained .staggs-product-view').height() );
			}
	
			$element.parents('.staggs-configurator-main').find('.staggs-product-options').prepend('<div class="gallery-spacer" style="height:' + marginHeight + 'px"></div>');

			if ( MOBILE_HEADER_HEIGHT ) {
				$element.css('position', 'fixed');
				$element.css('top', heightDiff + 'px');
				$element.removeClass('stick');
			}

			$(window).scroll(function(){
				if( ( $(window).scrollTop() + 30 >= elementPosition.top ) && ( $(window).scrollTop() <= ( ( $wrapper.height() - initialHeight ) + $wrapper.offset().top ) ) ) {
					$element.css('position', 'fixed');
					$element.css('top', MOBILE_HEADER_HEIGHT + 'px');
					$element.addClass('stick');
				} else {
					$element.css('position', 'absolute');
					$element.css('top', heightDiff + 'px');
					$element.removeClass('stick');
				}
			});
		}

		// Default options setting is disabled. Validate all conditionals on load.
		if ( DISABLE_DEFAULTS ) {
			_validateConditionalSteps();
		}

		if ( $('.staggs-configurator-topbar > *').length === 1 && ! $('.staggs-configurator-topbar #close-popup').length ) {
			$('.staggs-configurator-topbar').addClass('single-item');
		}
 
		if ( $('.option-group-options.tabs').length ) {
			$('.option-group-options.tabs').each(function(index,tab) {
				$(tab).find('.tab-list a').each(function(index,tablink) {
					var tab_ids = $(tablink).data('tabs');

					$(tablink).removeClass('active');
					$(tablink).removeClass('hidden');

					if ( tab_ids.toString().includes(',') ) {
						tab_ids = tab_ids.split(',');
						tab_ids.forEach(function(tab_id,index) {
							$('div#option-group-' + tab_id).addClass('tab-hidden');
						});
					} else {
						$('div#option-group-' + tab_ids).addClass('tab-hidden');
					}
				});

				$(tab).find('.tab-list li:first-of-type a').trigger('click');
			});
		}

		if ( $('.option-group-options.select').length && $(window).width() > 991 ) {
			$('.staggs-product-options .option-group-options.select select').each(function(index,selectmenu) {
				var $selectmenu = $(selectmenu).selectmenu( _getSelectMenuOptions() );
				$selectmenu.data("ui-selectmenu")._renderItem = _selectMenuItemRenderer;
				$selectmenu.data("ui-selectmenu")._renderButtonItem = _selectMenuButtonRenderer;
			});

			if ( $('.staggs-configurator-main.border-rounded option-group-options.select').length || $('.option-group.border-rounded .option-group-options.select').length ) {
				$('.ui-selectmenu-menu').addClass('border-rounded');
			}
			if ( $('.staggs-configurator-main.border-pill option-group-options.select').length || $('.option-group.border-pill .option-group-options.select').length ) {
				$('.ui-selectmenu-menu').addClass('border-pill');
			}

			if ( $('.staggs-floating .option-group-step.collapsible .ui-selectmenu-button').length ) {
				$('.staggs-floating .option-group-step.collapsible .ui-selectmenu-button').each(function(index,button) {
					var id = $(button).attr('aria-owns');
					$('.ui-selectmenu-menu #' + id).addClass('floating-collapsible');
				});
			}
		}

		if ( $('.option-group-options input.datepicker-input').length ) {
			$('.option-group-options input.datepicker-input').each(function(index,datepicker) {
				var options = {};
				if ( $(datepicker).data('date-format') ) {
					options.dateFormat = $(datepicker).data('date-format');
				}
				if ( $(datepicker).data('date-min') ) {
					options.minDate = $(datepicker).data('date-min');
				}
				if ( $(datepicker).data('date-max') ) {
					options.maxDate = $(datepicker).data('date-max');
				}
				
				if ( $(datepicker).data('inline') ) {
					$(datepicker).next('.datepicker-input-inline').datepicker(options);
					$(datepicker).next('.datepicker-input-inline').change(function(){
						$(this).parent('.input-field').find('input[type=text]').val($(this).val());
						$(this).parent('.input-field').find('input[type=text]').trigger('input');
					});
				} else {
					$(datepicker).datepicker(options).on("change", function() {
						$(this).trigger('input');
					});
				}
	
				if ( $('.staggs-configurator-main.border-rounded').length || $('.option-group.border-rounded .option-group-options.text-input').length ) {
					$('.ui-datepicker.ui-widget').addClass('border-rounded');
				}
				if ( $('.staggs-configurator-main.border-pill').length || $('.option-group.border-pill .option-group-options.text-input').length ) {
					$('.ui-datepicker.ui-widget').addClass('border-pill');
				}
			});
		}

		if ( $('.option-group-options input[data-preview-index]').length ) {
			$('.option-group-options input[data-preview-index]').each(function(index,input) {
				if ( 'file' !== $(input).attr('type') ) {
					_syncTextInputs($(input));
				}
			});
		}

		if ( $('.option-group-options textarea[data-preview-index]').length ) {
			$('.option-group-options textarea[data-preview-index]').each(function(index,input) {
				_syncTextInputs($(input));
			});
		}

		if ( $('.option-group-options input[data-type=range]').length ) {
			$('.option-group-options input[data-type=range]').each(function(item,range) {
				_initRangeSlider( $(range) );
			});
		}

		if ( $('.option-group .option-group-options').length ) {
			$('.option-group .option-group-options').each(function (index, item) {
				setActiveStepOptions(item);
			});
		}

		if ( $('.staggs-repeater-main').length ) {
			// Load in url options.
			var urlOptions = [];
			if ( queryString && ( queryString.includes('configuration=') || queryString.includes('options=') || queryString.includes('staggs_summary=') ) && ! queryString.includes('preview=true') ) {
				urlOptions = shared_config.values;
			}

			$('.staggs-repeater-main').each(function(index,repeater) {
				_initializeRepeater( $(repeater), urlOptions );
			});
		}

		_loadActiveStep();
		_loadConfiguratorPopup();
		_setTotals();

		$('.staggs-product-options').removeClass('loading');

		// Fetch wishlist if user has one
		if ( $('.preview-action.wishlist-toggle').length && $('body').hasClass('logged-in') ) {
			// User logged in and admin bar
			$.ajax({
				type: 'get',
				url: AJAX_URL,
				data: {
					action: 'staggs_fetch_customer_wishlist'
				},
				success: function (data) {
					if (data.length > 10) {
						wishlist_items = JSON.parse(data);
					}
				}
			});
		}

	});

	function _initializeRepeater( $repeater_main, urlOptions = [] ) {
		var repeaterValues = [];
		var repeaterKey = $repeater_main.find('[data-repeater-list]').data('repeater-list');

		if ( urlOptions.length ) {
			urlOptions.forEach(function(option,key) {
				if ( option.id && option.id.indexOf(repeaterKey) !== -1 ) {
					var optionParts = option.id.split('[');
					var index = optionParts[1].replace(']', '');
					var optionName = optionParts[2].replace(']', '');
					var optionValue = urldecode(option.value);

					if ( ! repeaterValues[index] ) {
						repeaterValues[index] = {[optionName]: optionValue};
					} else {
						repeaterValues[index][optionName] = optionValue;
					}
				}
			});
		}

		var $repeater = $repeater_main.repeater({
			initEmpty: true,
			min: 0,
			max: 1,
			defaultValues: {},
			repeaters: [], // nested repeaters
			show: function () {
				$repeater_main.find('.repeater-empty-note').hide();

				$(this).parents('[data-repeater-list]').find('[data-repeater-item]').each(function(index,item) {
					$(item).find('.index').text(index + 1);
				});

				activateConditionalStepFields( $(this) );

				$(this).find('.option-group-options').each(function (index, item) {
					var itemName = '';
					if ( $(item).find('input').length ) {
						itemName = $(item).find('input').attr('name');
					} else {
						itemName = $(item).find('select').attr('name');
					}
					setActiveStepOptions( $(item), itemName, 'id' );
				});

				$(this).slideDown();
			},
			hide: function (deleteElement) {
				if(confirm('Are you sure you want to delete this item?')) {
					$(this).slideUp(deleteElement, function() {

						if ( $('.staggs-view-gallery .swiper-slide').length ) {

							// Clear images from previews.
							$(this).find('.option-group').each(function(groupIndex,group) {
								var group_id = $(group).find('input').attr('name');
								if ( ! group_id ) {
									group_id = $(group).find('select').attr('name');
								}
								
								group_id = group_id.replaceAll('[', '_').replaceAll(']', '');
								if ( $(group).find('.option-group-options').hasClass( 'tickboxes' ) ) {
									$(group).find('input').each(function(index,input) {
										var input_id = $(input).attr('id');
										input_id = input_id.replaceAll('[', '_').replaceAll(']', '');
	
										$('.staggs-view-gallery .swiper-slide').each(function(key, slide) {
											if ( $(slide).find('img[id="preview_' + key + '_' + input_id + '"]').length ) {
												$(slide).find('img[id="preview_' + key + '_' + input_id + '"]').remove();
											}
										});
									});
								} else {
									$('.staggs-view-gallery .swiper-slide').each(function(key, slide) {
										if ( $(slide).find('img[id="preview_' + key + '_' + group_id + '"]').length ) {
											$(slide).find('img[id="preview_' + key + '_' + group_id + '"]').remove();
										}
									});
								}
							});
						}
						
						$(deleteElement).remove();

						if ( $(this).parents('[data-repeater-list]').find('[data-repeater-item]').length == 1 ) {
							$('.repeater-empty-note').show();
						} else {
							$(this).parents('[data-repeater-list]').find('[data-repeater-item]:visible').each(function(index,item) {
								$(item).find('.index').text(index + 1);
							});
						}

						if ( $('.staggs-summary-widget').length ) {
							setTimeout(function(){
								_updateSummary();
							}, 200);
						}
					});
				}
			},
		});

		if ( repeaterValues.length > 0 ) {
			$repeater.setList(repeaterValues);

			$repeater_main.find('.repeater-empty-note').hide();

			$repeater.find('.option-group-options').each((index, group) => {
				setActiveStepOptions( $(group) );
			});
		}

		repeater_items[repeaterKey] = $repeater;
	}

	function _emptyRepeater($repeater_main) {
		var repeaterKey = $repeater_main.find('[data-repeater-list]').data('repeater-list');
		var $repeater = repeater_items[repeaterKey];
		if ( $repeater ) {
			$repeater.setList([]);
		}
		$repeater_main.find('.repeater-empty-note').show();
	}

	// Collapsible groups.
	$(document).on('click', '.option-group-step.collapsible .option-group-step-title', function (e) {
		e.preventDefault();

		var groupStepId;
		if ( $(this).parents('.staggs-configurator-bottom-bar') ) {
			var groupStepId = $(this).parent('.option-group-step').data('step-group-id');

			if ( ! $(this).parent('.option-group-step').hasClass('collapsed') ) {
				$(this).parent('.option-group-step').addClass('collapsed');
				$('.staggs-configurator-bottom-bar .option-group-step[data-step-group-id="' + groupStepId + '"]').addClass('collapsed');
			} else {
				$('.staggs-configurator-bottom-bar .option-group-step.collapsible').addClass('collapsed');
				$(this).parent('.option-group-step').removeClass('collapsed');
				$('.staggs-configurator-bottom-bar .option-group-step[data-step-group-id="' + groupStepId + '"]').removeClass('collapsed');
			}
		} else {
			$(this).parent('.option-group-step').toggleClass('collapsed');
		}

		if ( ! $(this).parent('.option-group-step').hasClass('collapsed') ) {
			// Set image for active slide.
			if ( groupStepId ) {
				$('.option-group-step[data-step-group-id="' + groupStepId + '"]').find('.option-group[data-slide-preview]:not(.hidden)').each(function (index, item) {
					var slidePreviewKey = $(this).data('slide-preview');
	
					if (!gallerySwiper) {
						return;
					}
	
					_setActiveImageSlide(slidePreviewKey);
				});
			} else {
				$(this).parent('.option-group-step').find('.option-group[data-slide-preview]:not(.hidden)').each(function (index, item) {
					var slidePreviewKey = $(this).data('slide-preview');
	
					if (!gallerySwiper) {
						return;
					}
	
					_setActiveImageSlide(slidePreviewKey);
				});
			}
		}
	});

	// Collapsible attributes
	$(document).on('click', '.option-group.collapsible .option-group-header', function (e) {
		if ( $(this).parents('.option-group').hasClass('collapsed') ) {
			$(this).parents('.option-group').removeClass('collapsed').addClass('open');
		} else {
			$(this).parents('.option-group').removeClass('open').addClass('collapsed');
		}
	});

	// Tab links
	$(document).on('click', '.option-group-options.tabs a', function(e) {
		e.preventDefault();

		$(this).parents('.tab-list').find('a').each(function(index,tablink) {
			var tab_ids = $(tablink).data('tabs');

			$(tablink).removeClass('active');
			$(tablink).removeClass('hidden');

			if ( tab_ids.toString().includes(',') ) {
				tab_ids = tab_ids.split(',');
				tab_ids.forEach(function(tab_id,index) {
					if ( $('div#option-group-' + tab_id).length ) {
						$('div#option-group-' + tab_id).addClass('tab-hidden');
					}
				});
			} else {
				if ( $('div#option-group-' + tab_ids).length ) {
					$('div#option-group-' + tab_ids).addClass('tab-hidden');
				} else {
					$(tablink).addClass('hidden');
				}
			}
		});

		var thisId = $(this).data('tabs');
		if ( thisId.toString().includes(',') ) {
			thisId = thisId.split(',');
			thisId.forEach(function(thisTabId,index) {
				$('div#option-group-' + thisTabId).removeClass('tab-hidden');
			});
		} else {
			$('div#option-group-' + thisId).removeClass('tab-hidden');
		}

		$(this).addClass('active');

		if ( $(this).data('slide-preview') ) {
			var slidePreviewKey = $(this).data('slide-preview');
			_setActiveImageSlide(slidePreviewKey);
		}
	});

	// Steps
	$('.configurator-step-link').on('click', function (e) {
		e.preventDefault();

		var clickedStep = $(this).data('step-number');

		if ( clickedStep <= visitedStep ) {
			activeStep = clickedStep;
			_setActiveStep();
		}
	});

	// Product Options Prev/Next stepper.
	$('.staggs-step-prev-button').on('click', function (e) {
		e.preventDefault();

		if ( activeStep > minStep ) {
			activeStep--;
		}

		_setActiveStep();
	});

	$('.staggs-step-next-button').on('click', function (e) {
		e.preventDefault();
		
		var validStep = _validateStepFields();
		if ( activeStep < maxStep && validStep ) {
			$('.configurator-step-link[data-step-number=' + activeStep + ']').addClass('visited');
			activeStep++;
			visitedStep++;
		}

		_setActiveStep();
	});

	// Gallery fullscreen
	$(document).on('click', '.staggs-product-view button.fullscreen', function (e) {
		e.preventDefault();
		toggleFullScreen($('.staggs-product-view').get(0));
	});

	// Gallery capture
	$(document).on('click', '.staggs-product-view button.capture-image', function (e) {
		e.preventDefault();
		_captureAndDownloadImage();
	});

	// Panel open / popup click.
    $(document).mouseup(function (e) {
        var container = $(".option-group-panel");

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            $(".option-group-panel").removeClass('shown');
            $('body').removeClass('panel-shown');
        }
    });

	function _setActiveStep() {
		if ( ! $('.staggs-step-prev-button').length && ! $('.staggs-step-next-button').length ) {
			if ( $('.option-group.total[data-show-step="final"]').length ) {
				$('.option-group.total[data-show-step="final"]').removeClass('hidden');
			}
			return;
		}

		if ( activeStep >= minStep && activeStep <= maxStep ) {
			$('.configurator-step-link').removeClass('active');
			$('.option-group-step').addClass('hidden');

			$('.configurator-step-link[data-step-number=' + activeStep + ']').addClass('active');
			$('.option-group-step[data-step-group-id=' + activeStep + ']').removeClass('hidden');

			$('.option-group-step[data-step-group-id=' + activeStep + '] .option-group[data-slide-preview]:not(.hidden)').each(function (index, item) {
				var slidePreviewKey = $(this).data('slide-preview');

				if (!gallerySwiper) {
					return;
				}

				_setActiveImageSlide(slidePreviewKey);
			});

			if ( stepNavSwiper ) {
				stepNavSwiper.slideTo( activeStep - 1 );
			}

			if ( ! STEPPER_DISABLE_SCROLL_TOP ) {
				if ( $('.staggs-configurator-inline').length || $('.staggs-configurator').hasClass( 'has-theme-header' ) || $('.staggs-configurator').hasClass( 'has-theme-footer' ) ) {
					$('.staggs-product-options').scrollTop(0);
				} else {
					$('html,body').scrollTop(0);
				}
			}
		}

		var validStep = _validateStepFields();
		if ( activeStep > minStep ) {
			$('.staggs-step-prev-button').removeClass('disabled');
		} else {
			$('.staggs-step-prev-button').addClass('disabled');
		}

		if ( activeStep < maxStep && validStep ) {
			$('.staggs-step-next-button').removeClass('disabled');
		} else {
			$('.staggs-step-next-button').addClass('disabled');
		}

		_setTotalsButton(validStep);
	}

	function _validateStepFields() {
		let validStep = true;

		if ( $('.option-group-step[data-step-group-id="' + activeStep + '"] .invalid').length ) {
			validStep = false;
		}

		$('.option-group-step[data-step-group-id="' + activeStep + '"] input[required], .option-group-step[data-step-group-id="' + activeStep + '"] textarea[required]').each(function () {
			if ( 'radio' === $(this).attr('type') ) {
				if ( ! $(this).parents('.option-group-options').find('input:checked').length ) {
					validStep = false;
				}
			} else {
				if ( ! $(this).val() ) {
					validStep = false;
				}
			}
		});
		
		$('.option-group-step[data-step-group-id="' + activeStep + '"] select[required]').each(function () {
			if ( ! $(this).val() ) {
				validStep = false;
			}
		});

		return validStep;
	}

	function _loadActiveStep() {
		var queryString = window.location.search;

		if ( queryString && ( queryString.includes('configuration=') || queryString.includes('options=') || queryString.includes('staggs_summary=') ) && ! queryString.includes('preview=true') ) {
			if ( typeof shared_config.progress !== 'undefined' ) {
				activeStep = shared_config.progress.current;
				visitedStep = shared_config.progress.visited;
				maxStep = shared_config.progress.max;
			} else if ( queryString.includes('origin=wc_cart_page') ) {
				activeStep = $('.option-group-step').length;
				visitedStep = $('.option-group-step').length;
				maxStep = $('.option-group-step').length;
			} else {
				maxStep = $('.option-group-step').length;
			}
		} else {
			maxStep = $('.option-group-step').length;
		}

		_setActiveStep();
	}

	function _setActiveImageSlide( slidePreviewKey ) {
		if ( ! slidePreviewKey ) {
			return;
		}
		gallerySwiper.slideTo((slidePreviewKey - 1), false, false);

		$('.view-nav-buttons button').removeClass('selected');
		$('#preview_nav_' + (slidePreviewKey - 1)).addClass('selected');
		$('#preview_nav_label_' + (slidePreviewKey - 1)).addClass('selected');
	}

	function _loadConfiguratorPopup() {
		var queryString = window.location.search;

		if ( $('.staggs-configurator-popup').length && ( queryString && queryString.includes('options=') && ! queryString.includes('preview=true') ) ) {
			$('.staggs-configurator-popup').addClass('active');
			$('body').addClass('staggs-popup-active');
			$('.staggs-message-wrapper').removeClass('inline');
		}
		
		if ( $('.staggs-configurator-popup').length ) {
			if ( $('.staggs-configurator-bottom-bar .product-view-usps').length ) {
				$('.staggs-configurator-bottom-bar .bottom-bar-left').addClass('has-usps');
			}

			if ( $('.staggs-configurator-bottom-bar-spacer').length ) {
				$('.staggs-configurator-bottom-bar-spacer').height( $('.staggs-configurator-bottom-bar').height() );
			}
		}
	}

	// Activate popup.
	$('.staggs-configure-product-button').on('click', function (e) {
		e.preventDefault();

		$('.staggs-configurator-popup').addClass('active');
		$('body').addClass('staggs-popup-active');
		$('.staggs-message-wrapper').removeClass('inline');

		if ( $(window).width() < 992 && ! $('.staggs-configurator-popup.popup-horizontal').length && ! $('.staggs-configurator-popup.show-popup-mobile-inline').length ) {
			var wrapperHeight = $('.staggs-configurator-topbar').height() + $('.staggs-configurator-bottom-bar').height();
			wrapperHeight += $('.staggs-product-view').height();

			$('.staggs-product-options').css('height', 'calc( 100vh - ' + wrapperHeight + 'px )' );
		}

		if ( $('.staggs-configurator-bottom-bar-spacer').length ) {
			$('.staggs-configurator-bottom-bar-spacer').height( $('.staggs-configurator-bottom-bar').height() );
		}

		if (gallerySwiper) {
			if ($('.staggs-view-gallery').data('backgrounds')) {
				var backgrounds = $('.staggs-view-gallery').data('backgrounds').split('|');
				var backgroundUrl = backgrounds[gallerySwiper.activeIndex] ? backgrounds[gallerySwiper.activeIndex] : backgrounds[backgrounds.length - 1];

				$('.product-view-inner').css('background-image', 'url(' + backgroundUrl + ')');
			}
		}
	});

	// Close popup.
	$('#close-popup').on('click', function (e) {
		e.preventDefault();
		$('.staggs-configurator-popup').removeClass('active');
		$('body').removeClass('staggs-popup-active');
		$('.staggs-message-wrapper').addClass('inline');
	});

	// Product tabs details.
	$('.fieldset .fieldset-legend').on('click', function (e) {
		e.preventDefault();
		$(this).parents('.fieldset').toggleClass('closed');
	});

	// Product gallery nav buttons.
	$(document).on('click', '.view-nav-buttons button', function (e) {
		e.preventDefault();

		$('.view-nav-buttons button').removeClass('selected');
		$(this).addClass('selected');

		// if ( $(this).data('background') ) {
		// 	if ( $('.staggs-product-view model-viewer').attr('environment-image') ) {
		// 		$('.staggs-product-view model-viewer').attr('environment-image', $(this).data('background'));
		// 	}

		// 	if ( $('.staggs-product-view model-viewer').attr('skybox-image') ) {
		// 		$('.staggs-product-view model-viewer').attr('skybox-image', $(this).data('background'));
		// 	}
		// }

		var slideKey = $(this).data('key');
		gallerySwiper.slideTo(slideKey, false, false);
	});

	// // Initial height.
	// $(window).scroll(function () {
	// 	if ( $(window).width() < 768 ) {
	// 		var scrollTop = $(window).scrollTop() + ( $(window).height() / 1.4 );
	// 	} else {
	// 		var scrollTop = $(window).scrollTop() + ( $(window).height() / 2 );
	// 	}

	// 	if ( $('.option-group-step-buttons').length ) {
	// 		return;
	// 	}

	// 	$('.option-group[data-slide-preview]:not(.hidden)').each(function (index, item) {
	// 		var slidePreviewKey = $(this).data('slide-preview');

	// 		if (!gallerySwiper) {
	// 			return;
	// 		}

	// 		if ( scrollTop > $(item).find('.option-group-options').offset().top) {
	// 			_setActiveImageSlide(slidePreviewKey);
	// 		}
	// 	});
	// });

	$(document).on('click', '.option-group.intro .back-button', function(e) {
		if ( $(this).data('message') ) {
			return confirm( $(this).data('message') );
		}
	});

	$(document).on('click', '#totals-list-collapse', function(e) {
		e.preventDefault();
		$(this).parents('.totals-list').toggleClass('collapsed');
	});

	$(document).on('click', '.option-group-header a.show-panel', function (e) {
		e.preventDefault();

		if ( $(this).parents('.option-group-content').next('.option-group-panel').length ) {
			$(this).parents('.option-group-content').next('.option-group-panel').addClass('shown');
			$('body').addClass('panel-shown');
		} else {
			var panelId = $(this).parents('.option-group').data('step');
			if ( $('.staggs-configurator-main #description-panel-' + panelId).length ) {
				$('.staggs-configurator-main #description-panel-' + panelId).addClass('shown');
				$('body').addClass('panel-shown');
			} else {
				$(this).next('.option-group-tooltip-description').toggleClass('shown');
			}
		}
	});

	$(document).on('click', '.option-group-options a.show-panel-option', function (e) {
		e.preventDefault();
		if ( $(this).closest('.option-group-content').find('.option-group-panel').length ) {
			var label = $(this).closest('label').find('.option-label').text();
			if ( $(this).closest('label').find('.sgg-product-name').length ) {
				label = $(this).closest('label').find('.sgg-product-name').text();
			}
			var content = $(this).closest('label').find('.option-description').html();

			$(this).closest('.option-group-content').find('.option-group-panel-label').text(label);
			$(this).closest('.option-group-content').find('.option-group-panel-content').html(content);

			$(this).closest('.option-group-content').find('.option-group-panel').addClass('shown');
			$('body').addClass('panel-shown');
		}
	});

	$(document).on('click', '.option-group-panel a.close-panel', function (e) {
		e.preventDefault();
		$(this).parents('.option-group-panel').removeClass('shown');
		$('body').removeClass('panel-shown');
	});

	$(document).on('click', '.option-group-options a.button-minus', function (e) {
		e.preventDefault();
		var val = parseFloat( $(this).siblings('input').val() ) ?? 0;
		var min = $(this).siblings('input').attr('min') ?? 0;
		var step = $(this).siblings('input').attr('step') ?? 1;

		if ( val - step > min ) {
			$(this).siblings('input').val(val - step);
		}
		else {
			$(this).siblings('input').val(min);
		}
		$(this).siblings('input').trigger('input');
	});

	$(document).on('click', '.option-group-options a.button-plus', function (e) {
		e.preventDefault();
		var val = parseFloat( $(this).siblings('input').val() ) ?? 0;
		var min = $(this).siblings('input').attr('min') ?? 0;
		var max = $(this).siblings('input').attr('max') ?? 9999999999999; // crazy large
		var step = $(this).siblings('input').attr('step') ?? 1;

		if ( val + step < max ) {
			$(this).siblings('input').val(val + step);
		}
		else if ( ! val ) {
			$(this).siblings('input').val(min);
		}
		else {
			$(this).siblings('input').val(max);
		}
		$(this).siblings('input').trigger('input');
	});

	$(document).on('change', '#configurator-options input[type=radio], #configurator-options input[type=checkbox], #configurator-options select', function () {
		_validateConditionalSteps();
	});

	$(document).on('input', '#configurator-options input[type=text], #configurator-options textarea, #configurator-options input[type=number]', function () {
		_validateConditionalSteps();
	});

	function _validateConditionalSteps() {
		var keyValuePairs = {};

		// Collect selected values.
		$('#configurator-options input, #configurator-options select').each(function(index,input) {
			// conditional steps present.
			if ( $(input).attr('type') === 'checkbox' || $(input).attr('type') === 'radio' ) {
				var checked = $(this).is(':checked');
			} else {
				var checked = $(this).val() ? true : false;
			}

			if ($(input).find('option').length) {
				var stepId = $(input).find(':selected').data('step-id');
				var stepValue = $(input).find(':selected').data('option-id');
			} else if ( 'checkbox' === $(input).attr('type') ) {
				var stepId = $(input).data('step-id');
				var stepValue = $(input).data('option-id');
				checked = $(input).is(':checked');
			} else if ( 'radio' === $(input).attr('type') ) {
				var stepId = $(input).data('step-id');
				var stepValue = $(input).data('option-id');
			} else {
				var stepId = $(input).data('step-id');
				var stepValue = $(input).val();
			}

			if ( stepId in keyValuePairs ) {
				if ( checked ) {
					keyValuePairs[stepId].push(stepValue);
				}
			} else {
				if ( checked ) {
					keyValuePairs[stepId] = [stepValue];
				} else {
					keyValuePairs[stepId] = [];
				}
			}
		});

		var newGroupList = [];

		$('.conditional-wrapper').each(function(index, group) {
			var conditionalRules = $(group).data('step-rules');

			var stepId = $(group).data('step-id');
			var loaded = $(group).find('.option-group-content').length;
			if ( $(group).data('step-type') && 'repeater' == $(group).data('step-type') ) {
				var loaded = $(group).find('.staggs-repeater-main').length;
			}

			// Get valid rules count.
			var valid = _validateRuleset( conditionalRules, keyValuePairs );
			if ( valid ) {
				$(group).addClass('conditional-loaded');

				if ( $(group).data('step-type') && 'repeater' == $(group).data('step-type') ) {
					$(group).find('.staggs-repeater').removeClass('staggs-repeater-hidden');
				}

				if ( ! loaded ) {
					var savedHtml = sessionStorage.getItem('option-group-' + stepId);

					setConditionalStepHtml(group, savedHtml);

					activateConditionalStepFields(group);

					newGroupList.push(group);

					if ( $('.option-group-step-toggler[data-step-group-id=' + stepId + ']').length ) {
						$('.option-group-step-toggler[data-step-group-id=' + stepId + ']').removeClass('hidden');
					}

					// Show linked tabs.
					if ( $('.tab-list a[data-tabs*="' + stepId + '"]').length ) {
						$('.tab-list a[data-tabs*="' + stepId + '"]').removeClass('hidden');
						$('#option-group-' + stepId).removeClass('tab-hidden');
					}
				
					// Hide conditional groups when linked to tabs
					if ( $('.tab-list a[data-tabs*="' + stepId + '"]').length ) {
						if ( ! $('.tab-list a[data-tabs*="' + stepId + '"].active').length ) {
							$('#option-group-' + stepId).addClass('tab-hidden');
						}
					}

					// Display group wrapper when inner options present.
					if ( $(group).parents('.option-group-step.collapsible.hidden').length ) {
						if ( $(group).parents('.option-group-step').find('.option-group').length ) {
							if ( $(group).parents('.option-group-step').data('step-group-id') ) {
								// Stepper collapsible group. Check if step matches active step.
								if ( activeStep == $(group).parents('.option-group-step').data('step-group-id') ) {
									$(group).parents('.option-group-step').removeClass('hidden');
								}
							} else {
								// No stepper. Valid. Show group
								$(group).parents('.option-group-step').removeClass('hidden');
							}
						}
					}
				}
			} else {
				var html = $(group).html();
				if ( html ) {
					sessionStorage.setItem('option-group-' + stepId, html);
				}

				if ( $(group).data('step-type') && 'repeater' == $(group).data('step-type') ) {
					
					if ( $(group).find('.staggs-repeater-main').length ) {
						_emptyRepeater( $(group).find('.staggs-repeater-main') );
						$(group).find('.staggs-repeater').addClass('staggs-repeater-hidden');
					}

				} else {

					// Throw event to be catched by model viewer.
					var model = $(group).find('.option-group').data('model');
					if ( $(group).find('select') && $(group).find('option:first-of-type').data('preview-urls') ) {
						var texture = $(group).find('option:first-of-type').data('preview-urls').replace('0|', '');
					} else if ( $(group).find('input' ) && $(group).find('input:first-of-type').data('preview-urls') ) {
						var texture = $(group).find('input:first-of-type').data('preview-urls').replace('0|', '');
					}

					if ( $(group).find('select').length && $(group).find('option:first-of-type').data('color') ) {
						var color = $(group).find('option:first-of-type').data('color');
					} else if ( $(group).find('input').length && $(group).find('input:first-of-type').data('color') ) {
						var color = $(group).find('input:first-of-type').data('color');
					}

					$.event.trigger({
						type: "modelOptionsGroupRemoved",
						model: model,
						texture: texture,
						color: color,
					});

					// Clear images from previews.
					if ( $('.staggs-view-gallery .swiper-slide').length ) {
						var group_id = $(group).find('input').attr('name');
						if ( ! group_id ) {
							group_id = $(group).find('select').attr('name');
						}
						
						if ( $(group).find('.option-group-options input[type=checkbox]').length ) {
							$(group).find('input').each(function(index,input) {
								var input_id = $(input).attr('id');

								$('.staggs-view-gallery .swiper-slide').each(function(key, slide) {
									if ( $(slide).find('img[id="preview_' + key + '_' + input_id + '"]').length ) {
										$(slide).find('img[id="preview_' + key + '_' + input_id + '"]').remove();
									}
									if ( $('.staggs-view-gallery').find('img[id="button_preview_' + key + '_' + input_id + '"]').length ) {
										$('.staggs-view-gallery').find('img[id="button_preview_' + key + '_' + input_id + '"]').remove();
									}
								});
							});
						} else {
							$('.staggs-view-gallery .swiper-slide').each(function(key, slide) {
								if ( $(slide).find('img[id="preview_' + key + '_' + group_id + '"]').length ) {
									$(slide).find('img[id="preview_' + key + '_' + group_id + '"]').remove();
								}
								if ( $('.staggs-view-gallery').find('img[id="button_preview_' + key + '_' + group_id + '"]').length ) {
									$('.staggs-view-gallery').find('img[id="button_preview_' + key + '_' + group_id + '"]').remove();
								}
							});
						}
					}

					if ( $('.option-group-step-toggler[data-step-group-id=' + stepId + ']').length ) {
						$('.option-group-step-toggler[data-step-group-id=' + stepId + ']').addClass('hidden');
					}

					// Clear linked tabs.
					if ( $('.tab-list a[data-tabs*="' + stepId + '"]').length ) {
						if ( $('.tab-list a[data-tabs*="' + stepId + '"]').data('tabs') && $('.tab-list a[data-tabs*="' + stepId + '"]').data('tabs').toString().includes(',') ) {
							$('.tab-list a[data-tabs*="' + stepId + '"]').each(function(index,tabLink) {
								var tabIds = $(tabLink).data('tabs').split(',');
								var hiddenCount = 0;
								
								tabIds.forEach(function(tabId,i){
									if ( $('#option-group-' + tabId).hasClass('hidden')){
										hiddenCount++
									}
								});
								
								if (hiddenCount === tabIds.length && hiddenCount > 0){
									$(tabLink).addClass('hidden');
								}
							});
						} else {
							$('.tab-list a[data-tabs*="' + stepId + '"]').addClass('hidden');
						}
					}

					if (!IMAGE_STACK) {
						var group_id = $(group).find('input').attr('name');
						if ( ! group_id ) {
							group_id = $(group).find('select').attr('name');
						}

						if ( group_id ) {
							_deletePreviewGallery('', group_id);
						}
					}

					// Clear group wrapper when no inner options set.
					if ( $(group).parents('.option-group-step').length ) {
						if ( ! $(group).parents('.option-group-step').find('.option-group').length ) {
							$(group).parents('.option-group-step').addClass('hidden');
						}
					}

					// Empty text previews.
					$('.staggs-product-view .preview-text-input').each(function(index, item) {
						var item_id = $(item).attr('id');

						if ( $(item).parents('.preview-text-input-bundle' ).length ) {
							var group_id = $(item).parents('.preview-text-input-bundle').attr('id');
							group_id = group_id.replace('_wrapper', '');

							if ( $(group).find('#option-group-' + group_id).length ) {
								$(item).text('');
							}
						} else if ( $(group).find('.option-group input[data-field-key=' + item_id + ']').length ) {
							$(item).text('');
						}
					});

					// Clear image previews.
					$('.preview-image-input').each(function(index, item) {
						var item_id = $(item).attr('id');
						if ( $(group).find('.option-group input[data-field-key=' + item_id + ']').length ) {
							$(item).remove();
						}
					});

					// Clear html.
					$(group).html('');
				}
			}
		});

		// Check conditional logic options
		var changedOptionGroups = [];

		$('.option-group-options label[data-option-rules]').each(function (index, option) {
			var conditionalRules = $(option).data('option-rules');
			var loaded = $(option).find('input').length;

			// Get valid rules count.
			var valid = _validateRuleset( conditionalRules, keyValuePairs );
			var optionId = $(option).attr('for');

			if ( ! valid ) {
				// Not valid, still added.
				if ( loaded ) {
					if ( KEEP_CONDITIONAL_OPTIONS ) {
						$('label[for="' + optionId + '"] input').prop('checked', false);
						$('label[for="' + optionId + '"] input').prop('disabled', true);
						$('label[for="' + optionId + '"]').addClass('disabled');

						if ( ! $(this).parents('.option-group').find('label:not(.disabled) input:checked').length ) {
							$(this).parents('.option-group').find('label:not(.disabled) input').each(function(index,input) {
								$(input).prop('checked',true);
								$(input).trigger('change');
								return false; // break;
							});
						}
					} else {
						$(option).find('input').prop('checked', false);
						var html = $(option).html();
						if ( html ) {
							sessionStorage.setItem('sgg-option-' + optionId, html);
						}
						
						$(option).html('');
						$(option).addClass('hidden');

						changedOptionGroups.push( $(this).closest('.option-group') );
					}
				}
			} else {
				if ( ! loaded ) {
					// Valid, not added.
					if ( ! KEEP_CONDITIONAL_OPTIONS ) {
						var optionHtml = sessionStorage.getItem('sgg-option-' + optionId);
						$('label[for="' + optionId + '"]').html(optionHtml);
						$('label[for="' + optionId + '"]').removeClass('hidden');
					}

					changedOptionGroups.push( $(this).closest('.option-group') );
				} else if ( KEEP_CONDITIONAL_OPTIONS ) {
					$('label[for="' + optionId + '"] input').prop('disabled', false);
					$('label[for="' + optionId + '"]').removeClass('disabled');

					if ( ! $(this).parents('.option-group').find('label:not(.disabled) input:checked').length ) {
						$(this).parents('.option-group').find('label:not(.disabled) input').each(function(index,input) {
							$(input).prop('checked',true);
							$(input).trigger('change');
							return false; // break;
						});
					}
				}
			}
		});

		$('.option-group-options select option[data-option-rules]').each(function(index, option) {
			var conditionalRules = $(option).data('option-rules');

			// Get valid rules count.
			var valid = _validateRuleset( conditionalRules, keyValuePairs );

			if ( ! valid && ! $(option).hasClass('disabled') && ! $(option).hasClass('hidden') ) {
				// Not valid, still added.
					$(option).prop('selected', false);
					$(option).prop('disabled', true);

				if ( KEEP_CONDITIONAL_OPTIONS ) {
					$(option).addClass('disabled');
				} else {
					$(option).addClass('hidden');
				}

				changedOptionGroups.push( $(option).closest('.option-group') );
			} else if ( valid && ( $(option).hasClass('disabled') || $(option).hasClass('hidden') ) ) {
				// Valid, not added.
				$(option).prop('disabled', false);

				if ( ! KEEP_CONDITIONAL_OPTIONS ) {
					$(option).removeClass('hidden');
				} else {
					$(option).removeClass('disabled');
				}

				changedOptionGroups.push( $(option).closest('.option-group') );
			}
		});

		if ( changedOptionGroups.length ) {
			changedOptionGroups.forEach(function(group,index) {
				setActiveStepOptions( $(group) );
			});
		}

		if ( $('.tab-list a.active.hidden').length && $('.tab-list a:not(.hidden)').length ) {
			$('.tab-list a:not(.hidden)').get(0).click();
		}

		newGroupList.forEach(function(group, index){
			$(group).find('.option-group-options').each(function (index, item) {
				setActiveStepOptions(item);
			});
		});

		_setTotals();
	}

	function setConditionalStepHtml(group, html) {
		$(group).html(html);
	}

	function activateConditionalStepFields(group) {
		if ( $(group).find('.option-group-options.select').length && $(window).width() > 991 ) {
			$(group).find('.ui-selectmenu-button').remove();

			$('.staggs-product-options .option-group-options.select select').each(function(index,selectmenu) {
				var $selectmenu = $(selectmenu).selectmenu( _getSelectMenuOptions() );
				$selectmenu.data("ui-selectmenu")._renderItem = _selectMenuItemRenderer;
				$selectmenu.data("ui-selectmenu")._renderButtonItem = _selectMenuButtonRenderer;
			});

			if ( $('.staggs-configurator-main.border-rounded option-group-options.select').length || $('.option-group.border-rounded .option-group-options.select').length ) {
				$('.ui-selectmenu-menu').addClass('border-rounded');
			}
			if ( $('.staggs-configurator-main.border-pill option-group-options.select').length || $('.option-group.border-pill .option-group-options.select').length ) {
				$('.ui-selectmenu-menu').addClass('border-pill');
			}

			if ( $('.staggs-floating .option-group-step.collapsible .ui-selectmenu-button').length ) {
				$('.staggs-floating .option-group-step.collapsible .ui-selectmenu-button').each(function(index,button) {
					var id = $(button).attr('aria-owns');
					$('.ui-selectmenu-menu #' + id).addClass('floating-collapsible');
				});
			}
		}

		if ( $(group).find('.option-group-options input.datepicker-input').length ) {
			$(group).find('.option-group-options input.datepicker-input').each(function(index,datepicker) {
				var options = {};
				if ( $(datepicker).data('date-format') ) {
					options.dateFormat = $('.datepicker-input').data('date-format');
				}
				if ( $(datepicker).data('date-min') ) {
					options.minDate = $('.datepicker-input').data('date-min');
				}
				if ( $(datepicker).data('date-max') ) {
					options.maxDate = $('.datepicker-input').data('date-max');
				}

				if ( $(datepicker).data('inline') ) {
					$(datepicker).next('.datepicker-input-inline').datepicker(options);
					$(datepicker).next('.datepicker-input-inline').change(function(){
						$('input[type=text]').val($(this).val());
					});
				} else {
					$(datepicker).datepicker(options).on("change", function() {
						$(this).trigger('input');
					});
				}
	
				if ( $('.staggs-configurator-main.border-rounded').length || $('.option-group.border-rounded .option-group-options.text-input').length ) {
					$('.ui-datepicker.ui-widget').addClass('border-rounded');
				}
				if ( $('.staggs-configurator-main.border-pill').length || $('.option-group.border-pill .option-group-options.text-input').length ) {
					$('.ui-datepicker.ui-widget').addClass('border-pill');
				}
			});
		}

		if ( $(group).find('.option-group-options input[data-type=range]').length ) {
			$(group).find('.option-group-options input[data-type=range]').each(function(item,range) {
				_initRangeSlider( $(range) );
			});
		}
	}

	$(document).on('click', '.option-group:not(.hidden) label', function() {
		var slidePreviewKey = $(this).parents('.option-group').data('slide-preview');
		if (slidePreviewKey && gallerySwiper) {
			_setActiveImageSlide(slidePreviewKey);
		}
	});

	$(document).on('click', '.option-group-options label', function () {
		if ( $(this).find('input').data('page-url') && ! DISABLE_URL_CLICK ) {
			if ( $(this).find('input').data('page-url') !== window.location.href ) {
				$('.staggs-configurator-main').addClass('loading-page');
				window.location.href = $(this).find('input').data('page-url');
			}
		}
	});
 
	$(document).on('change', '.option-group-options.options input', function () {
		var group = $(this).attr('name');
		if ($(this).attr('type') === 'checkbox' ) {
			group = $(this).attr('id');
		}

		var stepId  = $(this).parents('.option-group').data('step');
		var preview = $(this).data('preview-urls');
		var order   = $(this).parents('.option-group').data('preview-order');
		var price   = parseFloat($(this).data('price'));

		if ( TRACK_OPTIONS ) {
			if ( $(this).is('checked') ) {
				$(this).attr('checked', 'checked');
			} else {
				$(this).attr('checked', false);
			}

			if ( TRACK_GLOBAL_OPTIONS ) {
				sessionStorage.setItem( PRODUCT_ID + '_sgg_' + group + '_option', $(this).data('option-id') )
			}
		}

		if ( $(this).next('.option').length ) {
			var text = '';
			if ( $(this).is(':checked') ) {
				text = $(this).next('.option').find('.option-label').text();
			}
			$(this).parents('.option-group-content').find('.option-group-summary').find('.name').html( text );
		} else if ( $(this).next('.button').length ) {
			var text = '';
			if ( $(this).is(':checked') ) {
				text = $(this).next('.button').find('.button-name').text();
			}
			$(this).parents('.option-group-content').find('.option-group-summary').find('.name').html(text);
		}

		if ( SHOW_PRODUCT_PRICE ) {
			if (isNaN(price)) {
				var value = '';
				if ( $(this).is(':checked') ) {
					value = INC_PRICE_LABEL;
				}
				$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html(value);
				price = 0;
			} else {
				var labelVal = $(this).data('price');
				var value = '';
				if ( $(this).is(':checked') ) {
					if (isNaN(labelVal) && labelVal.includes('|')) {
						var valueParts = labelVal.split('|');
						var value = _formatPriceOutput(valueParts[1]) + ' <del>' + _formatPriceOutput(valueParts[0]) + '</del>';
					} else if (!isNaN(parseFloat(labelVal)) && isFinite(labelVal)) {
						var value = _formatPriceOutput(labelVal);
					}
				}
				$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html(value);
			}

			if ( $('#configurator-options [data-table-x="' + group + '"]').length ) {
				$('#configurator-options [data-table-y="' + group + '"] input').trigger('input');
			}
			if ( $('#configurator-options [data-table-y="' + group + '"]').length ) {
				$('#configurator-options [data-table-y="' + group + '"] input').trigger('input');
			}
		} else {
			$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html('');
		}

		if (preview && $(this).is(':checked')) {
			if ( $(this).data('order') ) {
				order = $(this).data('order');
			}
			_buildPreviewGallery(preview, group, order);
		} else {
			_deletePreviewGallery(preview, group);
		}

		_updatePreviewTextFont( $(this) );

		if ( $(this).data('input-key') ) {
			if ( $(this).data('color') ) {
				var selector = 'color';
				if ( $(this).data('preview-selector') ) {
					selector = $(this).data('preview-selector');
				}
				_updatePreviewStyles( $(this).data('input-key'), selector + ': ' + $(this).data('color') );
			} else if ( $(this).data('preview-selector') ) {
				_updatePreviewStyles( $(this).data('input-key'), $(this).data('preview-selector') + ': ' + $(this).val() );
			}
		} else if ( $(this).closest('.option-group').data('model') ) {
			if ( $(this).data('color') ) {
				_updateOptionMaterialCanvas( $(this).closest('.option-group').data('model'), {'fill': $(this).data('color') } );
			}
		}

		if ($(this).is(':checked')) {
			pricetotal[group] = price;
			altpricetotal[group] = parseFloat($(this).data('alt-price'));
		} else {
			delete pricetotal[group];
			delete altpricetotal[group];
		}

		_updateMeasurementPrices( $(this), group );

		_recalculateMeasurementPricings( $(this), group, stepId );

		_updateOptionPriceDisplay( $(this), '.option-price', price );

		_setTotals();
	});

	$(document).on('change', '.option-group-options.icons input', function () {
		var group = $(this).attr('name');
		if ($(this).attr('type') === 'checkbox' ) {
			group = $(this).attr('id');
		}

		var stepId = $(this).parents('.option-group').data('step');
		var price = parseFloat($(this).data('price'));
		var preview = $(this).data('preview-urls');
		var order = $(this).parents('.option-group').data('preview-order');

		if ( TRACK_OPTIONS ) {
			if ( $(this).is('checked') ) {
				$(this).attr('checked', 'checked');
			} else {
				$(this).attr('checked', false);
			}

			if ( TRACK_GLOBAL_OPTIONS ) {
				sessionStorage.setItem( PRODUCT_ID + '_sgg_' + group + '_option', $(this).data('option-id') )
			}
		}

		var text = '';
		if ( $(this).is(':checked') ) {
			text = $(this).data('label');
		}
		$(this).parents('.option-group-content').find('.option-group-summary').find('.name').html(text);

		if ( SHOW_PRODUCT_PRICE ) {
			if (isNaN(price)) {
				var value = '';
				if ( $(this).is(':checked') ) {
					value = INC_PRICE_LABEL;
				}
				$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html(value);
				price = 0;
			} else {
				var labelVal = $(this).data('label-value');
				var value = '';
				if ( $(this).is(':checked') ) {
					if (isNaN(labelVal) && labelVal.includes('|')) {
						var valueParts = labelVal.split('|');
						var value = _formatPriceOutput(valueParts[1]) + ' <del>' + _formatPriceOutput(valueParts[0]) + '</del>';
					} else if (!isNaN(parseFloat(labelVal)) && isFinite(labelVal)) {
						var value = _formatPriceOutput(labelVal);
					}
				}
				$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html(value);
			}
		} else {
			$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html('');
		}
		
		if (preview && $(this).is(':checked')) {
			if ( $(this).data('order') ) {
				order = $(this).data('order');
			}
			_buildPreviewGallery(preview, group, order);
		} else {
			_deletePreviewGallery(preview, group);
		}

		if ( $(this).parents('.option-group').data('preview-ref') && $(this).data('color') ) {
			var selector = 'color';
			if ( $(this).data('preview-selector') ) {
				selector = $(this).data('preview-selector');
			}
			var css_rule = selector + ': ' + $(this).data('color');
			_updatePreviewStyles( $(this).parents('.option-group').data('preview-ref'), css_rule );
		} else if ( $(this).data('input-key') ) {
			if ( $(this).data('color') ) {
				var selector = 'color';
				if ( $(this).data('preview-selector') ) {
					selector = $(this).data('preview-selector');
				}
				_updatePreviewStyles( $(this).data('input-key'), selector + ': ' + $(this).data('color') );
			} else if ( $(this).data('preview-selector') ) {
				_updatePreviewStyles( $(this).data('input-key'), $(this).data('preview-selector') + ': ' + $(this).val() );
			}
		} else if ( $(this).closest('.option-group').data('model') ) {
			if ( $(this).data('color') ) {
				_updateOptionMaterialCanvas( $(this).closest('.option-group').data('model'), {'fill': $(this).data('color') } );
			}
		}

		if ($(this).is(':checked')) {
			pricetotal[group] = price;
			altpricetotal[group] = parseFloat($(this).data('alt-price'));
		} else {
			delete pricetotal[group];
			delete altpricetotal[group];
		}

		_updateMeasurementPrices( $(this), group );

		_recalculateMeasurementPricings( $(this), group, stepId );

		_updateOptionPriceDisplay( $(this), '.tooltip-price', price );

		_setTotals();
	});

	$(document).on('change', '.option-group-options.select select', function () {
		var group = $(this).attr('name');
		var price = parseFloat($(this).find(':selected').data('price'));
		var preview = $(this).find(':selected').data('preview-urls');
		var order = $(this).parents('.option-group').data('preview-order');
		var stepId = $(this).parents('.option-group').data('step');

		if ( TRACK_OPTIONS ) {
			$(this).find(':selected').attr('selected', 'selected');

			if ( TRACK_GLOBAL_OPTIONS ) {
				sessionStorage.setItem( PRODUCT_ID + '_sgg_' + group + '_option', $(this).data('option-id') )
			}
		}

		if ($(this).val()) {
			$(this).removeClass('invalid');
			$(this).parents('.option-group').find('.sgg-error').remove();
			$(this).parents('.option-group-content').find('.option-group-summary').find('.name').html($(this).find(':selected').val());

			if ( SHOW_PRODUCT_PRICE ) {
				if (isNaN(price)) {
					$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html(INC_PRICE_LABEL);
					pricetotal[group] = 0;
					altpricetotal[group] = 0;
				} else {
					$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html(_formatPriceOutput(price));
					pricetotal[group] = price;
					altpricetotal[group] = parseFloat($(this).find(':selected').data('alt-price'));
				}
			} else {
				$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html('');
			}

			if (preview) {
				if ( $(this).data('order') ) {
					order = $(this).data('order');
				}
				_buildPreviewGallery(preview, group, order);
			} else {
				_deletePreviewGallery(preview, group);
			}
		} else {
			$(this).parents('.option-group-content').find('.option-group-summary').find('.name').html('');
			$(this).parents('.option-group-content').find('.option-group-summary').find('.value').html('');

			delete pricetotal[group];
			delete altpricetotal[group];

			// Check if preview set.
			if ($('.staggs-view-gallery [id^="preview_"][id$="' + group + '"]').length) {
				$('.staggs-view-gallery [id^="preview_"][id$="' + group + '"]').remove();
			}
		}

		if ( $(this).find(':selected').data('input-key') ) {
			if ( $(this).find(':selected').data('color') ) {
				var selector = 'color';
				if ( $(this).data('preview-selector') ) {
					selector = $(this).data('preview-selector');
				}
				_updatePreviewStyles( $(this).find(':selected').data('input-key'), selector + ': ' + $(this).find(':selected').data('color') );
			}  else if ( $(this).find(':selected').data('preview-selector') ) {
				_updatePreviewStyles( $(this).find(':selected').data('input-key'), $(this).find(':selected').data('preview-selector') + ': ' + $(this).find(':selected').val() );
			}
		} else if ( $(this).closest('.option-group').data('model') ) {
			if ( $(this).find(':selected').data('color') ) {
				_updateOptionMaterialCanvas( $(this).closest('.option-group').data('model'), {'fill': $(this).data('color') } );
			}
		}

		_updatePreviewTextFont( $(this).find(':selected') );

		_updateMeasurementPrices( $(this), group );

		_recalculateMeasurementPricings( $(this), group, stepId );

		_setTotals();
	});

	$(document).on('change', '.option-group-options.single input[type=checkbox]', function () {
		var group = $(this).attr('name');
		var price = parseFloat($(this).data('price'));
		var preview = $(this).data('preview-urls');
		var order = $(this).parents('.option-group').data('preview-order');

		if ( TRACK_OPTIONS ) {
			if ($(this).is(':checked')) {
				$(this).attr('checked', 'checked');
			} else {
				$(this).attr('checked', false);
			}
		}

		if (isNaN(price)) {
			price = 0;
		}

		if (preview && $(this).is(':checked')) {
			if ( $(this).data('order') ) {
				order = $(this).data('order');
			}
			_buildPreviewGallery(preview, group, order);
		} else {
			_deletePreviewGallery(preview, group);
		}

		if ($(this).is(':checked')) {
			pricetotal[group] = price;
			altpricetotal[group] = parseFloat($(this).data('alt-price'));
		} else {
			delete pricetotal[group];
			delete altpricetotal[group];
		}

		_setTotals();
	});

	$(document).on('change', '.option-group-options.tickboxes input[type=checkbox].tickboxes-all-option', function () {
		$(this).closest('.tickboxes').find('input:not(.tickboxes-all-option)').prop('checked', $(this).is(':checked') );
		$(this).closest('.tickboxes').find('input:not(.tickboxes-all-option)').trigger('change');
	});

	$(document).on('change', '.option-group-options.tickboxes input[type=checkbox]:not(.tickboxes-all-option)', function () {
		var group = $(this).attr('id');
		var price = parseFloat($(this).data('price'));
		var preview = $(this).data('preview-urls');
		var order = $(this).parents('.option-group').data('preview-order');
		var stepId = $(this).parents('.option-group').data('step');

		if ( TRACK_OPTIONS ) {
			if ( $(this).is('checked') ) {
				$(this).attr('checked', 'checked');
			} else {
				$(this).attr('checked', false);
			}
		}

		if (isNaN(price)) {
			price = 0;
		}

		if (preview && $(this).is(':checked')) {
			if ( $(this).data('order') ) {
				order = $(this).data('order');
			}
			_buildPreviewGallery(preview, group, order);
		} else {
			_deletePreviewGallery(preview, group);
		}

		if ($(this).is(':checked')) {
			pricetotal[group] = price;
			altpricetotal[group] = parseFloat($(this).data('alt-price'));
		} else {
			delete pricetotal[group];
			delete altpricetotal[group];
		}

		_updateMeasurementPrices( $(this), group );

		_recalculateMeasurementPricings( $(this), group, stepId );

		_setTotals();
	});

	$(document).on('input', '.option-group-options.text-input input, .option-group-options.text-input textarea', function () {
		var group = $(this).attr('name');
		var val = $(this).val().length;
		var stepId = $(this).parents('.option-group').data('step');
		var min, max, price, unit_price;

		if ( TRACK_OPTIONS ) {
			$(this).attr('value', $(this).val());
		}

		if ( 'date' === $(this).data('type') ) {
			if ( $(this).attr('required') ) {
				// Required field.
				if ( $(this).val() && $(this).val().length >= 10 ) {
					$(this).removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			} else {
				$(this).removeClass('invalid');
				$(this).parents('.option-group').find('.sgg-error').remove();
			}
		} else {
			if ( $(this).attr('minlength') ) {
				min = parseFloat($(this).attr('minlength'));
			}
			if ( $(this).attr('maxlength') ) {
				max = parseFloat($(this).attr('maxlength'));
			}
			if ( $(this).data('price') ) {
				price = parseFloat($(this).data('price'));
			}
			if ( $(this).data('unit-price') ) {
				unit_price = parseFloat($(this).data('unit-price'));
			}
	
			if ( $(this).attr('required') ) {
				// Required field.
				if ( ! min && ! max && $(this).val() ) {
					$(this).removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				} else {
					if (min && val && val < min) {
						$(this).addClass('invalid');
					} else if (max && val && val > max) {
						$(this).addClass('invalid');
					} else {
						$(this).removeClass('invalid');
						$(this).parents('.option-group').find('.sgg-error').remove();
					}
				}
			} else if ( $(this).val() ) {
				// Optional field. Only validate if value is set.
				if (min && val < min) {
					$(this).addClass('invalid');
				} else if (max && val > max) {
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			} else {
				$(this).removeClass('invalid');
				$(this).parents('.option-group').find('.sgg-error').remove();
			}
		}

		var material = $(this).parents('.option-group').data('model');
		if (  '' != material || $(this).data('material-key') ) {
			// 3D model
			var textureChannel = $(this).parents('.option-group').data('model-material');
			if ( ! textureChannel ) {
				textureChannel = 'base';
			}
			if ( ! material ) {
				material = $(this).data('material-key');
			}
			
			if ( $('#' + group + '_canvas').length ) {
				var canvasId = group + '_canvas';
				var objConfig = {
					id: group,
					width: $(this).data('preview-width'),
					height: $(this).data('preview-height'),
					textAlign: 'center',
				};

				if ( ! sgg_canvasses.hasOwnProperty(canvasId) ) {
					sgg_canvasses[canvasId] = new fabric.Canvas(canvasId);
					var text = new fabric.Textbox( $(this).val(), objConfig );
					sgg_canvasses[canvasId].add(text);
				} else {
					var text = _getCanvasObjectById(group, canvasId);
					text[0].set( 'text', $(this).val() );
				}

				sgg_canvasses[canvasId].renderAll();
				var dataURL = sgg_canvasses[canvasId].toDataURL();

				if ( ! textureChannel ) {
					textureChannel = 'base';
				}

				$.event.trigger({
					type: "modelMaterialChanged",
					model: material,
					channel: textureChannel,
					texture: dataURL,
				});
			}
		}

		if (price) {
			if ($(this).val()) {
				pricetotal[group] = price;
				altpricetotal[group] = price;
			} else {
				delete pricetotal[group];
				delete altpricetotal[group];
			}
		} else if (unit_price) {
			if ($(this).val()) {
				pricetotal[group] = unit_price * val;
				altpricetotal[group] = unit_price * val;
			} else {
				delete pricetotal[group];
				delete altpricetotal[group];
			}
		}

		_updateMeasurementPrices( $(this), group );

		_recalculateMeasurementPricings( $(this), group, stepId );

		_setTotals();
	});

	$(document).on('input', '.option-group-options.measurements input[type=number]', function () {
		var min = parseFloat($(this).attr('min'));
		var max = parseFloat($(this).attr('max'));
		var val = parseFloat($(this).val());

		if ( TRACK_OPTIONS ) {
			$(this).attr('value', $(this).val());
		}

		if ( $(this).attr('required') ) {
			// Required field. Always validate.
			if ( ! min && ! max && $(this).val() ) {
				// No min and max defined. Value remove invalid.
				$(this).removeClass('invalid');
				$(this).parents('.option-group').find('.sgg-error').remove();
			} else {
				// Min and max defined. Check val.
				if (min && val < min) {
					$(this).addClass('invalid');
				} else if (max && val > max) {
					$(this).addClass('invalid');
				} else {
					$(this).removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			}
		} else if ( $(this).val() ) {
			// Optional field. Only validate if value is set.
			if (min && val < min) {
				$(this).addClass('invalid');
			} else if (max && val > max) {
				$(this).addClass('invalid');
			} else {
				$(this).removeClass('invalid');
				$(this).parents('.option-group').find('.sgg-error').remove();
			}
		}

		if ( ( $(this).attr('required') || $(this).val() )
			&& ! $(this).hasClass('invalid') 
			&& ( $(this).parents('.option-group').data('shared-min') || $(this).parents('.option-group').data('shared-max') ) ) {
			// Not invalid yet. Check parent settings
			var field_total = 0;
			$(this).parents('.option-group').find('input[type=number]').each(function(index,input) {
				field_total += parseFloat( $(input).val() );
			});

			_validateSharedFieldTotals( $(this), field_total );
		}

		if ( $('.staggs-summary-widget').length ) {
			_updateSummary();
		}
		
		if ( $('.sgg_field_summary').length ) {
			_updateFormFieldSummary();
		}

		if ( $('#staggs-send-email').length ) {
			_updateEmailBodySummary();
		}
	});

	var timer, delay = 500;
	$(document).bind('input', '.option-group-options.measurements input[type=number]', function(e) {
		var $this = $(e.target);
		if ( ! $this.parents('.measurements').length ) {
			return;
		}

		var group = $this.attr('name');
		if ( $this.parents('.measurements').find('input[type=hidden]').length ) {
			group = $this.parents('.option-group-options').find('input[type=hidden]').attr('id');
		}
		if ( $this.parents('.option-group-options').data('price-key') ) {
			group = $this.parents('.option-group-options').data('price-key').toString();
		}
		var stepId = $this.parents('.option-group').data('step');

		clearTimeout(timer);

		if ( $this.parents('.option-group').hasClass('sgg-init') ) {
			_updateMeasurementPrices( $this );

			_recalculateMeasurementPricings( $this, group, stepId );

			$this.parents('.option-group').removeClass('sgg-init')
		} else {
			timer = setTimeout(function() {
				_updateMeasurementPrices( $this );

				_recalculateMeasurementPricings( $this, group, stepId );
			}, delay );
		}
	});

	$(document).on('input', '.text-input input[data-preview-index], .text-input textarea[data-preview-index], .measurements input[data-preview-index]', function () {
		// Sync texts.
		_syncTextInputs($(this));
	});

	$(document).on('input', '.image-input input', function () {
		// Sync image previews.
		_syncImageInputs(this);

		if ( $('.staggs-summary-widget').length ) {
			_updateSummary();
		}

		if ( $('.sgg_field_summary').length ) {
			_updateFormFieldSummary();
		}

		if ( $('#staggs-send-email').length ) {
			_updateEmailBodySummary();
		}
	});

	$(document).on('click', '.image-input .remove-input-image', function () {
		var file_val = $(this).parents('.image-input-field').find('input[type=hidden]').val();
		var fileinput = $(this).parents('.image-input-field').find('input[type=file]').get(0);
		var file_ext  = file_val.split('.').at(-1);

		if ( ( fileinput.files[0] && ! _isImageFile( fileinput.files[0] ) ) || ( file_val && ! _isImageExt( file_ext ) ) ) {
			// Delete file from server again when removed.
			var $input = $(this);
			$.ajax({
				type: 'post',
				url: AJAX_URL,
				data: {
					action: 'staggs_clear_uploaded_file',
					details: file_val,
				},
				success: function (data) {
					$input.parents('.image-input-field').find('input[type=hidden]').val('');
				},
				error: function(err) {
					console.error('Could not delete file');
				}
			});
		}

		// Remove image preview.
		if ( $('.image-input-field input[data-field-key="' + $(this).data('delete') + '"]').length ) {
			var input = $('.image-input-field input[data-field-key="' + $(this).data('delete') + '"]');
			_removeImageInput(input);
		}

		// Remove texture preview.
		if ( $('.image-input-field input[data-material-key="' + $(this).data('delete') + '"]').length ) {
			var input = $('.image-input-field input[data-material-key="' + $(this).data('delete') + '"]');
			_removeImageInput(input);
		}

		// Remove image preview (no preview).
		if ( $('.image-input-field input[data-step-id="' + $(this).data('delete') + '"]').length ) {
			var input = $('.image-input-field input[data-step-id="' + $(this).data('delete') + '"]');
			_removeImageInput(input);
		}

		if ( $('.staggs-summary-widget').length ) {
			_updateSummary();
		}

		if ( $('.sgg_field_summary').length ) {
			_updateFormFieldSummary();
		}

		if ( $('#staggs-send-email').length ) {
			_updateEmailBodySummary();
		}
	});

	/**
	 * Product inputs
	 */

	$(document).on('click', '.option-group-options.products input[type=number]', function () {
		$(this).select();
	});

	$(document).on('input', '.option-group-options.products input[type=number]', function () {
		var group = $(this).attr('id');
		var preview = $(this).data('preview-urls');
		var order = $(this).parents('.option-group').data('preview-order');
		var qty = $(this).val() ? parseInt( $(this).val() ) : 0;
		var max = $(this).attr('max') ? parseInt( $(this).attr('max') ) : null;
		var price;

		if ( max ) {
			if ( qty > max ) {
				$(this).addClass('invalid');
			} else {
				$(this).removeClass('invalid');
				$(this).parents('.option-group').find('.sgg-error').remove();
			}
		}

		if ( qty > 0 ) {
			$(this).parents('.sgg-product').addClass('selected');
		} else {
			$(this).parents('.sgg-product').removeClass('selected');
		}

		if ( $(this).data('price') ) {
			price = parseFloat($(this).data('price'));
		}

		if (preview && qty > 0) {
			if ( $(this).data('order') ) {
				order = $(this).data('order');
			}
			_buildPreviewGallery(preview, group, order);
		} else {
			_deletePreviewGallery(preview, group);
		}

		if ( ( $(this).attr('required') || $(this).val() ) &&
			( $(this).parents('.option-group').data('shared-min') || $(this).parents('.option-group').data('shared-max') ) ) {
			// Not invalid yet. Check parent settings
			var field_total = 0;
			$(this).parents('.option-group').find('input[type=number]').each(function(index,input) {
				field_total += parseInt( $(input).val() );
			});
			$(this).parents('.option-group').find('input[type=checkbox]:checked').each(function(index,input) {
				field_total++;
			});

			_validateSharedFieldTotals( $(this), field_total );
		}
		
		if (price) {
			if (qty > 0) {
				pricetotal[group] = price * qty;
				altpricetotal[group] = parseFloat($(this).data('alt-price')) * qty;
			} else {
				delete pricetotal[group];
				delete altpricetotal[group];
			}
		}

		_setTotals();
	});

	$(document).on('change', '.option-group-options.products input[type=checkbox]', function () {
		var group = $(this).attr('id');
		var preview = $(this).data('preview-urls');
		var order = $(this).parents('.option-group').data('preview-order');
		var qty = $(this).val();
		var price;

		if ( $(this).data('price') ) {
			price = parseFloat($(this).data('price'));
		}

		if ( $(this).is(':checked') ) {
			$(this).parents('.sgg-product').addClass('selected');
		} else {
			$(this).parents('.sgg-product').removeClass('selected');
		}

		if (preview && $(this).is(':checked')) {
			if ( $(this).data('order') ) {
				order = $(this).data('order');
			}
			_buildPreviewGallery(preview, group, order);
		} else {
			_deletePreviewGallery(preview, group);
		}

		if ( ( $(this).attr('required') || $(this).val() ) &&
			( $(this).parents('.option-group').data('shared-min') || $(this).parents('.option-group').data('shared-max') ) ) {
			// Not invalid yet. Check parent settings
			var field_total = 0;
			$(this).parents('.option-group').find('input[type=number]').each(function(index,input) {
				field_total += parseInt( $(input).val() );
			});
			$(this).parents('.option-group').find('input[type=checkbox]:checked').each(function(index,input) {
				field_total++;
			});

			_validateSharedFieldTotals( $(this), field_total );
		}
		
		if (price) {
			if ( $(this).is(':checked') ) {
				pricetotal[group] = price * qty;
				altpricetotal[group] = parseFloat($(this).data('alt-price')) * qty;
			} else {
				delete pricetotal[group];
				delete altpricetotal[group];
			}
		}

		_setTotals();
	});

	/**
	 * Helper function
	 */

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();
		var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();
		return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	if ( $('.staggs-configurator-sticky-bar').length ) {
		$(window).on('resize scroll', function() {
			if ( $('.staggs-product-options form.cart .single_add_to_cart_button').length ) {
				if ( $('.staggs-product-options form.cart .single_add_to_cart_button').isInViewport() ) {
					$('.staggs-configurator-sticky-bar').removeClass('active');
				} else {
					$('.staggs-configurator-sticky-bar').addClass('active');
				}
			}
		});
	}

	/**
	 * Configuration Form Totals submit
	 */

    $(document).on('click', '.staggs-cart-form-button #staggs-send-email[data-include_pdf]', function (e) {
		var $this = $(this);
		if ( $(this).hasClass('sgg-generate-pdf') ) {
			var finalUrl = renderFinalProductImage();
			if (finalUrl.isPromise) {
				// probably a promise
				finalUrl.image.then(function(url) {
					_clearProductBackgroundImage();
					processComplexEmailButton( $this, url );
				});
			} else {
				// definitely not a promise
				processComplexEmailButton( $this, finalUrl.image );
			}
		}
	});

    $(document).on('click', '.staggs-cart-form-button #staggs-send-email', function (e) {
		if ( $(this).hasClass('sgg-generate-pdf') ) {
			return false;
		}

		let valid = true;
		$('#configurator-options input[required], #configurator-options select[required], #configurator-options textarea[required]').each(function () {
			if ( 'radio' === $(this).attr('type') ) {
				if ( ! $(this).parents('.option-group-options').find('input:checked').length ) {
					$(this).parents('.option-group').addClass('invalid');
					valid = false;
				} else {
					$(this).parents('.option-group').removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			} else if ( 'checkbox' === $(this).attr('type') ) {
				if ( ! $(this).parents('.option-group-options').find('input:checked').length ) {
					$(this).parents('.option-group').addClass('invalid');
					valid = false;
				} else {
					$(this).parents('.option-group').removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			} else {
				if ( ! $(this).val() ) {
					$(this).addClass('invalid');
					valid = false;
				}
			}
		});

		if (!valid) {
			if ( ! SINGLE_ERROR_MESSAGE ) {
				if ( $('#configurator-options .invalid').parents('.option-group').length && ! $('#configurator-options .invalid').parents('.option-group').find('.sgg-error').length ) {
					$('#configurator-options .invalid').parents('.option-group').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
				if ( ! $('#configurator-options .invalid .option-group-content .sgg-error').length ) {
					$('#configurator-options .invalid .option-group-content').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
			} else {
				alert(REQUIRED_MESSAGE);
			}

			if ( $('.sgg-error').length ) {
				$('html, body').animate({
					scrollTop: $('.sgg-error').offset().top - 300
				}, 500);
			}

			return false;
		}

		if ( $('#configurator-options .invalid').length ) {
			if ( ! SINGLE_ERROR_MESSAGE ) {
				if ( $('#configurator-options .invalid').parents('.option-group').length && ! $('#configurator-options .invalid').parents('.option-group').find('.sgg-error').length ) {
					$('#configurator-options .invalid').parents('.option-group').append('<small class="sgg-error">' + INVALID_FIELD_MESSAGE + '</small>');
				} 
				if ( ! $('#configurator-options .invalid .option-group-content .sgg-error').length ) {
					$('#configurator-options .invalid .option-group-content').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
			} else {
				alert(INVALID_MESSAGE);
			}

			if ( $('.sgg-error').length ) {
				$('html, body').animate({
					scrollTop: $('.sgg-error').offset().top - 300
				}, 500);
			}

			return false;
		}
	});

    $(document).on('click', '.staggs-configurator-sticky-bar .single_add_to_cart_button', function (e) {
		e.preventDefault();
		$('.staggs-configurator-main').find('form.cart .single_add_to_cart_button').click();
	});

    $('.staggs-cart-form-button .single_add_to_cart_button').off('click').on('click', function (e) {
		if ( $(this).parents('form.cart').find('.staggs-configure-product-button').length && ! $(this).hasClass('staggs-popup-cart-action') ) {
			return;
		}

		e.preventDefault();

		var $thisbutton = $(this);

		if ( $(this).parents('.staggs-summary-total-buttons').length ) {

			/**
			 * Summary page totals.
			 */

			var data = {
				action: 'add_product_to_cart',
				product_id: shared_config.product,
				options: shared_config.values,
				product_price: shared_config.total,
			};
	
			var quantity = _getConfiguratorQuantity();
			if ( quantity ) {
				data.quantity = quantity;
			}

			if ( CAPTURE_PREVIEW_IMAGE ) {
				data.product_image = shared_config.image;
			}

			processCartForm( $thisbutton, data );

		} else {

			/**
			 * Configurator page.
			 */

			if ( POPUP_UPDATE_PAGE ) {
				if ( $(this).parents('.staggs-configurator-popup').length ) {
					_updateProductPageDetails();
					return;
				}
			}

			let valid = _validateConfiguratorForm();
			if ( ! valid ) {
				return false;
			}

			// Indicate loading screen.
			$('.staggs-product-options').addClass('loading');

			var $form = $('body').find('form.cart'),
				defaultValues = $form.serializeArray(),
				optionValues  = getConfiguratorOptionValues(),
				total = getConfiguratorTotals();

			var data = {
				action: 'add_product_to_cart',
				options: optionValues,
				product_price: total,
			};

			for (var df = 0; df < defaultValues.length; df++) {
				if ( defaultValues[df].name.indexOf('[]') !== -1 ) {
					if ( ! data[ defaultValues[df].name ] ) {
						data[ defaultValues[df].name ] = [ defaultValues[df].value ];
					} else {
						data[ defaultValues[df].name ].push( defaultValues[df].value );
					}
				} else {
					data[ defaultValues[df].name ] = defaultValues[df].value;
				}
			}

			var quantity = _getConfiguratorQuantity();
			if ( quantity ) {
				data.quantity = quantity;
			}

			if ( ! CAPTURE_PREVIEW_IMAGE ) {
				processCartForm( $thisbutton, data );
			} else {
				var finalUrl = renderFinalProductImage();
				if (finalUrl.isPromise) {
					// probably a promise
					finalUrl.image.then(function(url) {
						data.product_image = url;
						_clearProductBackgroundImage();
						processCartForm( $thisbutton, data );
					});
				} else {
					// definitely not a promise
					data.product_image = finalUrl.image;
					processCartForm( $thisbutton, data );
				}
			}
		}

        return false;
	});

	$('body').on( 'added_to_cart', function($thisbutton, fragments, cart_hash) {
		if ( 'no' === REDIRECT_TO_CART ) {
			if ( $('.staggs-message-wrapper').length ) {
				// Show notices.
				$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
				$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html(fragments.notices_html);
				$('.staggs-message-wrapper').addClass('active');

				$(document).find('.staggs-message-wrapper .woocommerce-message').append('<a href="#0" class="hide-notice"></a>');

				if (noticeTimeout) {
					clearTimeout(noticeTimeout);
				}
				noticeTimeout = setTimeout(function() {
					$('.staggs-message-wrapper').removeClass('active');
					$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html('');
				}, 12000);
			} else {
				$('body').find('.woocommerce-notices-wrapper').html(fragments.notices_html);
			}
		} else {
			window.location.href = CART_URL;
		}
	});

	$(document).on('click', '#staggs-show-summary', function (e) {
		e.preventDefault();

		if ( POPUP_UPDATE_PAGE ) {
			if ( $(this).parents('.staggs-configurator-popup').length ) {
				_updateProductPageDetails();
				return;
			}
		}

		var valid = _validateConfiguratorForm();
		if ( ! valid ) {
			return false;
		}

		var summary_details = {
			product: $(this).data('product'),
			values: [],
			image: '',
			progress: '',
			total: '',
		};
		summary_details.values = getConfiguratorOptionValues();
		summary_details.total = getConfiguratorTotals();

		if ( $('.option-group-step-buttons').length && $('.option-group-step').length > 1 ) {
			summary_details.progress = {
				current: activeStep,
				visited: visitedStep,
				max: maxStep,
			};
		}

		if ( $('#preview_slide_0').length ) {
			modernScreenshot.domToPng(document.getElementById('preview_slide_0'))
				.then(function(dataUrl) {
					summary_details.image = dataUrl;
					_saveAndShowSummary(summary_details);
				})
				.catch(function (error) {
					console.error('oops, something went wrong!', error);
				});
		} else if ( $('.staggs-view-gallery model-viewer').length ) {
			summary_details.image = document.getElementById('product-model-view').toDataURL();
			_saveAndShowSummary(summary_details);
		}
	});

	$('#request-invoice button, #invoice button').on('click', function (e) {
		e.preventDefault();

		if ( 'download_pdf' === $(this).attr('id') ) {
			return;
		}

		if ( POPUP_UPDATE_PAGE ) {
			_updateProductPageDetails();
			return;
		}

		var valid = _validateConfiguratorForm();
		if (!valid) {
			return false;
		}

		// Indicate loading screen.
		$('.staggs-product-options').addClass('loading');

		var $button = $(this);
		var finalUrl = renderFinalProductImage();
		if (finalUrl.isPromise) {
			// probably a promise
			finalUrl.image.then(function(url) {
				_clearProductBackgroundImage();
				processForm( $button, url );
			});
		} else {
			// definitely not a promise
			processForm( $button, finalUrl.image );
		}
	});

	$('#request-invoice #download_pdf, #invoice #download_pdf, .preview-action.download-pdf').on('click', function (e) {
		e.preventDefault();

		var valid = _validateConfiguratorForm();
		if (!valid) {
			return false;
		}

		// Indicate loading screen.
		$('.staggs-product-options').addClass('loading');

		var $button = $(this);
		var finalUrl = renderFinalProductImage();
		if (finalUrl.isPromise) {
			// probably a promise
			finalUrl.image.then(function(url) {
				_clearProductBackgroundImage();
				processPdfForm( $button, url );
			});
		} else {
			// definitely not a promise
			processPdfForm( $button, finalUrl.image );
		}
	});

	$('#staggs_pdf_invoice').on('submit', function (e) {
		e.preventDefault();

		if ( POPUP_UPDATE_PAGE ) {
			_updateProductPageDetails();
			return;
		}

		var valid = _validateConfiguratorForm();
		if ( ! valid ) {
			return valid;
		}

		var pdfEmail = '';
		if ( $(this).find('input[name="generate_pdf_email"]').length ) {
			if ( '' === $(this).find('input[name="generate_pdf_email"]').val() ) {
				$(this).find('input[name="generate_pdf_email"]').addClass('invalid');
				valid = false;
			}

			const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    		valid = re.test( String( $(this).find('input[name="generate_pdf_email"]').val() ).toLowerCase() );

			if ( valid ) {
				pdfEmail = $(this).find('input[name="generate_pdf_email"]').val();
			}
		}

		if (!valid) {
			if ( ! SINGLE_ERROR_MESSAGE ) {
				if ( ! $('#configurator-options .invalid').parents('.option-group').find('.sgg-error').length ) {
					$('#configurator-options .invalid').parents('.option-group').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
			} else {
				alert(REQUIRED_MESSAGE);
			}
			return false;
		}

		// Indicate loading screen.
		$('.staggs-product-options').addClass('loading');

		var $button = $(this);
		var finalUrl = renderFinalProductImage();
		if (finalUrl.isPromise) {
			// probably a promise
			finalUrl.image.then(function(url) {
				_clearProductBackgroundImage();
				processPdfForm( $button, url, pdfEmail );
			});
		} else {
			// definitely not a promise
			processPdfForm( $button, finalUrl.image, pdfEmail );
		}
	});

	$(document).on('click', '.staggs-summary-template .staggs-back-configurator', function(e) {
		e.preventDefault();

		if ( $(this).parents('.staggs-inline-page-summary').length ) {

			$(this).parents('.staggs-inline-page-summary').addClass('hidden');
			$(this).parents('.staggs-product-options').find('.option-group-wrapper').removeClass('hidden');

		} else {

			$('.staggs-summary-template').remove();
			$('.staggs-configurator-main').removeClass('summary-visible');

			if ( $('.staggs-configurator-steps-nav').length ) {
				stepNavSwiper = new Swiper('.staggs-configurator-steps-nav', {
					slidesPerView: 'auto',
					initialSlide: 0,
					spaceBetween: 10,
				});

				var slidesWidth = 0;
				$('.staggs-configurator-steps-nav').find('.swiper-slide').each(function(index,slide){
					slidesWidth += $(slide).width();
				});

				if ( slidesWidth < $('.staggs-configurator-steps-nav').width() ) {
					$('.staggs-configurator-steps-nav').find('.swiper-wrapper').addClass('centered');
				}
			}

			if ( $('.staggs-configurator-main .swiper-options-nav').length ) {
				optionNavSwiper = new Swiper('.swiper-options-nav', {
					slidesPerView: 'auto',
					initialSlide: 0,
					spaceBetween: 10,
				});
			}
		}

		var base_url = window.location.origin + window.location.pathname;
		window.history.pushState('', '', base_url);
	});

	$(document).on('click', '.staggs-preview-actions button.wishlist-toggle', function (e) {
		e.preventDefault();

		var $button = $(this);
		var wishlist_details = {
			product: $(this).data('product'),
			values: [],
			image: '',
			progress: '',
			total: '',
		};
		wishlist_details.values = getConfiguratorOptionValues();
		wishlist_details.total = getConfiguratorTotals();

		if ( $('.option-group-step-buttons').length && $('.option-group-step').length > 1 ) {
			wishlist_details.progress = {
				current: activeStep,
				visited: visitedStep,
				max: maxStep,
			};
		}

		if ( $('#preview_slide_0').length ) {
			modernScreenshot.domToPng(document.getElementById('preview_slide_0'))
				.then(function(dataUrl) {
					wishlist_details.image = dataUrl;

					_saveToWishlist(wishlist_details, $button);
				})
				.catch(function (error) {
					console.error('oops, something went wrong!', error);
				});

		} else if ( $('.staggs-view-gallery model-viewer').length ) {
			wishlist_details.image = document.getElementById('product-model-view').toDataURL();

			_saveToWishlist(wishlist_details, $button);
		}
	});

	$('#save-configuration button, .staggs-preview-actions button.share-link').on('click', function (e) {
		e.preventDefault();

		var iconHtml = '';
		var $button  = $(this);
		if ( $(this).find('.link-icon').length && SGG_LOADER_ICON ) {
			iconHtml = $(this).find('.link-icon').html();
			$(this).find('.link-icon').html(SGG_LOADER_ICON);
		}

		var configuration = {
			values: [],
			progress: '',
		};
		configuration.values = getConfiguratorOptionValues();

		if ( $('.option-group-step-buttons').length && $('.option-group-step').length > 1 ) {
			configuration.progress = {
				current: activeStep,
				visited: visitedStep,
				max: maxStep,
			};
		}

		var json = JSON.stringify(configuration);

		$.ajax({
			type: 'post',
			url: AJAX_URL,
			data: {
				action: 'staggs_save_configuration_to_file',
				contents: json,
			},
            success: function (data) {
				var response = JSON.parse(data);
				var product_base_url = window.location.origin + window.location.pathname;
				var options = '?configuration=' + response.filename;

				// Show message.
				if ( $('.staggs-message-wrapper').length ) {
					$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html(
						'<div class="woocommerce-message" role="alert"><a href="' + product_base_url + options + '" tabindex="1" id="copylink" class="button wc-forward">' + COPY_NOTICE_BUTTON_TEXT + '</a>' + COPY_NOTICE_MESSAGE + '<a href="#0" class="hide-notice"></a></div>'
					);
					$('.staggs-message-wrapper').addClass('active');
				} else {
					$('.woocommerce-notices-wrapper').html(
						'<div class="woocommerce-message" role="alert"><a href="' + product_base_url + options + '" tabindex="1" id="copylink" class="button wc-forward">' + COPY_NOTICE_BUTTON_TEXT + '</a>' + COPY_NOTICE_MESSAGE + '<a href="#0" class="hide-notice"></a></div>'
					);
				}

				if (noticeTimeout) {
					clearTimeout(noticeTimeout);
				}
				noticeTimeout = setTimeout(function() {
					$('.staggs-message-wrapper').removeClass('active');
					$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html('');
				}, 12000);

				if ( $button.find('.link-icon').length && SGG_LOADER_ICON ) {
					$button.find('.link-icon').html(iconHtml);
				}
			}
		});
	});

	$(document).on('click', '.staggs-summary-template #download-summary-pdf', function(e) {
		e.preventDefault();

		var pdf_details = {
			product_id: shared_config.product,
			configuration: shared_config.values,
			product_image: shared_config.image,
			product_price: shared_config.total,
		};

		$.ajax({
			type: 'post',
			url: AJAX_URL,
			data: {
				action: 'staggs_download_configuration_pdf',
				pdf: pdf_details,
			},
            success: function (response) {
				// PDF data
				const response_body = response;
				// Extract the base64-encoded part
				const base64Data = response_body.file.split(',')[1];
				const binaryPdfData = atob(base64Data);
				const uint8Array = new Uint8Array(binaryPdfData.length);
				for (let i = 0; i < binaryPdfData.length; i++) {
					uint8Array[i] = binaryPdfData.charCodeAt(i);
				}
				const pdfBlob = new Blob([uint8Array], { type: 'application/pdf' });
				// Create a URL for the Blob
				const pdfUrl = URL.createObjectURL(pdfBlob);
				const downloadLink = document.createElement('a');
				downloadLink.href = pdfUrl;
				downloadLink.download = response_body.file_name;
				document.body.appendChild(downloadLink);
				downloadLink.click();
				document.body.removeChild(downloadLink);
				URL.revokeObjectURL(pdfUrl);
				// Indicate loading screen.
				$('.staggs-product-options').removeClass('loading');
			}
		});
	})

	$('.staggs-preview-actions button.reset-toggle').on('click', function (e) {
		e.preventDefault();

		var iconHtml = '';
		if ( $(this).find('.reset-icon').length && SGG_LOADER_ICON ) {
			iconHtml = $(this).find('.reset-icon').html();
			$(this).find('.reset-icon').html(SGG_LOADER_ICON);
		}

		// Clear all options

		$('.option-group input').each(function(index,input) {

			if ( $(input).parents('.option-group').data('price-key') ) {
				var group = $(input).parents('.option-group').data('price-key').toString();
			} else {
				var group = $(input).attr('name');
			}
			
			if ( $(input).parents('.image-input-field').length ) {

				$(input).parents('.image-input-field').find('.remove-input-image').trigger('click');

			} else if ( 'radio' === $(input).attr('type') || 'checkbox' === $(input).attr('type') ) {

				var defval = false;
				if ( $(input).data('default') ) {
					defval = true;
				}

				$(input).prop('checked', defval);
				$(input).attr('checked', defval);
				$(input).trigger('change');

				if ( ! defval && $(input).parents('.option-group-content').find('.option-group-summary').length ) {
					$(input).parents('.option-group-content').find('.option-group-summary .name').text('');
					$(input).parents('.option-group-content').find('.option-group-summary .value').text('');
				}

			} else {

				var defval = '';
				if ( $(input).data('default') ) {
					defval = $(input).data('default');
				}

				$(input).val(defval);
				$(input).trigger('input');
			}

			if ( pricetotal[group] ) {
				delete pricetotal[group];
				delete altpricetotal[group];
			}

			var groupName = $(this).attr('name');
			if ($(this).attr('type') === 'checkbox' ) {
				groupName = $(this).attr('id');
			}

			if ( TRACK_GLOBAL_OPTIONS && sessionStorage.getItem( PRODUCT_ID + '_sgg_' + groupName + '_option') ) {
				sessionStorage.removeItem(PRODUCT_ID + '_sgg_' + groupName + '_option');
			}
		});

		$('.option-group select').each(function(index,select) {
			if ( $(select).find('option[data-default]').length ) {
				var option = $(select).find('option[data-default]');
			} else {
				var option = $(select).find('option:not(:disabled):first-of-type');
			}

			$(select).find('option').prop('selected', false);
			if ( $(option).attr('value') ) {
				$(select).val( $(option).attr('value') );
			} else {
				$(select).val( '' );
			}
			$(select).trigger('change');

			if ( $(select).next('.ui-selectmenu-button').length ) {
				$(select).selectmenu('refresh', true);
			}
			
			var groupName = $(this).attr('name');
			if ( TRACK_GLOBAL_OPTIONS && sessionStorage.getItem( PRODUCT_ID + '_sgg_' + groupName + '_option') ) {
				sessionStorage.removeItem(PRODUCT_ID + '_sgg_' + groupName + '_option');
			}
		});

		// Reset step.
		minStep = 1;
		activeStep = 1;
		visitedStep = 1;

		_setActiveStep();

		// Reset gallery

		if ( $('.staggs-view-gallery .staggs-view-gallery__image').length ) {
			$('.staggs-view-gallery .staggs-view-gallery__image').each(function(index,gallery_image) {
				$(gallery_image).find('img').attr('src', $(gallery_image).data('base-url'));
				$(gallery_image).find('img').attr('id', $(gallery_image).data('base-id'));
			});
		}

		// Reinit options

		if ( $('.option-group .option-group-options').length ) {
			$('.option-group .option-group-options').each(function (index, item) {
				setActiveStepOptions(item);
			});
		}

		if ( $('.option-group-options input[data-type=range]').length ) {
			$('.option-group-options input[data-type=range]').each(function(item,range) {
				_initRangeSlider( $(range) );
			});
		}

		if ( $('.option-group-options input[data-preview-index]').length ) {
			$('.option-group-options input[data-preview-index]').each(function(index,input) {
				if ( 'file' !== $(input).attr('type') ) {
					_syncTextInputs($(input));
				}
			});
		}

		_setTotals();

		if ( $(this).find('.reset-icon').length && SGG_LOADER_ICON ) {
			$(this).find('.reset-icon').html(iconHtml);
		}
	});

	$(document).on('click', '.staggs-summary-template .share-link', function(e) {
		e.preventDefault();
		$(this).siblings('input').select();
		$(this).siblings('input').focus();
		document.execCommand("copy");
	});

	$(document).on('click', '.staggs-message-wrapper .hide-notice', function() {
		$('.staggs-message-wrapper').removeClass('active');
		$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html('');
		clearTimeout(noticeTimeout);
	});

	$(document).on('click', '#copylink', function(e) {
		e.preventDefault();

		var $temp = $("<input>");
		var link = $(e.target).attr('href');

		$("body").append($temp);
		$temp.val(link).select();
		document.execCommand("copy");
		$temp.remove();

		$(e.target).text(COPY_NOTICE_BUTTON_COPIED);
	});

	$(document).on('input', '.option-group.total input.qty', function() {
		// Check quantity 
		if ( $('.option-group-options[data-table-x="quantity"]').length ) {
			_recalculateMeasurementPricings( $(this), 'quantity', 999999 );
		}

		if ( $('.option-group-options[data-table-y="quantity"]').length ) {
			_recalculateMeasurementPricings( $(this), 'quantity', 999999 );
		}

		if ( $('.option-group-options[data-formula*="quantity"]').length ) {
			$('.option-group-options[data-formula*="quantity"]').each(function(index,optiongroup) {
				$(optiongroup).find('input').trigger('input');
			});
		}

		_setTotals();
	});

	/**
	 * Helper functions
	 */

	function _recalculateMeasurementPricings( $option, group, stepId ) {
		var name = $option.attr('name');

		$('#configurator-options .option-group-options[data-formula*="' + group + '"]').each(function(index,optiongroup) {
			if ( stepId !== $(optiongroup).parents('.option-group').data('step') ) {
				if ( ! $(optiongroup).find('input[name="' + name + '"]').length && ! $(optiongroup).find('select[name="' + name + '"]').length ) {
					if ( $(optiongroup).find('input[type=radio]').length || $(optiongroup).find('input[type=checkbox]').length ) {
						$(optiongroup).find('input').trigger('change');
					} else if ( $(optiongroup).find('select').length ) {
						$(optiongroup).find('select').trigger('change');
					} else {
						$(optiongroup).find('input').trigger('input');
					}
				}
			}
		});

		$('#configurator-options .option-group-options[data-table-x="' + group + '"]').each(function(index,optiongroup) {
			if ( stepId !== $(optiongroup).parents('.option-group').data('step') ) {
				if ( ! $(optiongroup).find('input[name="' + name + '"]').length && ! $(optiongroup).find('select[name="' + name + '"]').length ) {
					if ( $(optiongroup).find('input[type=radio]').length || $(optiongroup).find('input[type=checkbox]').length ) {
						$(optiongroup).find('input').trigger('change');
					} else if ( $(optiongroup).find('select').length ) {
						$(optiongroup).find('select').trigger('change');
					} else {
						$(optiongroup).find('input').trigger('input');
					}
				}
			}
		});

		$('#configurator-options .option-group-options[data-table-y="' + group + '"]').each(function(index,optiongroup) {
			if ( stepId !== $(optiongroup).parents('.option-group').data('step') ) {
				if ( ! $(optiongroup).find('input[name="' + name + '"]').length && ! $(optiongroup).find('select[name="' + name + '"]').length ) {
					if ( $(optiongroup).find('input[type=radio]').length || $(optiongroup).find('input[type=checkbox]').length ) {
						$(optiongroup).find('input').trigger('change');
					} else if ( $(optiongroup).find('select').length ) {
						$(optiongroup).find('select').trigger('change');
					} else {
						$(optiongroup).find('input').trigger('input');
					}
				}
			}
		});
	}

	function _updateMeasurementPrices( $input, group = '' ) {
		var formula = $input.parents('.option-group-options').data('formula');
		var matrix  = $input.parents('.option-group-options').data('table');

		if ( $input.parents('.measurements').find('input[type=hidden]').length ) {
			group = $input.parents('.option-group-options').find('input[type=hidden]').attr('id');
		}
		if ( $input.parents('.option-group-options').data('price-key') ) {
			group = $input.parents('.option-group-options').data('price-key').toString();
		}

		if (matrix) {
			var val_x, val_y;
			var key_x = $input.parents('.option-group-options').data('table-x');
			var key_y = $input.parents('.option-group-options').data('table-y');
			var type_x = $input.parents('.option-group-options').data('table-type-x');
			var type_y = $input.parents('.option-group-options').data('table-type-y');
			var table_type = $input.parents('.option-group-options').data('table-type');
			var table_round = $input.parents('.option-group-options').data('table-round');
			var table_range = $input.parents('.option-group-options').data('table-range');
			var minprice = $input.parents('.option-group-options').data('table-min');
			var matrixsale  = $input.parents('.option-group-options').data('table-sale');

			$input.parents('.option-group-options').find('input[type=number]').each(function (index, input) {
				var key = $(input).data('field-key');
				var val = $(input).val();

				if ( key === key_x ) {
					val_x = val;
				} else if ( key === key_y ) {
					val_y = val;
				}
			});

			if ( ! val_x ) {
				if ( $('[name="' + key_x + '"]:checked').length ) {
					val_x = $('[name="' + key_x + '"]:checked').val();
				} else {
					val_x = $('[name="' + key_x + '"]').val();
				}
			}
			if ( ! val_y ) {
				 if ( $('[name="' + key_y + '"]:checked').length ) {
					val_y = $('[name="' + key_y + '"]:checked').val();
				} else {
					val_y = $('[name="' + key_y + '"]').val();
				}
			}

			// Indicate loading screen.
			$input.parents('.option-group-content').addClass('loading');

			_getMatrixPriceTableValue(matrix, val_x, val_y, type_x, type_y, table_round, table_range, minprice, matrixsale).then(function(response) {
				if ( response.toString().includes('|') ) {
					var prices = response.split('|');
					var unit_price = parseFloat( prices[1] );
					var alt_unit_price = parseFloat( prices[0] );
				} else {
					var unit_price = parseFloat( response );
					var alt_unit_price = parseFloat( response );
				}

				// Remove loading screen.
				$input.parents('.option-group-content').removeClass('loading');
				
				// Only evaluate if unit price exists.
				if ( unit_price ) {
					if ( 'lookup' == table_type ) {
						var price = unit_price;
						var altprice = alt_unit_price;
					} else if ('doublemultiply' == table_type) {
						var price = (val_x * val_y) * unit_price;
						var altprice = (val_x * val_y) * alt_unit_price;
					} else {
						var price = (val_x * unit_price) + (val_y * unit_price);
						var altprice = (val_x * unit_price) + (val_y * alt_unit_price);
					}
				} else {
					var price = 0;
					var altprice = 0;
				}

				if ( price < minprice ) {
					price = minprice;
				}
				if ( altprice < minprice ) {
					altprice = minprice;
				}

				$input.parents('.option-group-options').find('input[type=hidden]').val(price);

				if ( $input.parents('.option-group-options').data('price-label-pos') === 'inside' ) {
					if ( SHOW_PRODUCT_PRICE ) {
						var price_html = _formatPriceOutput(price);
						if ( altprice ) {
							price_html += '<del>' + _formatPriceOutput(altprice) + '</del>';
						}
						$input.parents('.option-group').find('.option-group-summary').find('.value').html(price_html);
					} else {
						$input.parents('.option-group').find('.option-group-summary').find('.value').html('');
					}
				} else {
					if ( SHOW_PRODUCT_PRICE ) {
						var price_html = _formatPriceOutput(price);
						if ( altprice !== price ) {
							price_html += '<del>' + _formatPriceOutput(altprice) + '</del>';
						}
						$input.parents('.option-group').find('.option-group-price').html(price_html);
					} else {
						$input.parents('.option-group').find('.option-group-price').html('');
					}
				}
				
				pricetotal[group] = price;
				altpricetotal[group] = altprice;

				if ( formula ) {
					_updateFormulaPrices( $input, group );
				}

				_setTotals();
			});

		} else if (formula) {
			
			_updateFormulaPrices( $input, group )
			
		} else {

			var unit  = $input.data('unit-price');
			var table = $input.data('table-price');
			var fixed  = $input.data('price');
			var val   = $input.val();
			var price = 0;

			if ( $input.data('field-key') ) {
				var group = $input.data('field-key');
			} else {
				var group = $input.attr('name');
			}

			if ( unit || fixed ) {
				if ( unit ) {
					price = parseFloat( unit * val );
				} else if ( fixed ) {
					price = fixed;
				}

				if (isNaN(price)) {
					price = 0;
				}
			
				$input.parents('.input-field-wrapper').find('.input-price').html(_formatPriceOutput(price));

				if ( $input.val() ) {
					pricetotal[group] = price;
				} else {
					delete pricetotal[group];
				}

				_setTotals();

			} else if (table) {

				_getPriceTableValue(table, group, val).then((result) => {
					var table_result = JSON.parse(result);
					
					var table_price = parseFloat( table_result.price );
					if ( isNaN(table_price) ) {
						table_price = 0;
					}

					$input.parents('.input-field-wrapper').find('.input-price').html(_formatPriceOutput(table_price));

					if ( $input.val() ) {
						pricetotal[group] = table_price;
					} else {
						delete pricetotal[group];
					}

					if ( $('#configurator-options .option-group-options input[data-price-formula*="' + group + '"]').length ) {
						$('#configurator-options .option-group-options input[data-price-formula*="' + group + '"]').each(function(index,input) {
							var group_name = $(input).attr('name');
							if ( 'checkbox' === $(input).attr('type') ) {
								group_name = $(input).attr('id');
							}
							_setSingleOptionFormulaPrice(input, group_name);
						});
					}
					else if ( $('#configurator-options .option-group-options option[data-price-formula*="' + group + '"]').length ) {
						$('#configurator-options .option-group-options option[data-price-formula*="' + group + '"]').each(function(index,option) {
							var group_name = $(option).parents('select').attr('name');
							_setSingleOptionFormulaPrice(option, group_name);
						});
					}
					
					_setTotals();
				});
			}
		}

		if ( $('#configurator-options .option-group-options input[data-price-formula*="' + group + '"]').length ) {
			$('#configurator-options .option-group-options input[data-price-formula*="' + group + '"]').each(function(index,input) {
				var group_name = $(input).attr('name');
				if ( 'checkbox' === $(input).attr('type') ) {
					group_name = $(input).attr('id');
				}
				_setSingleOptionFormulaPrice(input, group_name);
			});
		}
		else if ( $('#configurator-options .option-group-options option[data-price-formula*="' + group + '"]').length ) {
			$('#configurator-options .option-group-options option[data-price-formula*="' + group + '"]').each(function(index,option) {
				var group_name = $(option).parents('select').attr('name');
				_setSingleOptionFormulaPrice(option, group_name);
			});
		}
		
		if ( $('#configurator-options .option-group-options input[data-price-field="' + group + '"]').length ) {
			$('#configurator-options .option-group-options input[data-price-field="' + group + '"]').each(function(index,input) {
				var group_name = $(input).attr('name');
				if ( 'checkbox' === $(input).attr('type') ) {
					group_name = $(input).attr('id');
				}
				_setSingleOptionPercentagePrice(input, group_name);
			});
		}
		else if ( $('#configurator-options .option-group-options option[data-price-field="' + group + '"]').length ) {
			$('#configurator-options .option-group-options option[data-price-field="' + group + '"]').each(function(index,option) {
				var group_name = $(option).parents('select').attr('name');
				_setSingleOptionPercentagePrice(option, group_name);
			});
		}

		var parent_id = $input.parents('.option-group').attr('id');
		if ( $('#configurator-options .option-group:not(#' + parent_id + ') input[data-price-field="sgg_total_price"]').length ) {
			$('#configurator-options .option-group:not(#' + parent_id + ') input[data-price-field="sgg_total_price"]').each(function(index,input) {
				var group_name = $(input).attr('name');
				if ( 'checkbox' === $(input).attr('type') ) {
					group_name = $(input).attr('id');
				}
				_setSingleOptionPercentagePrice(input, group_name);
			});
		}
	}

	function _updateFormulaPrices( $input, group ) {
		var formula = $input.parents('.option-group-options').data('formula');
		var pending_calls = [];
		var empty_keys = [];

		$input.parents('.option-group-options').find('input[type=number]').each(function(index, input) {
			if ( $(input).data('field-key') ) {
				var key = $(input).data('field-key');
			} else {
				var key = group;
			}

			var singleprice = $(input).data('price');
			var unitprice = $(input).data('unit-price');
			var table = $(input).data('table-price');
			var val = $(input).val();

			if ( val ) {
				if ( singleprice ) {
					formula = formula.replaceAll( key, singleprice );
				} else if ( unitprice ) {
					formula = formula.replaceAll( key + '_value', val );
					formula = formula.replaceAll( key + '_price', unitprice );
					formula = formula.replaceAll( key, '(' + val + ' * ' + unitprice + ')' );
				} else if ( table ) {
					pending_calls.push( _getPriceTableValue(table, key, val) );
				} else {
					// Fallback.
					formula = formula.replaceAll( key, '0' );
				}
			} else {
				empty_keys.push(key);
			}
		});

		pricetotal = Object.keys(pricetotal).sort(function(a, b) {
			return a.length < b.length
		}).reduce((obj, key) => {
			obj[key] = pricetotal[key];
			return obj;
		}, {});

		// Pending table calls. Wait untill all have been finished.
		if ( pending_calls.length ) {
			// Indicate loading screen.
			$input.parents('.option-group-content').addClass('loading');

			Promise.all(pending_calls).then( function(results) {
				var price = 0;

				results.forEach(function(result,index) {
					var item = JSON.parse(result);

					if ( item.value ) {
						formula = formula.replaceAll( item.index, '(' + item.value + ' * ' + item.price + ')' );
					} else {
						formula = formula.replaceAll( item.index, '0' );
					}
				});

				var regExp = /([a-zA-Z]+-*_*)+/g;
				// Only evaluate formula when all letters have been replaced.
				if ( ! regExp.test( formula ) ) {
					price = eval( formula );
				} else {
					for ( var key in pricetotal ) {
						formula = formula.replaceAll( key, pricetotal[key] );
					}

					var quantity = _getConfiguratorQuantity();
					if ( quantity ) {
						formula = formula.replaceAll( 'quantity', quantity );
					}

					formula = formula.replaceAll(regExp, '0');
					price = eval( formula );
				}

				// Remove loading screen.
				$input.parents('.option-group-content').removeClass('loading');

				$input.parents('.option-group-options').find('input[type=hidden]').val(price);
				if ( $input.parents('.option-group-options').data('price-label-pos') === 'inside' ) {
					if ( SHOW_PRODUCT_PRICE ) {
						$input.parents('.option-group').find('.option-group-summary').find('.value').html(_formatPriceOutput(price));
					} else {
						$input.parents('.option-group').find('.option-group-summary').find('.value').html('');
					}
				} else {
					if ( SHOW_PRODUCT_PRICE ) {
						$input.parents('.option-group').find('.option-group-price').html(_formatPriceOutput(price));
					} else {
						$input.parents('.option-group').find('.option-group-price').html('');
					}
				}
		
				pricetotal[group] = price;
				altpricetotal[group] = price;

				_setTotals();
			});
		} else {
			var regExp = /([a-zA-Z]+-*_*)+/g;
			var price = 0;

			// Only evaluate formula when all letters have been replaced.
			if ( ! regExp.test( formula ) ) {
				price = eval( formula );
			} else {
				for ( var key in pricetotal ) {
					formula = formula.replaceAll( key, pricetotal[key] );
				}

				var quantity = _getConfiguratorQuantity();	
				if ( quantity ) {
					formula = formula.replaceAll( 'quantity', quantity );
				}

				formula = formula.replaceAll(regExp, '0');
				price = eval( formula );
			}

			$input.parents('.option-group-options').find('input[type=hidden]').val(price);
			if ( $input.parents('.option-group-options').data('price-label-pos') === 'inside' ) {
				if ( SHOW_PRODUCT_PRICE ) {
					$input.parents('.option-group').find('.option-group-summary').find('.value').html(_formatPriceOutput(price));
				} else {
					$input.parents('.option-group').find('.option-group-summary').find('.value').html('');
				}
			} else {
				if ( SHOW_PRODUCT_PRICE ) {
					$input.parents('.option-group').find('.option-group-price').html(_formatPriceOutput(price));
				} else {
					$input.parents('.option-group').find('.option-group-price').html('');
				}
			}

			pricetotal[group] = price;
			altpricetotal[group] = price;

			_setTotals();
		}
	}

	function _setOptionFormulaPrices(group_div, group_name = '') {
		if ( ! $(group_div).find('input[data-price-formula]').length ) {
			if ( $(group_div).find('option[data-price-formula]').length ) {
				$(group_div).find('option[data-price-formula]').each(function(index,option) {
					_setSingleOptionFormulaPrice(option, group_name);
				});
			}
			return;
		}

		$(group_div).find('input[data-price-formula]').each(function(index,input) {
			_setSingleOptionFormulaPrice(input, group_name);
		});
	}

	function _setSingleOptionFormulaPrice(input, group = '') {
		var price_formula = $(input).data('price-formula');

		if ( price_formula) {
			for ( var key in pricetotal ) {
				price_formula = price_formula.replaceAll( key + '_value', $('input[name="' + key + '"]').val() );
				price_formula = price_formula.replaceAll( key, pricetotal[key] );
			}

			var quantity = _getConfiguratorQuantity();	
			if ( quantity ) {
				price_formula = price_formula.replaceAll( 'quantity', quantity );
			}

			var regExp = /([a-zA-Z]+-*_*)+/g;
			if ( ! regExp.test( price_formula ) ) {
				price = eval( price_formula );
			} else {
				price_formula = price_formula.replaceAll(regExp, '0');
				price = eval( price_formula );
			}

			if (group && group in pricetotal) {
				if ( $(input).is(':checked') || $(input).is(':selected') ) {
					pricetotal[group] = price;
				}
			}

			$(input).data('price', price);
			
			if ( SHOW_PRODUCT_PRICE ) {
				$(input).parents('label').find('.box-price').html(_formatPriceOutput(price));
				$(input).parents('label').find('.option-price').html(_formatPriceOutput(price));

				if ( ( $(input).is(':checked') || $(input).is(':selected') ) && $(input).parents('.option-group').find('.option-group-summary').length ) {
					// Update active selection summary price view
					$(input).parents('.option-group').find('.option-group-summary .value').html(_formatPriceOutput(price));
				}
			}
		}
	}

	function _setOptionPercentagePrices(group_div, group_name = '') {
		if ( ! $(group_div).find('input[data-price-percent]').length ) {
			return;
		}

		$(group_div).find('input[data-price-percent]').each(function(index,input) {
			_setSingleOptionPercentagePrice(input, group_name);
		});
	}

	function _setSingleOptionPercentagePrice(input, group = '') {
		var percent = $(input).data('price-percent');
		var field = $(input).data('price-field');

		if (percent && field) {
			if ('sgg_total_price' === field) {
				var total = getConfiguratorTotals(group);
				price = total * (percent / 100);
			} else {
				for ( var key in pricetotal ) {
					field = field.replaceAll( key, pricetotal[key] );
				}
	
				var regExp = /([a-zA-Z]+-*_*)+/g;
				if ( ! regExp.test( field ) ) {
					price = field * (percent / 100);
				} else {
					price = 0;
				}
			}
			
			$(input).data('price', price);
			if ( SHOW_PRODUCT_PRICE ) {
				$(input).parents('label').find('.box-price').html(_formatPriceOutput(price));
				$(input).parents('label').find('.option-price').html(_formatPriceOutput(price));
			}

			if ( $(input).is(':checked') && group && group in pricetotal) {
				pricetotal[group] = price;
			}
		}
	}

	function _setAttributeOptionSkus(group_div) {
		var options = getConfiguratorOptionValues();
		
		$('#configurator-options input[data-sku-format]').each(function(index,item) {
			var regExp = /{(.*?)}/;
			var skuFormat = $(item).data('sku-format');
			var newSku = skuFormat;

			options.forEach((option,key) => {
				if ( option.sku && isNaN(option.value) ) {
					newSku = newSku.replace('{' + option.name + '}', option.sku);
				} else if ( option.value ) {
					newSku = newSku.replace('{' + option.name + '}', option.value);
				}
			});

			if ( ! regExp.test( newSku ) ) {
				$(item).attr('data-sku', newSku);
			}
		});

		$('#configurator-options option[data-sku-format]').each(function(index,item) {
			var regExp = /{(.*?)}/;
			var skuFormat = $(item).data('sku-format');
			var newSku = skuFormat;

			options.forEach((option,key) => {
				if ( option.sku && isNaN(option.value) ) {
					newSku = newSku.replace('{' + option.name + '}', option.sku);
				} else if ( option.value ) {
					newSku = newSku.replace('{' + option.name + '}', option.value);
				}
			});

			if ( ! regExp.test( newSku ) ) {
				$(item).attr('data-sku', newSku);
			}
		});
	}
	
	function _updateOptionPriceDisplay( $option, price_div, price ) {
		if ( $option.parents('.option-group-options').find(price_div + ' span').length ) {
			// if any option has a price set
			if (SHOW_PRODUCT_PRICE && SHOW_PRICE_DIFFERENCE) {
				$option.parents('.option-group-options').find('input').each(function(index,input) {
					if ( $(input).is(':checked') ) {
						$(input).closest('label').find(price_div).html('');
					} else {
						var optionPrice = $(input).data('price') ?? 0;
						var newPrice = optionPrice - price;
						$(input).closest('label').find(price_div).html(_formatPriceOutput(newPrice, '', false));
					}
				});
			}
		}
	}

	function _getConfiguratorQuantity() {
		var quantity = false;
		if ( $('.option-group.total[data-quantity-id]').length ) {
			var inputName = $('.option-group.total').data('quantity-id');
			quantity = $('input[name="' + inputName + '"]').val();
		} else if ( $('.option-group.total input.qty').length ) {
			quantity = $('.option-group.total input.qty').val();
		}

		return quantity;
	}

	function _getPriceTableValue(table, key, val) {
		var data = {
			action: 'get_price_table_value',
			table_id: table,
			index: key,
			value: val,
		};
		
		return $.ajax({
			type: 'post',
			url: AJAX_URL,
			data: data
		});
	}

	function _getMatrixPriceTableValue(table, val_x, val_y, type_x, type_y, table_round, table_range, minprice, table_sale) {
		var data = {
			action: 'get_matrix_price_table_value',
			table_id: table,
			value_x: val_x,
			value_y: val_y,
			type_x: type_x,
			type_y: type_y,
			table_round: table_round,
			table_range: table_range,
			minprice: minprice,
			table_sale: table_sale
		};

		return $.ajax({
			type: 'post',
			url: AJAX_URL,
			data: data
		});
	}

	function processForm( $this, imageUrl ) {
		// Quote form.
		var values = getConfiguratorOptionValues();
		var total  = getConfiguratorTotals();
		var attrValues = {};

		$this.addClass('loading');

		values.forEach(function(item,index) {
			if ( item.hidden ) {
				return;
			}

			if ( attrValues[item.name] ) {
				attrValues[item.name] = attrValues[item.name] + ', ' + item.value;
			} else {
				attrValues[item.name] = item.value;
			}
		});

		for (var [attr_name, attr_value] of Object.entries(attrValues)) {
			if ( typeof attr_value === 'object' ) {
				attr_value.forEach(function(sub_value,index) {
					$this.parents('form').append('<input type="hidden" name="' + sub_value.id + '" value="' + sub_value.value + '"/>' );
				});
			} else {
				$this.parents('form').append('<input type="hidden" name="' + attr_name + '" value="' + attr_value + '"/>' );
			}
		}

		if ( $this.attr('id') == 'download_pdf' ) {
			$this.parents('form').append('<input type="hidden" name="product_image" value="' + imageUrl + '"/>' );
		}

		if ( $this.parents('.staggs-configurator-main').find('#totalprice').length || $this.parents('.option-group.total').find('#totalprice').length ) {
			$this.parents('form').append('<input type="hidden" name="product_price" value="' + total + '"/>' );
		}

		if ( $this.data('include_pdf') || $this.data('include_image') || $this.data('include_url') ) {
			var data = {
				action: 'staggs_get_configuration_form_urls',
				values: values,
				image: imageUrl
			}
			
			if ( $this.data('include_image') ) {
				data.image_id = $this.data('include_image');
			}

			if ( $this.data('include_pdf') ) {
				data.pdf = {
					product_id: $this.data('include_pdf'),
					product_price: total,
				}
			}

			if ( $this.data('include_url') ) {
				var configuration = {
					values: values,
					progress: '',
				};

				if ( $('.option-group-step-buttons').length && $('.option-group-step').length > 1 ) {
					configuration.progress = {
						current: activeStep,
						visited: visitedStep,
						max: maxStep,
					};
				}

				var json = JSON.stringify(configuration);
				data.contents = json;
			}

			$.ajax({
				type: 'post',
				url: AJAX_URL,
				data: data,
				success: function (response) {
					var result = JSON.parse(response);

					// PDF data
					if ( $this.data('include_url') ) {
						var options_url = window.location.origin + window.location.pathname + '?configuration=' + result.url;
						$this.parents('form').append('<input type="hidden" name="product_url" value="' + options_url + '"/>' );
					}
					if ( $this.data('include_image') ) {
						$this.parents('form').append('<input type="hidden" name="product_image" value="' + result.image_url + '"/>' );
					}
					if ( $this.data('include_pdf') ) {
						$this.parents('form').append('<input type="hidden" name="product_pdf" value="' + result.pdf_url + '"/>' );
					}

					$this.parents('form').submit();
					$this.removeClass('loading');
					// Clear loading screen.
					$('.staggs-product-options').removeClass('loading');
				},
				error: function(err) {
					$this.parents('form').submit();
					$this.removeClass('loading');
					// Clear loading screen.
					$('.staggs-product-options').removeClass('loading');
				}
			});
		} else {
			$this.parents('form').submit();
			// Clear loading screen.
			$('.staggs-product-options').removeClass('loading');
		}
	}

	function processPdfForm( $form, imageUrl, pdfEmail = '' ) {
		var icon_html = '';
		if (SGG_LOADER_ICON && $form.find('.pdf-icon').length ){
			var icon_html = $form.find('.pdf-icon').html();
			$form.find('.pdf-icon').html(SGG_LOADER_ICON);
		}

		$form.find('[type=submit]').addClass('loading');

		if ( $form.parents('.staggs-summary-total-buttons').length ) {

			/**
			 * Summary page totals.
			 */
			var pdf_details = {
				product_id: shared_config.product,
				configuration: shared_config.values,
				product_price: shared_config.total,
				product_image: shared_config.image
			};
	
		} else {

			// PDF form.
			var pdf_details = {
				product_id: $form.data('product'),
				configuration: [],
				product_image: imageUrl,
				product_price: '',
			};
			pdf_details.configuration = getConfiguratorOptionValues();
			pdf_details.product_price = getConfiguratorTotals();

		}

		if ( '' !== pdfEmail) {
			pdf_details.user_email = pdfEmail;
		}

		var link_configuration = {
			values: pdf_details.configuration,
			progress: '',
		};
		if ( $('.option-group-step-buttons').length && $('.option-group-step').length > 1 ) {
			pdf_details.progress = {
				current: activeStep,
				visited: visitedStep,
				max: maxStep,
			};
		}
		pdf_details.link = JSON.stringify(link_configuration);

		$.ajax({
			type: 'post',
			url: AJAX_URL,
			data: {
				action: 'staggs_download_configuration_pdf',
				pdf: pdf_details,
			},
            success: function (response) {
				// PDF data
				const response_body = response;
				// Extract the base64-encoded part
				const base64Data = response_body.file.split(',')[1];
				const binaryPdfData = atob(base64Data);
				const uint8Array = new Uint8Array(binaryPdfData.length);
				for (let i = 0; i < binaryPdfData.length; i++) {
					uint8Array[i] = binaryPdfData.charCodeAt(i);
				}
				const pdfBlob = new Blob([uint8Array], { type: 'application/pdf' });
				// Create a URL for the Blob
				const pdfUrl = URL.createObjectURL(pdfBlob);
				const downloadLink = document.createElement('a');
				downloadLink.href = pdfUrl;
				downloadLink.download = response_body.file_name;
				document.body.appendChild(downloadLink);
				downloadLink.click();
				document.body.removeChild(downloadLink);
				URL.revokeObjectURL(pdfUrl);

				// Indicate loading screen.
				$('.staggs-product-options').removeClass('loading');
				$form.find('[type=submit]').removeClass('loading')

				if (SGG_LOADER_ICON && $form.find('.pdf-icon').length ){
					$form.find('.pdf-icon').html(icon_html);
				}
			}
		});
	}

	function processComplexEmailButton( $button, imageUrl ) {
		$button.addClass('loading');

		var values = getConfiguratorOptionValues();
		var total = getConfiguratorTotals();

		if ( $button.data('include_pdf') || $button.data('include_image') || $button.data('include_url') ) {
			var data = {
				action: 'staggs_get_configuration_form_urls',
				values: values,
				image: imageUrl
			}
			
			if ( $button.data('include_image') ) {
				data.image_id = $button.data('include_image');
			}

			if ( $button.data('include_pdf') ) {
				data.pdf = {
					product_id: $button.data('include_pdf'),
					product_price: total,
				}
			}

			if ( $button.data('include_url') ) {
				var configuration = {
					values: values,
					progress: '',
				};

				if ( $('.option-group-step-buttons').length && $('.option-group-step').length > 1 ) {
					configuration.progress = {
						current: activeStep,
						visited: visitedStep,
						max: maxStep,
					};
				}

				var json = JSON.stringify(configuration);
				data.contents = json;
			}

			$.ajax({
				type: 'post',
				url: AJAX_URL,
				data: data,
				success: function (response) {
					var result = JSON.parse(response);

					var mail_links = '';
					if ( $button.data('include_url') ) {
						var options_url = window.location.origin + window.location.pathname + '?configuration=' + result.url;
						mail_links += options_url;
					}

					if ( $button.data('include_image') ) {
						if (mail_links !== '') {
							mail_links += '%0D%0A';
						}
						mail_links += result.image_url;
					}

					if ( $button.data('include_pdf') ) {
						if (mail_links !== '') {
							mail_links += '%0D%0A';
						}
						mail_links += result.pdf_url;
					}

					_updateEmailBodySummary( mail_links );

					$button.removeClass('loading');
					window.location.href = $('#staggs-send-email').attr('href');
				},
				error: function(err) {
					$button.removeClass('loading');
					window.location.href = $('#staggs-send-email').attr('href');
				}
			});
		} else {
			$button.removeClass('loading');
			window.location.href = $('#staggs-send-email').attr('href');
		}
	}

	function processCartForm( $thisbutton, data ) {
        $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

        $.ajax({
            type: 'post',
            url: AJAX_URL,
            data: data,
            beforeSend: function (response) {
                $thisbutton.addClass('loading');
            },
            complete: function (response) {
                $thisbutton.removeClass('loading');
				$('.staggs-product-options').removeClass('loading');
            },
            success: function (response) {
                $thisbutton.removeClass('loading');
				if ( 'yes' === REDIRECT_TO_CART ) {

					window.location.href = CART_URL;

				} else if ( response.fragments && response.cart_hash ) {

                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);

					if ( $('.staggs-message-wrapper').length ) {
						$('.staggs-message-wrapper .woocommerce-notices-wrapper').html(response.fragments.notices_html);

						if ( $('.staggs-message-wrapper .woocommerce-error').length ) {
							$('.staggs-message-wrapper .woocommerce-error li').append('<a href="#0" class="hide-notice"></a>');		
						}
						if ( $('.staggs-message-wrapper .woocommerce-message').length ) {
							$('.staggs-message-wrapper .woocommerce-message').append('<a href="#0" class="hide-notice"></a>');		
						}

						$('.staggs-message-wrapper').addClass('active');
						
						if (noticeTimeout) {
							clearTimeout(noticeTimeout);
						}
						noticeTimeout = setTimeout(function() {
							$('.staggs-message-wrapper').removeClass('active');
							$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html('');
						}, 12000);
					} else {
						$('body').find('.woocommerce-notices-wrapper').html(response.fragments.notices_html);
					}
				}
            }
        });
	}

	function _validateConfiguratorForm() {
		var valid = true;

		$('#configurator-options input[required], #configurator-options select[required], #configurator-options textarea[required]').each(function () {
			if ( 'radio' === $(this).attr('type') ) {
				if ( ! $(this).parents('.option-group-options').find('input:checked').length ) {
					$(this).parents('.option-group').addClass('invalid');
					valid = false;
				} else {
					$(this).parents('.option-group').removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			} else if ( 'checkbox' === $(this).attr('type') ) {
				if ( ! $(this).parents('.option-group-options').find('input:checked').length ) {
					$(this).parents('.option-group').addClass('invalid');
					valid = false;
				} else {
					$(this).parents('.option-group').removeClass('invalid');
					$(this).parents('.option-group').find('.sgg-error').remove();
				}
			} else {
				if ( ! $(this).val() ) {
					$(this).addClass('invalid');
					valid = false;
				}
			}
		});

		if (!valid) {
			if ( ! SINGLE_ERROR_MESSAGE ) {
				if ( $('#configurator-options .invalid').parents('.option-group').length && ! $('#configurator-options .invalid').parents('.option-group').find('.sgg-error').length ) {
					$('#configurator-options .invalid').parents('.option-group').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
				if ( ! $('#configurator-options .invalid .option-group-content .sgg-error').length ) {
					$('#configurator-options .invalid .option-group-content').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
			} else {
				alert(REQUIRED_MESSAGE);
			}

			if ( $('.sgg-error').length ) {
				$('html, body').animate({
					scrollTop: $('.sgg-error').offset().top - 300
				}, 500);
			}

			valid = false;
		}

		if ( $('#configurator-options .invalid').length ) {
			if ( ! SINGLE_ERROR_MESSAGE ) {
				if ( $('#configurator-options .invalid').parents('.option-group').length && ! $('#configurator-options .invalid').parents('.option-group').find('.sgg-error').length ) {
					$('#configurator-options .invalid').parents('.option-group').append('<small class="sgg-error">' + INVALID_FIELD_MESSAGE + '</small>');
				}
				if ( ! $('#configurator-options .invalid .option-group-content .sgg-error').length ) {
					$('#configurator-options .invalid .option-group-content').append('<small class="sgg-error">' + REQUIRED_FIELD_MESSAGE + '</small>');
				}
			} else {
				alert(INVALID_MESSAGE);
			}

			if ( $('.sgg-error').length ) {
				$('html, body').animate({
					scrollTop: $('.sgg-error').offset().top - 300
				}, 500);
			}

			valid = false;
		}

		return valid;
	}

	function _validateSharedFieldTotals( $input, fieldTotal ) {
		var shared_min = $input.parents('.option-group').data('shared-min') ?? false;
		var shared_max = $input.parents('.option-group').data('shared-max') ?? false;

		if ( shared_min && fieldTotal < shared_min ) {
			$input.parents('.option-group').find('input').addClass('invalid');
		} else if ( shared_max && fieldTotal > shared_max ) {
			$input.parents('.option-group').find('input').addClass('invalid');
		} else {
			$input.parents('.option-group').find('input').removeClass('invalid');
		}
	}

	function getConfiguratorOptionValues() {
		var values = $('#configurator-options').serializeArray();
		var finalValues = [];

		values.forEach(function (item, index) {
			var repeaterId = '';

			$('input[name="' + item.name + '"]').each(function (key, input) {
				item.step_id = $(input).closest('.option-group').data('step');
				item.step_sku  = $(input).closest('.option-group').data('step-sku');
				item.step_name = $(input).closest('.option-group').data('step-name');
				item.hidden = $('input[name="' + item.name + '"]').closest('.option-group').hasClass('always-hidden');

				var $input = $(input);
				if ( $(input).attr('type') === 'hidden' ) {
					$input = $(input).closest('label').find('input[name="' + item.name + '-input"]');
				}

				if ( $(input).val() === item.value && $(input).data('option-id') ) {
					item.id        = $(input).data('option-id');
					item.step_id   = $(input).data('step-id');
				}

				if ( $(input).data('title') ) {
					item.label = $.trim( $(input).data('title') );
				} else if ( $(input).closest('.input-field-wrapper').length ) {
					item.label = $.trim( $(input).closest('.input-field-wrapper').find('.input-title').text().replace('*', '') );
				} else if ( $(input).closest('.sgg-product-info').length ) {
					item.label = $.trim( $(input).closest('.sgg-product-info').find('.sgg-product-name').text() );
				} else if ( $('div[data-step="' + item.step_id + '"] .option-group-header').find('.title').text() ) {
					item.label = $.trim( $('div[data-step="' + item.step_id + '"] .option-group-header').find('.title').text().replace('*', '') );
				}

				if ( $(input).data('product') ) {
					item.product = $(input).data('product');
				}

				if ( $(input).val() === item.value || 'file' === $(input).attr('type') ) {
					if ( $(input).siblings('.option').find('.option-note').length ) {
						item.note = $.trim( $(input).siblings('.option').find('.option-note').html() );
					} else if ( $(input).siblings('.tooltip').find('.note').length ) {
						item.note = $.trim( $(input).siblings('.tooltip').find('.note').html() );
					} else if ( $(input).data('note') ) {
						item.note = $.trim( $(input).data('note') );
					} else if ( $(input).siblings('.box').find('.box-note').length ) {
						item.note = $.trim( $(input).siblings('.box').find('.box-note').html() );
					} else if ( $(input).closest('.input-field-wrapper').find('.option-note').length ) {
						item.note = $.trim( $(input).closest('.input-field-wrapper').find('.option-note').html() );
					}
	
					if ( $input.attr('data-sku') ) {
						item.sku = $input.data('sku');
					}
					if ( $input.attr('data-weight') ) {
						item.weight = $input.data('weight');
					}
					if ( $input.attr('data-price') ) {
						item.price = $input.data('price');
					}
					if ( $input.attr('data-sale') ) {
						item.sale = $input.data('sale');
					}
					if ( $input.attr('data-product-id') ) {
						item.product_id = $input.data('product-id');
					}
					if ( $input.attr('data-product-qty') ) {
						item.product_qty = $input.data('product-qty');
					}
				}

				if ( $('input[name="' + item.name + '"]').closest('[data-repeater-list]').length ) {
					repeaterId = $('input[name="' + item.name + '"]').closest('[data-repeater-list]').data('repeater-list');
				}
			});

			$('textarea[name="' + item.name + '"]').each(function (key, textarea) {
				item.step_id = $(textarea).closest('.option-group').data('step');
				item.step_sku  = $(textarea).closest('.option-group').data('step-sku');
				item.step_name = $(textarea).closest('.option-group').data('step-name');
				item.hidden = $('textarea[name="' + item.name + '"]').closest('.option-group').hasClass('always-hidden');

				if ( $(textarea).val() === textarea.value && $(textarea).data('option-id') ) {
					item.id        = $(textarea).data('option-id');
					item.step_id   = $(textarea).data('step-id');
				}

				if ( $(textarea).data('title') ) {
					item.label = $.trim( $(textarea).data('title') );
				} else if ( $(textarea).closest('.input-field-wrapper').length ) {
					item.label = $.trim( $(textarea).closest('.input-field-wrapper').find('.input-title').text().replace('*', '') );
				} else if ( $('div[data-step="' + item.step_id + '"] .option-group-header').find('.title').text() ) {
					item.label = $.trim( $('div[data-step="' + item.step_id + '"] .option-group-header').find('.title').text().replace('*', '') );
				}

				if ( $(textarea).closest('.input-field-wrapper').find('.option-note').length ) {
					item.note = $.trim( $(textarea).closest('.input-field-wrapper').find('.option-note').html() );
				}
				if ( $(textarea).attr('data-sku') ) {
					item.sku = $(textarea).data('sku');
				}
				if ( $(textarea).attr('data-price') ) {
					item.price = $(textarea).data('price');
				}

				if ( $('textarea[name="' + item.name + '"]').closest('[data-repeater-list]').length ) {
					repeaterId = $('textarea[name="' + item.name + '"]').closest('[data-repeater-list]').data('repeater-list');
				}
			});

			$('select[name="' + item.name + '"]').each(function (key, select) {
				item.step_id = $(select).closest('.option-group').data('step');
				item.step_sku  = $(select).closest('.option-group').data('step-sku');
				item.step_name = $(select).closest('.option-group').data('step-name');

				if ($(select).find(':selected').val() === item.value && $(select).find(':selected').data('option-id')) {
					item.id = $(select).find(':selected').data('option-id');
					item.step_id = $(select).find(':selected').data('step-id');

					if ( $(select).find(':selected').data('sku') ) {
						item.sku = $(select).find(':selected').data('sku');
					}
					if ( $(select).find(':selected').data('weight') ) {
						item.weight = $(select).find(':selected').data('weight');
					}
					if ( $(select).find(':selected').data('price') ) {
						item.price = $(select).find(':selected').data('price');
					}
					if ( $(select).find(':selected').data('sale') ) {
						item.sale = $(select).find(':selected').data('sale');
					}
					if ( $(select).find(':selected').data('product-id') ) {
						item.product_id = $(select).find(':selected').data('product-id');
					}
					if ( $(select).find(':selected').data('product-qty') ) {
						item.product_qty = $(select).find(':selected').data('product-qty');
					}

					if ( $('div[data-step="' + item.step_id + '"] .option-group-header').find('.title').text() ) {
						item.label = $('div[data-step="' + item.step_id + '"] .option-group-header').find('.title').text().replace('*', '');
					}
				}
				item.hidden = $('select[name="' + item.name + '"]').parents('.option-group').hasClass('always-hidden');

				if ( $('select[name="' + item.name + '"]').parents('[data-repeater-list]').length ) {
					repeaterId = $('select[name="' + item.name + '"]').parents('[data-repeater-list]').data('repeater-list');
				}
			});

			if ( repeaterId ) {
				if (item.name.includes('[')) {
					var itemNameParts = item.name.split('[');
					var index = parseInt( itemNameParts[1].replace(']', '') );
					var itemName = itemNameParts[2].replace(']', '');

					var repeaterLabel = $.trim( $('[data-repeater-list="' + repeaterId + '"]').closest('.staggs-repeater').find('.staggs-repeater-header .title').text() );
					var repeaterItemId = repeaterId + '-' + (index + 1);

					if ( '' != item.value ) {
						item.id = item.name;
						item.name = itemName;

						var repeaterIndex = finalValues.findIndex(val => val.id === repeaterItemId);
						if ( repeaterIndex > -1 ) {
							finalValues[repeaterIndex].value.push(item);
						} else {
							finalValues.push({
								id: repeaterItemId,
								name: repeaterItemId,
								label: repeaterLabel + ' ' + (index + 1),
								value: [item]
							});
						}
					}
				}
			} else if ( '' != item.value ) {
				// Item has value.
				finalValues.push( item );
			}
		});

		return finalValues;
	}

	function getConfiguratorTotals( exclude = '' ) {
		var producttotal = 0;
		if ( USE_PRODUCT_PRICE ) {
			producttotal = PRODUCT_PRICE;
		}

		var optiontotal = 0;
		var price_keys = Object.keys(pricetotal);
		var prices = Object.values(pricetotal);
		for (var price_index in prices) {
			if ( exclude != price_keys[price_index] ) {
				optiontotal += parseFloat(prices[price_index]);
			}
		}

		var grandtotal = producttotal + optiontotal;
		if ( $('.option-group.total .button-wrapper').data('formula') ) {
			var formula = $('.option-group.total .button-wrapper').data('formula');

			formula = formula.replaceAll('product_price', producttotal);
			formula = formula.replaceAll('option_price', optiontotal);
			formula = formula.replaceAll('total_price', grandtotal);

			pricetotal = Object.keys(pricetotal).sort(function(a, b) {
				return a.length < b.length
			}).reduce((obj, key) => {
				obj[key] = pricetotal[key];
				return obj;
			}, {});

			for ( var key in pricetotal ) {
				formula = formula.replace(key, pricetotal[key]);
			}

			var regExp = /([a-zA-Z]+-*_*)+/g;
			// Only evaluate formula when all letters have been replaced.
			if ( ! regExp.test( formula ) ) {
				grandtotal = eval( formula );
			} else {
				formula = formula.replaceAll(regExp, '0');
                grandtotal = eval( formula );
            }
		}

		return grandtotal;
	}

	function renderFinalProductImage() {
		// Start image capture.
		$('.staggs-view-gallery__images').addClass('capture-picture');

		// Reset image positions if modified.
		$('.staggs-view-gallery__image .preview-image-input-wrapper').each(function(index,imageWrapper) {
			if ( $(imageWrapper).find('img').height() > $(imageWrapper).innerHeight() ) {
				$(imageWrapper).find('img').height( $(imageWrapper).innerHeight() );
				$(imageWrapper).find('.preview-image-input').css('top', '0');
			}
			if ( $(imageWrapper).find('img').width() > $(imageWrapper).width() ) {
				$(imageWrapper).find('img').width( $(imageWrapper).width() );
				$(imageWrapper).find('.preview-image-input').css('left', '0');
			}
		});

		// Get final image.
		return _getFinalProductImage();
	}

	function setActiveStepOptions(group, groupId = '', compareId = '') {
		var queryString   = window.location.search;
		var selectedValue = '';
		var groupName     = '';

		if ( '' === groupId ) {
			if ( $(group).find('input').length ) {
				groupId = $(group).find('input').data('step-id');
				groupName = $(group).find('input').attr('name');

				if ($(this).attr('type') === 'checkbox' ) {
					groupName = $(this).attr('id');
				}
			} else {
				groupId = $(group).find('select').data('step-id');
				groupName = $(group).find('select').attr('name');
			}
		}

		if ( queryString && ( queryString.includes('configuration=') || queryString.includes('options=') || queryString.includes('staggs_summary=') ) && ! queryString.includes('preview=true') ) {
			var json = shared_config.values;
			if ( '' === compareId ) {
				var value = json.filter(j => j.step_id == groupId);
			} else {
				var value = json.filter(j => j[compareId] == groupId);
			}

			if ( value.length > 0 ) {
				selectedValue = value[0];
			}
		}

		if ( TRACK_GLOBAL_OPTIONS && sessionStorage.getItem( PRODUCT_ID + '_sgg_' + groupName + '_option') ) {
			selectedValue = {
				name: groupName,
				id: sessionStorage.getItem( PRODUCT_ID + '_sgg_' + groupName + '_option')
			};
		}

		if ( $(group).hasClass('tickboxes') || ( $(group).find('input[type="checkbox"]').length && ! $(group).hasClass('single') && ! $(group).hasClass('products') ) ) {
			$(group).find('label:not(.disabled)').each(function(index,tickbox) {
				var groupId = $(tickbox).find('input').data('option-id');

				if ( json ) {
					var value = json.filter(j => j.id == groupId);
					selectedValue = value[0];
				}

				if ( $(tickbox).find('input').data('default') ) {
					selectedValue = 1;
				}

				if ( selectedValue ) {
					$(tickbox).find('input').prop('checked', true);
					$(tickbox).find('input').trigger('change');
				}
			});

		} else if ( $(group).hasClass('text-input') || $(group).hasClass('measurements') ) {

			/**
			 * Multi attribute values.
			 */

			$(group).find('.input-field-wrapper').each(function(index,option) {
				if ( $(option).find('textarea').length ) {
					var groupName = $(option).find('textarea').attr('name');
				} else {
					var groupName = $(option).find('input').attr('name');
				}

				if ( json ) {
					var value = json.filter(j => j.name == groupName);
					selectedValue = value[0];
				}

				if ( selectedValue ) {
					$(option).find('input[name="' + selectedValue.name + '"]').val(urldecode(selectedValue.value));
					$(option).find('input[name="' + selectedValue.name + '"]').trigger('input');

					if ( $(option).find('input[data-type=range]').length ) {
						// _setRangeSliderValues
						// var values = selectedValue.value.split('-');
						_setRangeSliderValue( $(option).find('input[data-type=range]'), selectedValue.value );
					}

					if ( $(option).find('input[data-type=date]').length ) {
						if ( $(option).find('input[data-type=date]').next('.datepicker-input-inline').length ) {
							$(option).find('input[data-type=date]').next('.datepicker-input-inline').datepicker('setDate', selectedValue.value);
							$(option).find('input[data-type=date]').trigger('input');
						} else {
							$(option).find('input[data-type=date]').datepicker('setDate', selectedValue.value);
							$(option).find('input[data-type=date]').trigger('input');
						}
					}
				} else if ( ! DISABLE_DEFAULTS ) {

					if ( $(option).find('input[data-type=range]').length ) {
						// _setRangeSliderValues
						// var values = selectedValue.value.split('-');
						_initRangeSlider( $(option).find('input[data-type=range]') );
					} else {
						$(option).find('input').trigger('input');
					}
				}
			});

		} else if ( $(group).hasClass('image-input') ) {

			/**
			 * Image upload value.
			 */

			$(group).find('.input-field-wrapper').each(function(index,option) {
				var groupName = $(option).find('input[type=hidden]').attr('name');

				if ( json ) {
					var value = json.filter(j => j.name == groupName);
					selectedValue = value[0];
				}

				if ( selectedValue ) {
					var image = selectedValue.url,
						imageExt = image.split('.').at(-1),
						$input = $(option).find('input'),
						stepId = $input.data('step-id'),
						label = $input.data('field-key'),
						material = $input.data('material-key'),
						group = $input.attr('id'),
						price = parseFloat($input.data('price'));

					if ( label ) {
						var delKey = label;
					} else if ( material ) {
						var delKey = material;
					} else {
						var delKey = stepId;
					}

					$(option).find('.show-if-input-value').removeClass('hidden');

					if ( _isImageExt( imageExt ) ) {
						$(option).find('.show-if-input-value').html('<div class="input-image-thumbnail"><img src="' + image + '"><span>' + selectedValue.value + '</span><a href="#0" class="remove-input-image" data-delete="' + delKey + '"></a></div>');

						if ( $('model-viewer').length ) {
							var modelViewer = document.querySelector("model-viewer");
							modelViewer.addEventListener('load', function() {
								_setImageUploadValue( $input, image );
							});
						} else {
							_setImageUploadValue( $input, image );
						}
					} else {
						$(option).find('.show-if-input-value').html('<div class="input-image-thumbnail"><span>' + selectedValue.value + '</span><a href="#0" class="remove-input-image" data-delete="' + delKey + '"></a></div>');
					}
					$(option).find('.hide-if-input-value').addClass('hidden');

					$(option).find('input[type=hidden]').val( selectedValue.value + '|' + image );
					$(option).find('input[type=hidden]').trigger('input');

					if ( price ) {
						pricetotal[group] = price;
						altpricetotal[group] = price;
					}
				}
			});

		} else if ( $(group).hasClass('products') ) {

			/**
			 * Products value.
			 */

			$(group).find('label:not(.disabled)').each(function(index,product) {
				var groupId = $(product).find('input').data('option-id');
				var id = $(product).find('input').attr('name');

				if ( json ) {
					var value = json.filter(j => j.id == groupId);
					if ( ! value.length ) {
						value = json.filter(j => j.id == id);
						selectedValue = value[0];
					} else {
						selectedValue = value[0];
					}
				}

				if ( selectedValue ) {
					if ( selectedValue.id.toString().indexOf('][') ) {
						// Repeater val
						var productInput = $(group).find('input[name="' + selectedValue.id + '"]');

						if ( $(productInput).attr('type') == 'checkbox' ) {
							$(group).find('input[name="' + selectedValue.id + '"]').prop('checked', true);
							$(group).find('input[name="' + selectedValue.id + '"]').trigger('change');
						} else {
							$(group).find('input[name="' + selectedValue.id + '"]').val( selectedValue.value );
							$(group).find('input[name="' + selectedValue.id + '"]').trigger('input');
						}
					} else {
						// Single val
						var productInput = $(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]');

						if ( $(productInput).attr('type') == 'checkbox' ) {
							$(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]').prop('checked', true);
							$(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]').trigger('change');
						} else {
							$(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]').val( selectedValue.value );
							$(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]').trigger('input');
						}
					}
				}
			});

		} else {

			/**
			 * Single attribute values.
			 */

			if ( $(group).find('label').length && $(group).find('label').length > 0 ) {

				var groupName = $(group).data('group');

				if ( ( groupName && ! $('.option-group-options[data-group="' + groupName + '"]').find('input:checked').length ) || ! groupName ) {

					if ( selectedValue ) {

						if ( compareId && compareId in selectedValue && 
							$(group).find('input[name="' + selectedValue[compareId] + '"][value="' + selectedValue['value'] + '"]:not(:disabled)').length ) {

							$(group).find('input[name="' + selectedValue[compareId] + '"][value="' + selectedValue['value'] + '"]').prop('checked', true);
							$(group).find('input[name="' + selectedValue[compareId] + '"][value="' + selectedValue['value'] + '"]').trigger('change');
						
						} else {

							if ( $(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]:not(:disabled)').length ) {

								$(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]').prop('checked', true);
								$(group).find('input[name="' + selectedValue.name + '"][data-option-id="' + selectedValue.id + '"]').trigger('change');

							} else {

								$(group).find('label:not(.disabled)').each(function(key,label) {
									if ( ! $(this).find('.out-of-stock').length ) {
										$(label).find('input').prop('checked', true);
										$(label).find('input').trigger('change');
										return false; // break;
									}
								});
							}
						}

					} else if ( ! $(group).hasClass('single') && ! DISABLE_DEFAULTS ) {

						if ( $(group).find('label:not(.disabled):first-of-type .out-of-stock').length ) {
							var stepIsChecked = false;

							$(group).find('label:not(.disabled)').each(function(key,label) {
								if ( ! $(this).find('.out-of-stock').length && ! $(this).find('[disabled]').length ) {
									$(label).find('input').prop('checked', true);
									$(label).find('input').trigger('change');
									stepIsChecked = true;
									return false; // break;
								}
							});

							if (!stepIsChecked) {
								isValidConfigurator = false;
								$('button#order').remove();
							}

						} else {

							if ( ! $(group).find('label:not(.disabled) input:checked').length ) {
								$(group).find('label:not(.disabled) input').each(function(key,input) {
									$(input).prop('checked', true);
									$(input).trigger('change');
									return false; // break;
								});
							} else {
								$(group).find('label:not(.disabled) input:checked').prop('checked', true);
								$(group).find('label:not(.disabled) input:checked').trigger('change');
							}
						}
					}
				} else if ( $(group).find('input:checked').length ) {
					// Toggle checked.
					$(group).find('input:checked').trigger('change');
				}
			} else if ($(group).find('select').length) {

				/**
				 * Dropdowns
				 */
				
				if ( selectedValue ) {

					var selectName = selectedValue.name;
					if ( compareId && compareId in selectedValue ) {
						selectName = selectedValue[compareId];
					}

					$(group).find('select[name="' + selectName + '"]').val(urldecode(selectedValue.value));
					$(group).find('select[name="' + selectName + '"]').trigger('change');
					
					// Check if selectmenu has been initialized.
					if ( $(group).find('.ui-selectmenu-button').length ) {
						$(group).find('select[name="' + selectName + '"]').selectmenu('refresh',true);
					}
				} else {
					if ( $(group).find('select').find('option:selected') ) {
						var option = $(group).find('select').find('option:selected');
					} else {
						var option = $(group).find('select').find('option:not(:disabled):first-of-type');
					}
					var style = $(option).attr('style');

					$(group).find('select').val( $(option).attr('value') );
					$(group).find('select').trigger('change');

					if ( $(group).find('.ui-selectmenu-button').length ) {
						$(group).find('select').selectmenu('refresh', true);
						
						if ( style ) {
							$(group).find('.ui-selectmenu-button').find('.ui-selectmenu-text').attr('style', style);

							if ( $(option).data('input-key') ) {
								_updatePreviewTextFont( $(option) );
							}
						}
					}
				}
			}
		}

		_setOptionFormulaPrices(group);
		_setOptionPercentagePrices(group);
	}

	function urldecode(str) {
		return decodeURIComponent((str+'').replace(/\+/g, '%20'));
	}

	function _syncTextInputs($input, modifier = '') {
		var label  = $input.data('field-key'),
			text   = $input.val(),
			slide  = $input.data('preview-index'),
			offset = '',
			order  = $input.parents('.option-group').data('preview-order'),
			group  = false;

		if ( '' !== modifier ) {
			label += modifier;
		}

		if ( $input.parents('.option-group').hasClass('always-hidden') ) {
			return;
		}

		if ( $input.parents('.option-group').data('bundle-preview') ) {
			group = $input.parents('.option-group').data('step');
		}

		if ( $input.data('unit') ) {
			text += ' ' + $input.data('unit');
		}

		// No label set. Abort.
		if ( ! label ) {
			return;
		}
		// No preview slide set. Abort.
		if ( ! slide ) {
			return;
		}

		if ( $(window).width() < 768 && ( $input.data('preview-top-xs') || $input.data('preview-left-xs') || $input.data('preview-width-xs') ) ) {
			if ( $input.data('preview-top-xs') ) {
				offset += 'top:' + $input.data('preview-top-xs') + ';';
			}
			if ( $input.data('preview-left-xs') ) {
				offset += 'left:' + $input.data('preview-left-xs') + ';';
			}
			if ( $input.data('preview-width-xs') ) {
				offset += 'width:' + $input.data('preview-width-xs') + ';';
			}
		} else {
			if ( $input.data('preview-top') ) {
				offset += 'top:' + $input.data('preview-top') + ';';
			}
			if ( $input.data('preview-left') ) {
				offset += 'left:' + $input.data('preview-left') + ';';
			}
			if ( $input.data('preview-width') ) {
				offset += 'width:' + $input.data('preview-width') + ';';
			}
		}

		if ( $input.data('preview-overflow') && 'hidden' == $input.data('preview-overflow') ) {
			offset += 'overflow:' + $input.data('preview-overflow') + ';';
		}

		var textOrder = order;
		if ( order && order.toString().includes(',') ) {
			order = order.split(',');
		}

		if ( slide.toString().includes(',') ) {
			var slides = slide.split(',');
			slides.forEach(function(slide, key) {
				if ( Array.isArray(order) ) {
					textOrder = order[key];
				}
				_setPreviewText( slide, label, text, offset, textOrder, group );
			});
		} else {
			_setPreviewText( slide, label, text, offset, textOrder, group );
		}

		if ( group ) {
			if ( $input.parents('.option-group').data('bundle-height') ) {
				$('#' + group + '_wrapper').css('maxHeight',  $input.parents('.option-group').data('bundle-height'));
			}
		}

		if ( $input.data('preview-overflow') && 'fittext' == $input.data('preview-overflow') ) {
			if ( group ) {
				_resizeTextToFit( $('#' + group + '_wrapper'), parseInt( $('#' + group + '_wrapper').css('font-size') ) );
			} else {
				_resizeTextToFit( $('#' + label + '_wrapper'), parseInt( $('#' + label + '_wrapper').css('font-size') ) );
			}
		}

		if ( $('#' + label).hasClass('init') ) {
			$('#' + label).removeClass('init');
		}
	}

	function _setPreviewText( slide, label, text, offset = '', order = 0, bundle = false ) {
		slide = slide - 1; // zero based index.

		var style = 'z-index: ' + order + ';';
		if ( offset && '' !== offset ) {
			style += offset;
		}

		if ( bundle ) {
			// Bundled.
			if ( $('#preview_slide_' + slide + ' #' + bundle + '_wrapper').length ) {
				if ( $('#preview_slide_' + slide + ' #' + bundle + '_wrapper #' + label).length ) {
					if ( $('#preview_slide_' + slide + ' #' + bundle + '_wrapper #' + label + ' .text-preview').length ) {
						$('#preview_slide_' + slide + ' #' + bundle + '_wrapper #' + label + ' .text-preview').text(text);
					} else {
						$('#preview_slide_' + slide + ' #' + bundle + '_wrapper #' + label).append('<span class="text-preview">' + text + '</span>');
					}
				} else {
					$('#preview_slide_' + slide + ' #' + bundle + '_wrapper').append('<div class="preview-text-input init" id="' + label + '"><span class="text-preview">' + text + '</span></div></div>');
				}
			} else {
				$('#preview_slide_' + slide).append('<div class="preview-text-input-wrapper preview-text-input-bundle" id="' + bundle + '_wrapper" style="' + style + '"><div class="preview-text-input init" id="' + label + '"><span class="text-preview">' + text + '</span></div></div>');
			}
		} else {
			// Individual.
			if ( $('#preview_slide_' + slide).find('#' + label).length ) {
				if ( $('#preview_slide_' + slide + ' #' + label).find('.text-preview').length ) {
					$('#preview_slide_' + slide).find('#' + label + ' .text-preview').text( text );
				} else {
					$('#preview_slide_' + slide).find('#' + label).append( '<span class="text-preview">' + text + '</span>' );
				}
			} else {
				$('#preview_slide_' + slide).append('<div class="preview-text-input-wrapper preview-text-input-single" id="' + label + '_wrapper" style="' + style + '"><div class="preview-text-input init" id="' + label + '"><span class="text-preview">' + text + '</span></div></div>');
			}
		}

		if ( order > zIndex ) {
			zIndex = order;
		}
	}

	function _syncImageInputs(input) {
		var label = $(input).data('field-key'),
			material = $(input).data('material-key'),
			stepId = $(input).data('step-id'),
			group = $(input).attr('id'),
			price = parseFloat($(input).data('price'));

		if ( $(input).parents('.option-group').hasClass('always-hidden') ) {
			return;
		}

		if ( label ) {
			var delKey = label;
		} else if ( material ) {
			var delKey = material;
		} else {
			var delKey = stepId;
		}

		if (input.files && input.files[0]) {
			if ( $(input).data('size') ) {
				var mb = parseFloat( $(input).data('size') );
				var allowedMB = mb * 1024 * 1024;
				var filesize = input.files[0].size;

				if ( filesize > allowedMB ) {
					$(input).val(''); // undo file.
					alert('File size should be less than ' + mb + ' MB');
					return false;
				}
			}

			// Find fix.
			// if ( "image/jpeg" === input.files[0].type ) {
			// 	$(input).val(''); // undo file.
			// 	alert('JPEG files are not supported. Please use JPG instead.');
			// 	return false;
			// }

			var reader = new FileReader();
			// webp, gif, tiff, bmp, heif, eps, ico
			if ( _isImageFile( input.files[0] ) ) {
				// Read the file as a Data URL (suitable for images)
				reader.onload = function (e) {
					_setImageUploadValue( $(input), e.target.result );

					$(input).removeClass('invalid');
					$(input).parents('.option-group').find('.sgg-error').remove();

					$(input).parents('.image-input-field').find('.show-if-input-value').removeClass('hidden');
					$(input).parents('.image-input-field').find('.show-if-input-value').html(
						'<div class="input-image-thumbnail"><img src="' + e.target.result + '" alt="' + input.files[0].name + '"><span>'
						 + input.files[0].name + '</span><a href="#0" class="remove-input-image" data-delete="' + delKey + '"></a></div>'
					);
					$(input).parents('.image-input-field').find('.hide-if-input-value').addClass('hidden');

					$(input).parents('.image-input-field').find('input[type=hidden]').val(input.files[0].name + '|' + e.target.result);

					var validStep = _validateStepFields();
					if ( activeStep < maxStep && validStep ) {
						$('.staggs-step-next-button').removeClass('disabled');
					} else {
						$('.staggs-step-next-button').addClass('disabled');
					}

					if ( $('.staggs-summary-widget').length ) {
						_updateSummary();
					}
				}
				
				reader.readAsDataURL(input.files[0]);
			} else {
				reader.onload = function (e) {
					$(input).removeClass('invalid');
					$(input).parents('.option-group').find('.sgg-error').remove();

					$(input).parents('.image-input-field').find('.show-if-input-value').removeClass('hidden');
					$(input).parents('.image-input-field').find('.show-if-input-value').html(
						'<div class="input-image-thumbnail"><span>'
						+ input.files[0].name + '</span><a href="#0" class="remove-input-image" data-delete="' + delKey + '"></a></div>'
					);
					$(input).parents('.image-input-field').find('.hide-if-input-value').addClass('hidden');

					$(input).parents('.image-input-field').find('input[type=hidden]').val(input.files[0].name + '|' + e.target.result);

					var validStep = _validateStepFields();
					if ( activeStep < maxStep && validStep ) {
						$('.staggs-step-next-button').removeClass('disabled');
					} else {
						$('.staggs-step-next-button').addClass('disabled');
					}

					if ( $('.staggs-summary-widget').length ) {
						_updateSummary();
					}
				}
				reader.readAsArrayBuffer(input.files[0]);

				var data = new FormData();
				data.append('file', input.files[0]);
				data.append('group', group);
				data.append('action', 'staggs_validate_file_upload');
				
				var $input = $(input);

				$.ajax({
					type: 'post',
					url: AJAX_URL,
					contentType: false,
					processData: false,
					data: data,
					success: function (data) {
						var result = JSON.parse(data);
						$input.parents('.image-input-field').find('input[type=hidden]').val(result.filename + '|' + result.filepath);
					},
					error: function(err) {
						$input.addClass('invalid');
						if ( ! $input.parents('.option-group').find('.sgg-error').length ) {		
							$input.parents('.option-group').append('<div class="sgg-error">File could not be processed. Please try again.</div>');
						}

						$input.parents('.image-input-field').find('.show-if-input-value').addClass('hidden');
						$input.parents('.image-input-field').find('.hide-if-input-value').removeClass('hidden');
						$input.parents('.image-input-field').find('input[type=hidden]').val('');
					}
				});
			}

			if ( price ) {
				pricetotal[group] = price;
				altpricetotal[group] = price;
			}
		} else if ( price ) {
			delete pricetotal[group];
			delete altpricetotal[group];
		}

		_setTotals();
	}

	function _setImageUploadValue( $input, image ) {
		var label = $input.data('field-key'),
			material = $input.parents('.option-group').data('model'),
			slide = $input.data('preview-index'),
			offset = '',
			order = $input.parents('.option-group').data('preview-order');

		if (  '' != material || $input.data('material-key') ) {
			// 3D model
			var textureChannel = $input.parents('.option-group').data('model-material');
			if ( ! textureChannel ) {
				textureChannel = 'base';
			}
			if ( ! material ) {
				material = $input.data('material-key');
			}

			if ( $('#' + label + '_canvas').length ) {
				var canvas = new fabric.Canvas(label + '_canvas');
				var objConfig = {
					width: $input.data('preview-width'),
					height: $input.data('preview-height'),
				};

				fabric.Image.fromURL(image, function(img) {
					var canvasWidth = canvas.width;
					var canvasHeight = canvas.height;
					var imgWidth = img.width;
					var imgHeight = img.height;
					var scaleX = canvasWidth / imgWidth;
					var scaleY = canvasHeight / imgHeight;
					var scale = Math.min(scaleX, scaleY);

					img.set({
						scaleX: scale,
						scaleY: scale,
						left: (canvasWidth - imgWidth * scale) / 2,
						top: (canvasHeight - imgHeight * scale) / 2,
						originX: 'left',
						originY: 'top'
					});

					canvas.add(img);
					canvas.renderAll();

					var dataURL = canvas.toDataURL();
					if ( ! textureChannel ) {
						textureChannel = 'base';
					}

					$.event.trigger({
						type: "modelMaterialChanged",
						model: material,
						channel: textureChannel,
						texture: dataURL,
					});
				});
			}
		}

		if ( label ) {
			// No preview slide set. Abort.
			if ( ! slide ) {
				return;
			}

			// Image preview.
			if ( $input.data('preview-width') ) {
				offset += 'width:' + $input.data('preview-width') + ';';
			}
			if ( $input.data('preview-height') ) {
				offset += 'height:' + $input.data('preview-height') + ';';
			}

			if ( $(window).width() < 768 && $input.data('preview-top-xs')  ) {
				// Mobile
				if ( $input.data('preview-top-xs') ) {
					offset += 'top:' + $input.data('preview-top-xs') + ';';
				}
				if ( $input.data('preview-left-xs') ) {
					offset += 'left:' + $input.data('preview-left-xs') + ';';
				}
			} else {
				// Desktop
				if ( $input.data('preview-top') ) {
					offset += 'top:' + $input.data('preview-top') + ';';
				}
				if ( $input.data('preview-left') ) {
					offset += 'left:' + $input.data('preview-left') + ';';
				}
			}

			var imageOrder = order;
			if ( order && order.toString().includes(',') ) {
				order = order.split(',');
			}

			if ( slide.toString().includes(',') ) {
				var slides = slide.split(',');
				slides.forEach(function(slide, key) {
					if ( Array.isArray(order) ) {
						imageOrder = order[key];
					}

					_setPreviewImage(slide, label, image, offset, imageOrder);
				});
			} else {
				_setPreviewImage(slide, label, image, offset, imageOrder);
			}
	
			if ( $('span#' + label).hasClass('init') ) {
				$('span#' + label).removeClass('init');

				if ( $input.data('preview-fill') ) {
					$('span#' + label).find('img').css('object-fit', $input.data('preview-fill'));
				}
			}
		}

		_setTotals();
	}

	function _removeImageInput(input) {
		var label = $(input).data('field-key'),
			material = $(input).parents('.option-group').data('model'),
			slide = $(input).data('preview-index'),
			group = $(input).attr('id'),
			price = parseFloat($(input).data('price'));

		if (material) {
			// Undo 3D model texture.
			var textureChannel = $(input).parents('.option-group').data('model-material');
			if ( ! textureChannel ) {
				textureChannel = 'base';
			}

			$.event.trigger({
				type: "modelOptionsGroupRemoved",
				model: material,
				channel: textureChannel
			});
		}

		if ( label ) {
			// No preview slide set. Abort.
			if ( ! slide ) {
				return;
			}

			if ( slide.toString().includes(',') ) {
				var slides = slide.split(',');
				slides.forEach(function(slide, key) {
					$('#preview_slide_' + (slide - 1)).find('.preview-image-input#' + label).remove();
				});
			} else {
				$('#preview_slide_' + (slide - 1)).find('.preview-image-input#' + label).remove();
			}
		}

		$(input).val('');
		$(input).next('input[type=hidden]').val('');

		$(input).parents('.image-input-field').find('.hide-if-input-value').removeClass('hidden');
		$(input).parents('.image-input-field').find('.show-if-input-value').html('');

		if ( price ) {
			delete pricetotal[group];
			delete altpricetotal[group];
		}

		_setTotals();
	}

	function _setPreviewImage(slide, label, src, offset = '', order = 0) {
		slide = slide - 1; // zero based index.

		var style = 'z-index: ' + order + ';';
		if ( offset && '' !== offset ) {
			style += offset;
		}

		if ($('#preview_slide_' + slide).find('#' + label).length) {
			$('#preview_slide_' + slide).find('#' + label + ' img').attr('src', src);
		} else {
			$('#preview_slide_' + slide).append('<div class="preview-image-input-wrapper" id="' + label + '_wrapper" style="' + style + '"><span class="preview-image-input init" id="' + label + '"><img src="' + src + '"></span></div>');
		}

		if ( offset && '' !== offset ) {
			$('#preview_slide_' + slide).find('#' + label + ' img').css('height', $('#preview_slide_' + slide + ' #' + label + '_wrapper').innerHeight() );
			$('#preview_slide_' + slide).find('#' + label + ' img').css('width', $('#preview_slide_' + slide + ' #' + label + '_wrapper').width() );
		}

		if ( order > zIndex ) {
			zIndex = order;
		}
	}

	function _updatePreviewTextStyle( label, css ) {
		var pair = css.split(':');

		if ( label.toString().includes('.') || label.toString().includes('#') ) {
			$(label).css( pair[0], pair[1].replace(';', '') );
		} else if ( $('.staggs-view-gallery').find('div#' + label + '_wrapper').length ) {
			$('.staggs-view-gallery').find('div#' + label + '_wrapper').css( pair[0], pair[1].replace(';', '') );
		}
	}

	function _updateMultiplePreviewStyles( inputKey, $input, baseProperties = '' ) {
		if ( '' === inputKey ) {
			return;
		}
		if ( ! $input.parents('.option-group').data('preview-ref-props') && ( ! baseProperties || '' === baseProperties ) ) {
			return;
		}

		var selector = $input.data('preview-selector');
		var property = $input.parents('.option-group').data('preview-ref-props')

		if ( selector.indexOf(',') !== -1 ) {
			selector = selector.split(',');
			property = property.split(',');
		} else {
			selector = [ selector ];
			property = [ property ];
		}

		if ( baseProperties ) {
			if ( baseProperties.indexOf(',') !== -1 ) {
				property = baseProperties.split(',');
			} else {
				property = [ baseProperties ];
			}
		}

		property.forEach((prop, index) => {
			prop = prop.replace(' ', '');
			
			if ( selector[index] ) {
				var css = prop + ': ' + selector[index];
				
				_updatePreviewStyles( inputKey, css );
			}
		});
	}

	function _updatePreviewStyles( inputKey, css ) {
		if ( ! inputKey || '' == inputKey ) {
			return;
		}
		if ( ! css || '' == css ) {
			return;
		}

		if ( inputKey.indexOf(',') !== -1 ) {
			var inputKeys = inputKey.split(',');
			inputKeys.forEach((key, index) => {
				key = key.replace(' ', '');
				_updatePreviewTextStyle( key, css );
			});
		} else {
			_updatePreviewTextStyle( inputKey, css );
		}
	}

	function _updatePreviewTextFont( $input ) {
		var inputKey = $input.data('input-key');
		if (!inputKey) {
			if ($input.closest('.option-group').data('model')) {
				_updateOptionMaterialCanvas($input.closest('.option-group').data('model'), {
					fontFamily: $input.css('fontFamily').split(',')[0].replaceAll('"', ''),
					fontWeight: $input.css('fontWeight'),
				});
			}
			return;
		}

		var fontFamily = $input.data('font-family');
		var fontWeight = $input.data('font-weight');
		if ( ! fontFamily || ! fontWeight ) {
			_updateMultiplePreviewStyles(inputKey, $input);
			return;
		}

		if ( inputKey.indexOf(',') ) {
			var inputKeys = inputKey.split(',');
			inputKeys.forEach((key, index) => {
				key = key.replace(' ' , '');

				if ( $('.preview-text-input#' + key).parents('.preview-text-input-bundle').length ) {
					var $found_wrapper = $('.preview-text-input#' + key).parents('.preview-text-input-bundle');

					if ( $found_wrapper.length > 1 ) {
						$found_wrapper.each(function(index,wrapper) {
							var wrapperKey = $(wrapper).attr('id').replace('_wrapper', '');
						
							_updatePreviewTextStyle( wrapperKey, 'font-family: ' + fontFamily );
							_updatePreviewTextStyle( wrapperKey, 'font-weight: ' + fontWeight );
		
							_resizeTextToFit( $(wrapper), parseInt( $(wrapper).css('font-size') ) );
						});
					} else {
						var wrapperKey = $found_wrapper.attr('id').replace('_wrapper', '');
					
						_updatePreviewTextStyle( wrapperKey, 'font-family: ' + fontFamily );
						_updatePreviewTextStyle( wrapperKey, 'font-weight: ' + fontWeight );
	
						_resizeTextToFit( $found_wrapper, parseInt( $found_wrapper.css('font-size') ) );
					}
				} else {
					_updatePreviewTextStyle(key, 'font-family: ' + fontFamily);
					_updatePreviewTextStyle(key, 'font-weight: ' + fontWeight);
				}
			})
		} else {
			_updatePreviewTextStyle(inputKey, 'font-family: ' + fontFamily);
			_updatePreviewTextStyle(inputKey, 'font-weight: ' + fontWeight);
		}

		if ( $input.data('preview-overflow') && 'fittext' == $input.data('preview-overflow') ) {
			if ( group ) {
				_resizeTextToFit( $('#' + group + '_wrapper'), parseInt( $('#' + group + '_wrapper').css('font-size') ), action );
			} else {
				_resizeTextToFit( $('#' + label + '_wrapper'), parseInt( $('#' + label + '_wrapper').css('font-size') ), action );
			}
		}
	}

	function _initRangeSlider( $range ) {
		var inputKey = $range.data('field-key');
		var value   = parseInt( $range.val() );
		var unit    = '';

		if ( $range.data('unit') ) {
			unit = $range.data('unit');
		}

		if ($(window).width() < 700) {
			_makeRangeHandleDraggable($range.find('span.ui-slider-handle'));
		}

		if ( ! $range.attr('min') || ! $range.attr('max') ) {
			alert('Field min and max values are required for range input.');
		} else {
			var sliderConfig = {
				range: false, // isRange,
				value: value,
			};

			if ( $range.attr('min') ) {
				sliderConfig.min = parseInt( $range.attr('min') );

				if ( ! value ) {
					sliderConfig.value = sliderConfig.min;
				}
			}

			if ( $range.attr('max') ) {
				sliderConfig.max = parseInt( $range.attr('max') );
			}

			if ( $range.data('range-increments') ) {
				sliderConfig.step = parseInt( $range.data('range-increments') );
			}
			
			sliderConfig.create = function(){ 
				if ( $range.data('range-bubble') ) {
					var handle = jQuery(this).find('.ui-slider-handle');
					var bubble = jQuery('<div class="ui-slider-valuebox"></div>');
					if ( ! jQuery(this).find('.ui-slider-valuebox').length ) {
						handle.append(bubble);
					}
				}
			}

			sliderConfig.slide = function( event, ui ) {
				// On handle slide
				$range.val( ui.value );
				$range.parents('label').find('span.value').text( ui.value + ' ' + unit );

				if ( $('.staggs-view-gallery #' + inputKey).length ) {
					$('.staggs-view-gallery #' + inputKey).text( ui.value + ' ' + unit );
				}

				if ( $range.data('range-bubble') ) {
					ui.handle.childNodes[0].innerHTML = ui.value;
				}

				if ( $range.parents('.option-group').data('preview-ref') && $range.parents('.option-group').data('preview-ref-props') ) {
					var selector = $range.data('preview-selector').split(',');
					var property = $range.parents('.option-group').data('preview-ref-props').split(',');

					property.each(function(index,property){
						if ( selector[index] ) {
							var css_rule = property + ':' + selector[index];
						} else {
							var css_rule = property + ':' + ui.value + unit;
						}
						_updatePreviewTextStyle( $range.parents('.option-group').data('preview-ref'), css_rule );
					});
				}
			}

			$range.closest('.input-field').find('.range-slider').slider(sliderConfig).bind('slidechange', function(event,ui){
				// After let handle loose.
				_validateConditionalSteps();

				_updateMeasurementPrices( $range );
			});

			var value = $range.closest('.input-field').find('.range-slider').slider("value");
			$range.val( value );
			$range.closest('.input-field').find('.range-slider').find('.ui-slider-valuebox').text(value);
			$range.parents('label').find('span.value').text( value + ' ' + unit );
		}
	}

	function _setRangeSliderValue( $range, val ) {
		var unit = '';
		if ( $range.data('unit') ) {
			unit = $range.data('unit');
		}

		$range.closest('.input-field').find('.range-slider').slider("option", "value", val);
		$range.val(val);
		$range.parents('label').find('span.value').text( val + ' ' + unit );
	}

	function _makeRangeHandleDraggable(handle) {
		handle.draggable({
			touchAction: "none",
			axis: "x",
			containment: "parent", 
			drag: function(event, ui) {
				var slider = handle.closest('.range-slider');
				var minPos = 0;
				var maxPos = slider.width() - handle.width();
				var value = Math.round((ui.position.left - minPos) / (maxPos - minPos) * (slider.slider("option", "max") - slider.slider("option", "min")) + slider.slider("option", "min"));
				slider.slider("value", value);

				var display = slider.find('.left-default-value');
				var displayWidth = display.width();
				var displayLeft = ui.position.left - displayWidth / 2 + handle.width() / 2;
				display.css('left', displayLeft);
			}
		});
	}

	function _validateRuleset( conditionalRules, keyValuePairs ) {
		var validRules = {};
		var jsonRules  = conditionalRules.replaceAll("'", '"');
		jsonRules = JSON.parse(jsonRules);

		// Compare values.
		for ( var r = 0; r < jsonRules.length; r++ ) {
			var ruleSet = jsonRules[r];
			var compareResult = false;

			var valueIndex = 0;
			var ruleKey = ruleSet.key;
			if ( ruleKey && ruleKey.toString().indexOf('-') !== -1 ) {
				ruleKey = ruleSet.key.split('-')[0];
				valueIndex = ruleSet.key.split('-')[1];
			}

			if ( ruleKey in keyValuePairs ) {
				// Current value visible. Check condition.
				switch(ruleSet.compare) {
					case '!empty':
						compareResult = true; // has value.
						break;
					case '<=':
						compareResult = parseFloat(keyValuePairs[ruleKey][valueIndex]) <= parseFloat(ruleSet.value);
						break;
					case '>=':
						compareResult = parseFloat(keyValuePairs[ruleKey][valueIndex]) >= parseFloat(ruleSet.value);
						break;
					case '<':
						compareResult = parseFloat(keyValuePairs[ruleKey][valueIndex]) < parseFloat(ruleSet.value);
						break;
					case '>':
						compareResult = parseFloat(keyValuePairs[ruleKey][valueIndex]) > parseFloat(ruleSet.value);
						break;
					case '!=':
						compareResult = !keyValuePairs[ruleKey].includes(ruleSet.value);
						break;
					default:
						compareResult = keyValuePairs[ruleKey].includes(ruleSet.value);
						break;
				};
			} else if ( 'empty' === ruleSet.compare ) {
				compareResult = true; // Not defined. Has no value. Empty.
			}

			validRules[r] = {
				result: compareResult,
				compare: ruleSet.relation,
				newset: ruleSet.newset == 'yes',
			}
		}

		// Get valid rules count.
		var valid = false;
		var finalRule = '';
		var validRuleKeys = Object.keys(validRules);

		validRuleKeys.forEach(function(vkey,index) {
			finalRule += validRules[vkey].result;

			if ( index >= 0 && index + 1 < validRuleKeys.length ) {
				var compare = validRules[vkey].compare;
				var compareString = '';
				if ( 'and' == compare ) {
					compareString = '&&';
				} else {
					compareString = '||';
				}

				if ( validRules[vkey].newset ) {
					finalRule += ')' + compareString + '(';
				} else {
					finalRule += compareString;
				}
			}
		});

		if ( '' !== finalRule ) {
			finalRule = '(' + finalRule + ')';
			valid = eval(finalRule);
		}

		return valid;
	}

	function _buildPreviewGallery(preview, stepIndex, order = 0) {
		preview = preview.split(',');
		
		var imageOrder = order;
		if ( order && order.toString().includes(',') ) {
			order = order.split(',');
		}

		if (preview.length) {
			for (var index in preview) {
				var groupIndex = preview[index].split('|')[0];
				var previewUrl = preview[index].split('|')[1];

				if ( Array.isArray(order) ) {
					imageOrder = order[index];
				}

				if (!IMAGE_STACK) {
					// Save URL.	
					lastImages[stepIndex] = previewUrl;
					// No image stack. Always replace base image.
					stepIndex = 0;
				} else {
					stepIndex = stepIndex.replaceAll('[', '_').replaceAll(']', '');
				}

				if ($('.staggs-view-gallery #preview_' + groupIndex + '_' + stepIndex).length) {
					$('.staggs-view-gallery #preview_' + groupIndex + '_' + stepIndex).attr('src', previewUrl);
					$('.view-nav-buttons #button_preview_' + groupIndex + '_' + stepIndex).attr('src', previewUrl);
				} else {
					$('#preview_slide_' + groupIndex).append('<img id="preview_' + groupIndex + '_' + stepIndex + '" src="' + previewUrl + '" alt="" style="z-index: ' + imageOrder + '">');
					$('#preview_nav_' + groupIndex).append('<img id="button_preview_' + groupIndex + '_' + stepIndex + '" src="' + previewUrl + '" alt="" style="z-index: ' + imageOrder + '">');
				}

				if ( ! USE_PRODUCT_THUMBNAIL ) {
					if ($('#preview_' + groupIndex + '_preview').length) {
						$('#preview_' + groupIndex + '_preview').remove();
					}
	
					if ($('#button_preview_' + groupIndex + '_preview').length) {
						$('#button_preview_' + groupIndex + '_preview').remove();
					}
				}
			}

			if ( order > zIndex ) {
				zIndex = order;
			}
		}
	}

	function _deletePreviewGallery(preview, stepIndex) {
		if (IMAGE_STACK) {
			if (preview) {
				preview = preview.split(',');
				if (preview.length) {
					for (var index in preview) {
						var groupIndex = preview[index].split('|')[0];
	
						if ($('.staggs-view-gallery #preview_' + groupIndex + '_' + stepIndex).length) {
							$('.staggs-view-gallery #preview_' + groupIndex + '_' + stepIndex).remove();
							$('.staggs-view-gallery #button_preview_' + groupIndex + '_' + stepIndex).remove();
						}
					}
				}
			} else if ( $('.staggs-view-gallery [id^="preview_"][id$="' + stepIndex + '"]').length ) {
				$('.staggs-view-gallery [id^="preview_"][id$="' + stepIndex + '"]').remove();
				$('.staggs-view-gallery [id^="button_preview_"][id$="' + stepIndex + '"]').remove();
			}
		} else {
			// No image stack. Replace or restore base image.
			delete lastImages[stepIndex];

			var previewUrl = lastImages[Object.keys(lastImages)[Object.keys(lastImages).length - 1]];
			var groupIndex = 0;

			$('.staggs-view-gallery #preview_' + groupIndex + '_0').attr('src', previewUrl);
			$('.staggs-view-gallery #button_preview_' + groupIndex + '_0').attr('src', previewUrl);
		}
	}

	function _getFinalProductImage() {
		var baseUrl = PRODUCT_THUMBNAIL_URL, 
			dataUrl = '',
			isPromise = false;

		if ( $('#preview_slide_0').length ) {
			isPromise = true;
			// Remove 'noscript' tags possibly added by lazy loading.
			if ( $('#preview_slide_0 noscript').length ) {
				$('#preview_slide_0 noscript').html('');
				$('#preview_slide_0 noscript').remove();
			}

			// Set background to slide so it gets captured along with the image.
			if ( $('#staggs-preview[data-include-bg]').length ) {
				$('#preview_slide_0').css('backgroundImage', $('.product-view-inner').css('backgroundImage') );

				if ( $('.product-view-inner').css('backgroundSize') ) {
					$('#preview_slide_0').css('backgroundSize', $('.product-view-inner').css('backgroundSize') );
					$('#preview_slide_0').css('backgroundPosition', $('.product-view-inner').css('backgroundPosition') );
				}
			}

			dataUrl = modernScreenshot.domToPng(document.getElementById('preview_slide_0'));
		} else if ( $('.staggs-view-gallery model-viewer').length ) {
			$('.staggs-view-gallery model-viewer').attr('camera-orbit', $('.staggs-view-gallery').data('base-orbit') ?? 'auto' );
			$('.staggs-view-gallery model-viewer').attr('camera-target', $('.staggs-view-gallery').data('base-target') ?? 'auto' );
			$('.staggs-view-gallery model-viewer').attr('field-of-view', $('.staggs-view-gallery').data('base-view') );

			dataUrl = new Promise(function(resolve,reject) {
				setTimeout(function() {
					dataUrl = document.getElementById('product-model-view').toDataURL();
					resolve(dataUrl);
				}, 500);
			});
			isPromise = true;
		} else {
			dataUrl = baseUrl;
		}

		return {
			'image': dataUrl,
			'isPromise': isPromise
		}
	}

	function _clearProductBackgroundImage() {
		if ( $('#staggs-preview[data-include-bg]').length ) {
			$('#preview_slide_0').css('backgroundImage', '');
		}
	}

	function _setTotalsButton(validStep) {
		if ( $('.option-group.total').data('show-step') && 'final' === $('.option-group.total').data('show-step') ) {
			if ( activeStep === maxStep ) {
				if ( $('.option-group.total').data('step-valid') && 'required' === $('.option-group.total').data('step-valid') ) {
					if ( validStep ) {
						$('.option-group.total').removeClass('hidden');
						$('.option-group.total form .button').prop('disabled',false);
					}
				} else {
					$('.option-group.total').removeClass('hidden');
					$('.option-group.total form .button').prop('disabled',false);
				}
			} else {
				$('.option-group.total').addClass('hidden');
				$('.option-group.total form .button').prop('disabled',true);
			}
		}

		if ( $('.bottom-bar-totals').data('show-step') && 'final' === $('.bottom-bar-totals').data('show-step') ) {
			if ( activeStep === maxStep ) {
				if ( $('.bottom-bar-totals').data('step-valid') && 'required' === $('.bottom-bar-totals').data('step-valid') ) {
					if ( validStep ) {
						$('.bottom-bar-totals').removeClass('hidden');
						$('.bottom-bar-totals form .button').prop('disabled',false);

						_stepActivateCartButton();
					}
				} else {
					$('.bottom-bar-totals').removeClass('hidden');
					$('.bottom-bar-totals form .button').prop('disabled',false);

					_stepActivateCartButton();
				}
			} else {
				$('.bottom-bar-totals').addClass('hidden');
				$('.bottom-bar-totals form .button').prop('disabled',true);

				_stepResetCartButton();
			}
		}

		if ( activeStep === maxStep ) {
			$('.option-group.total form').addClass('option-group-step-last');
			$('.bottom-bar-totals form').addClass('option-group-step-last');
		} else {
			$('.option-group.total form').removeClass('option-group-step-last');
			$('.bottom-bar-totals form').removeClass('option-group-step-last');
		}

		$.event.trigger({
			type: "setActiveConfiguratorButtons",
			active: activeStep,
			max: maxStep
		});
	}

	function _stepActivateCartButton() {
		// if ( $(window).width() > 767 ) {
			if ( $('.bottom-bar-totals').data('button-position') && 'step-controls' === $('.bottom-bar-totals').data('button-position') && ! $('.bottom-bar-totals').attr('data-replaced') ) {
				$('.staggs-configurator-bottom-bar').addClass('buttons-reversed');

				var buttonHtml = $('.bottom-bar-totals .button-wrapper .cart').get(0).outerHTML;
				$('.bottom-bar-totals .button-wrapper .cart').html('');
				$('.option-group-step-buttons').append(buttonHtml);

				$('.bottom-bar-totals').attr('data-replaced','replaced');
			}
		// }
	}

	function _stepResetCartButton() {
		// if ( $(window).width() > 767 ) {
			if ( $('.bottom-bar-totals').data('button-position') && 'step-controls' === $('.bottom-bar-totals').data('button-position') ) {
				var buttonHtml = $('.option-group-step-buttons .cart').html();
				$('.option-group-step-buttons .cart').remove();
				$('.bottom-bar-totals .cart').append(buttonHtml);

				$('.bottom-bar-totals').removeAttr('data-replaced');
			}
		// }
	}

	async function _setTotals() {
		var total = 0;
		var alttotal = 0; // without sale

		var groupnames = [];
		$('#configurator-options input, #configurator-options textarea, #configurator-options select').each(function (index, input) {
			if ( $(input).data('field-key') ) {
				var group = $(input).data('field-key');
			} else { 
				var group = $(input).attr('name');
			}

			if ($(input).attr('type') === 'checkbox' || $(input).attr('type') === 'hidden' || $(input).parents('.sgg-product').length ) {
				group = $(input).attr('id');
			}

			if ( $(input).parents('.measurements').length && $(input).parents('.measurements').data('price-key') ) {
				group = $(input).parents('.measurements').data('price-key');
			}

			if (!groupnames.includes(group)) {
				groupnames.push(group);
			}
		});
		
		for (var priceitem in pricetotal) {
			if (!groupnames.includes(priceitem)) {
				delete pricetotal[priceitem];
				delete altpricetotal[priceitem];
			}
		}

		var prices = Object.values(pricetotal);
		for (var price_key in prices) {
			total += parseFloat(prices[price_key]);
		}
		var altprices = Object.values(altpricetotal);
		for (var alt_price_key in altprices) {
			alttotal += parseFloat(altprices[alt_price_key]);
		}

		if ( $('.total #productprice').length ) {
			var productPrice = _formatPriceOutput(PRODUCT_PRICE, '', true);
			if ( PRODUCT_ALT_PRICE > PRODUCT_PRICE) {
				productPrice += '<del>' + _formatPriceOutput(PRODUCT_ALT_PRICE, '', true) + '</del>';
			}
			$('.option-group.total #productprice').html(productPrice);
			$('.staggs-product-options-basic #productprice').html(productPrice);
		}

		if ( $('.total #optionsprice').length ) {
			var optionPrice = _formatPriceOutput(total, '', true);
			if ( total < alttotal ) {
				optionPrice += '<del>' + _formatPriceOutput(alttotal, '', true) + '</del>';
			}
			$('.option-group.total #optionsprice').html(optionPrice);
			$('.staggs-product-options-basic #optionsprice').html(optionPrice);
		}

		var quantity = _getConfiguratorQuantity();
		if ( ! quantity ) {
			quantity = 1;
		}

		var grandtotal = total;
		var altgrandtotal = alttotal;
		if ( USE_PRODUCT_PRICE ) {
			if ( $('.option-group.total[data-table-id]').length ) {
				var tableId = $('.option-group.total').data('table-id');
				var tableResult = await _getPriceTableValue(tableId, 'quantity', quantity );
				if ( tableResult ) { 
					var result = JSON.parse(tableResult);
					grandtotal = total + parseFloat( result.price );
					altgrandtotal = alttotal + parseFloat( result.price );
				}
			} else {
				grandtotal = total + PRODUCT_PRICE;
				altgrandtotal = alttotal + PRODUCT_ALT_PRICE;
			}
		}
		
		if ( $('.option-group.total .button-wrapper').data('formula') ) {
			var formula = $('.option-group.total .button-wrapper').data('formula');
			formula = formula.replaceAll('product_price', PRODUCT_PRICE);
			formula = formula.replaceAll('option_price', total);
			formula = formula.replaceAll('total_price', grandtotal);

			pricetotal = Object.keys(pricetotal).sort(function(a, b) {
				return a.length < b.length
			}).reduce((obj, key) => {
				obj[key] = pricetotal[key];
				return obj;
			}, {});

			for ( var key in pricetotal ) {
				formula = formula.replaceAll(key, pricetotal[key]);
			}

			formula = formula.replaceAll('quantity', quantity);

			var regExp = /([a-zA-Z]+-*_*)+/g;
			// Only evaluate formula when all letters have been replaced.
			if ( ! regExp.test( formula ) ) {
				grandtotal = eval( formula );
				altgrandtotal = eval( formula );
			} else {
                formula = formula.replaceAll(regExp, '0');

				grandtotal = eval( formula );
				altgrandtotal = eval( formula );
            }
		}

		if ( $('.option-group.total[data-qty-totals]').length && quantity ) {
			grandtotal = grandtotal * quantity;
			altgrandtotal = altgrandtotal * quantity;
		}

		if ( $('.option-group.total #totaltaxprice').length ) {
			if ( 'incl' == PRODUCT_TAX_DISPLAY && PRODUCT_TAX ) {
				// price does includes tax. price is shown as inclusive of tax. Show excluding tax.
				var taxtotal = grandtotal / (1 + (PRODUCT_TAX / 100));
				$('.option-group.total #totaltaxprice').html(_formatPriceOutput(taxtotal, '', true) + ' ' + ALT_TAX_PRICE_SUFFIX);
			} else if ('excl' == PRODUCT_TAX_DISPLAY && PRODUCT_TAX ) {
				// price includes tax. price is shown as exclusive of tax. Show including tax.
				var taxtotal = grandtotal + (grandtotal * (PRODUCT_TAX / 100));
				$('.option-group.total #totaltaxprice').html(_formatPriceOutput(taxtotal, '', true) + ' ' + ALT_TAX_PRICE_SUFFIX);
			}
		}

		var grandtotaltaxhtml = _formatPriceOutput(grandtotal, '', true);
		var grandtotalhtml = _formatPriceOutput(grandtotal, '', true);
		if ( altgrandtotal > grandtotal ) {
			grandtotaltaxhtml += '<del>' + _formatPriceOutput(altgrandtotal, '', true) + '</del>';
			grandtotalhtml += '<del>' + _formatPriceOutput(altgrandtotal, '', true) + '</del>';
		}
		grandtotaltaxhtml += ' ' + TAX_PRICE_SUFFIX;

		$('.option-group.total #totalprice').html(grandtotaltaxhtml);
		$('.staggs-product-options-basic #totalprice').html(grandtotaltaxhtml);
		$('.staggs-configurator-bottom-bar #totalprice').html(grandtotaltaxhtml);

		if (DISABLE_PRODUCT_PRICE_UPDATE) {
			// Update disabled.
		} else {
			// Staggs template
			if ( $('.option-group.intro .price').length ) {
				$('.option-group.intro .price').html(grandtotaltaxhtml);
			}
			// Woo Template
			if ( $('.entry-summary .price').length && ! $('.staggs-configurator-popup').length ) {
				$('.entry-summary .price').html(grandtotaltaxhtml);
			}
		}

		if ( $('#product_weight').length ) {
			var weight = PRODUCT_WEIGHT;
			var options = getConfiguratorOptionValues();

			options.forEach(function(item,key) {
				if ( item.weight ) {
					weight += item.weight;
				}
			});

			$('#product_weight').text(weight + ' ' + PRODUCT_WEIGHT_UNIT);
		}

		if ( $('.option-group-step-buttons').length ) {
			var validStep = _validateStepFields();

			if ( activeStep < maxStep && validStep ) {
				$('.staggs-step-next-button').removeClass('disabled');
			} else {
				$('.staggs-step-next-button').addClass('disabled');
			}

			_setTotalsButton(validStep);
		}

		if ( $('#configurator-options input[data-sku-format]').length || $('#configurator-options option[data-sku-format]').length ) {
			_setAttributeOptionSkus();
		}

		if ( $('.staggs-summary-widget').length ) {
			_updateSummary();
		}

		if ( $('.sgg_field_summary').length ) {
			_updateFormFieldSummary();
		}

		if ( $('#staggs-send-email').length ) {
			_updateEmailBodySummary();
		}

        // Update Pixels object.
        if ( window.pysWooProductData && window.pysWooProductData[ PRODUCT_ID ] ) {
			if ( window.pysWooProductData[ PRODUCT_ID ].google_ads ) {
				window.pysWooProductData[ PRODUCT_ID ].google_ads.params.value = grandtotal;
			}
			if ( window.pysWooProductData[ PRODUCT_ID ].facebook ) {
        		window.pysWooProductData[ PRODUCT_ID ].facebook.params.value = grandtotal;
			}
			if ( window.pysWooProductData[ PRODUCT_ID ].ga ) {
        		window.pysWooProductData[ PRODUCT_ID ].ga.params.value = grandtotal;
			}
        }
	}

	function _updateFormFieldSummary() {
		var summary_field_val = '';

		var options = getConfiguratorOptionValues();

		if ( options.length ) {
			options.forEach(function(option,index) {

				if ( Array.isArray( option.value ) ) {
					// Single value.
					var groupval = '' + option.label + ':\n';

					option.value.forEach(function(subvalue,subindex) {
						if ( $('input[name="' + subvalue.id + '"]').parents('.input-field-wrapper').length ) {
							var unit = '';
							if ( $('input[name="' + subvalue.id + '"]').siblings('.unit').length ) {
								unit = ' ' + $('input[name="' + subvalue.id + '"]').siblings('.unit').text();
							}
							groupval += '\n' + $.trim( subvalue.label ) + ':' + subvalue.value + unit;

						} else if ( subvalue.label && subvalue.value ) {
							var subvalue_note = '';
							if ( subvalue.note && SUMMARY_SHOW_NOTES ) {
								subvalue_note = ' (' + subvalue.note + ')';
							}
		
							var displayvalue = subvalue.value;
							if ( subvalue.value.toString().indexOf(';base64,') ) {
								displayvalue = subvalue.value.split('|')[0];
							}
		
							groupval += '\n' + $.trim( subvalue.label ) + ': ' + displayvalue + ' ' + subvalue_note;
						}
					});

					summary_field_val += groupval;
				} else {
					// Single value.
					if ( $('input#' + option.id).parents('.input-field-wrapper').length ) {
						var unit = '';
						if ( $('input#' + option.id).siblings('.unit').length ) {
							unit = ' ' + $('input#' + option.id).siblings('.unit').text();
						}
	
						summary_field_val += $.trim( option.label ) + ': ' + option.value + unit + '\n';
					} else if ( option.label && option.value ) {
						var option_note = '';
						if ( option.note && SUMMARY_SHOW_NOTES ) {
							option_note = ' (' + option.note + ')';
						}
	
						var displayvalue = option.value;
						if ( option.value.toString().indexOf(';base64,') ) {
							displayvalue = option.value.split('|')[0];
						}

						summary_field_val += $.trim( option.label ) + ': ' + displayvalue + ' ' + option_note + '\n';
					}
				}
			});
		} else {
			summary_field_val = EMPTY_SUMMARY_MESSAGE;
		}

		$('.sgg_field_summary input').val(summary_field_val)
		$('.sgg_field_summary textarea').val(summary_field_val)
	}

	function _updateSummary() {
		var options = getConfiguratorOptionValues();
		var existingLabels = [];

		if ( options.length ) {
			$('.staggs-summary-items').html('');

			var hiddenItems = [];
			if ( $('.staggs-summary-items').data('hidden') ) {
				hiddenItems = $('.staggs-summary-items').data('hidden').toString().split(',').map((val) => $.trim(val));
			}

			options.forEach(function(option,index) {
				if ( ! option.hidden && ! hiddenItems.includes( $.trim(option.label) ) ) {
					if ( Array.isArray( option.value ) ) {
						// Single value.
						var groupval = '<li class="summary-items-' + slugify(option.label) + '"><strong>' + option.label + ':</strong>';

						option.value.forEach(function(subvalue,subindex) {
							if ( $('input[name="' + subvalue.id + '"]').parents('.input-field-wrapper').length ) {
								var unit = '';
								if ( $('input[name="' + subvalue.id + '"]').siblings('.unit').length ) {
									unit = ' ' + $('input[name="' + subvalue.id + '"]').siblings('.unit').text();
								}
			
								groupval +=
									'<br><p>' + $.trim( subvalue.label )
									+ ':<span>' + subvalue.value + unit + '</span></p>';

							} else if ( subvalue.label && subvalue.value ) {
								var subvalue_note = '';
								if ( subvalue.note && SUMMARY_SHOW_NOTES ) {
									subvalue_note = '<small>' + subvalue.note + '</small>';
								}

								var displayvalue = subvalue.value;
								if ( subvalue.value.toString().indexOf(';base64,') ) {
									displayvalue = subvalue.value.split('|')[0];
								}
			
								groupval +=
									'<br><p>' + $.trim( subvalue.label )
									+ ': <span>' + displayvalue + '</span> ' + subvalue_note + '</p>';
							}
						});

						$('.staggs-summary-items').append( groupval );
					} else {
						// Single value.
						if ( $('input#' + option.id).parents('.input-field-wrapper').length ) {
							var unit = '';
							if ( $('input#' + option.id).siblings('.unit').length ) {
								unit = ' ' + $('input#' + option.id).siblings('.unit').text();
							}

							var label = $.trim( option.label );
							$('.staggs-summary-items').append(
								'<li class="summary-items-' + slugify(label) + '"><strong>' + label + ':</strong> <p><span>' + option.value + unit + '</span></p></li>'
							);
		
						} else if ( option.label && option.value && ! $('input[name="' + option.name + '"]').data('title') ) {

							var label = $.trim( option.label );
							var option_note = '';
							if ( option.note && SUMMARY_SHOW_NOTES ) {
								option_note = '<small>' + option.note + '</small>';
							}

							var displayvalue = option.value;
							if ( option.value.toString().indexOf(';base64,') ) {
								displayvalue = option.value.split('|')[0];
							}

							if ( ! existingLabels.includes(label) ) {
								$('.staggs-summary-items').append(
									'<li class="summary-items-' + slugify(label) + '"><strong>' + label + ':</strong> <p><span>' + displayvalue + '</span> ' + option_note + '</p></li>'
								);
							} else {
								$('.staggs-summary-items').append(
									'<li class="summary-items-' + slugify(label) + '"><p><span>' + displayvalue + '</span> ' + option_note + '</p></li>'
								);
							}

							if ( SUMMARY_SINGLE_TITLE ) {
								existingLabels.push(label);
							}
						}
					}
				}
			});
		} else {
			$('.staggs-summary-items').html('<li>' + EMPTY_SUMMARY_MESSAGE + '</li>');
		}

		$.event.trigger({
			type: "staggs_product_summary_updated",
		});
	}

	function _updateEmailBodySummary( append = '' ) {
		var options = getConfiguratorOptionValues()
		var existingLabels = [];
		var fullEmailLink = $('#staggs-send-email').attr('href');
		var baseLink = fullEmailLink.split('body=')[0];
		var emailLink = baseLink + 'body=';
		var emailBody = '';
		if ( $('#staggs-send-email').data('title') ) {
			emailBody = $('#staggs-send-email').data('title') + '%0D%0A';
		}

		if ( $('#staggs-send-email').data('include_pdf') ) {
			$('#staggs-send-email').addClass('sgg-generate-pdf');
		}

		if ( options.length ) {
			options.forEach(function(option,index) {
				if ( $('input#' + option.id).parents('.input-field-wrapper').length ) {
					var unit = '';
					if ( $('input#' + option.id).siblings('.unit').length ) {
						unit = ' ' + $('input#' + option.id).siblings('.unit').text();
					}

					emailBody += $('input#' + option.id).parents('.input-field-wrapper').find('.input-title').text() + ':%20' + option.value + unit + '%0D%0A';
				} else if ( $('div[data-step="' + option.step_id + '"] .option-group-header').find('.title').text() ) {
					var displayvalue = option.value;
					if ( option.value.toString().indexOf(';base64,') ) {
						displayvalue = option.value.split('|')[0];
					}
					var displaylabel = $('div[data-step="' + option.step_id + '"] .option-group-header').find('.title').text();

					if ( ! existingLabels.includes(displaylabel) ) {
						emailBody += displaylabel + ':%20' + displayvalue + '%0D%0A';
					} else {
						emailBody += displayvalue + '%0D%0A';
					}

					if ( SUMMARY_SINGLE_TITLE ) {
						existingLabels.push(displaylabel);
					}
				}
			});
		}

		if ( '' !== append) {
			emailBody += '%0D%0A' + append;
		}

		$('#staggs-send-email').attr('href', emailLink + emailBody);
	}

	function _updateProductPageDetails() {
		var newPreview = $('.staggs-product-view .swiper-slide-active').html();
		var galleryImage = $('.woocommerce-product-gallery__wrapper').find('.woocommerce-product-gallery__image:first-of-type');
		
		// Update regular page details.
		if ( galleryImage.length ) {
			galleryImage.html(newPreview);
			
			$('.woocommerce-product-gallery__trigger').hide();

			if ( galleryImage.attr('data-thumb') ) {
				galleryImage.attr('data-thumb', '');
			}
		}

		// Regular WooCommerce Single price
		if ( $('.entry-summary p.price').length && ! DISABLE_PRODUCT_PRICE_UPDATE ) {	
			var totalPrice = getConfiguratorTotals();
			$('.entry-summary p.price').html(_formatPriceOutput(totalPrice, '', true));
		}
		// Elementor Single Price template
		if ( $('.wd-single-price p.price').length && ! DISABLE_PRODUCT_PRICE_UPDATE ) {	
			var totalPrice = getConfiguratorTotals();
			$('.wd-single-price p.price').html(_formatPriceOutput(totalPrice, '', true));
		}

		$('.staggs-configure-product-button').parents('form').find('.single_add_to_cart_button').addClass('staggs-popup-cart-action');

		// Close popup.
		$('.staggs-configurator-popup').removeClass('active');
		$('body').removeClass('staggs-popup-active');
		$('.staggs-message-wrapper').addClass('inline');
	}

	function _saveToWishlist(wishlist_item, $button) {
		var wishlist_item_json = JSON.stringify(wishlist_item);
		wishlist_items.push(wishlist_item_json);

		var iconHtml = $button.find('.wishlist-icon').html();
		if (SGG_LOADER_ICON){
			$button.find('.wishlist-icon').html(SGG_LOADER_ICON);
		}

		$.ajax({
			type: 'post',
			url: AJAX_URL,
			data: {
				action: 'staggs_save_wishlist_item_for_user',
				wishlist: wishlist_items,
			},
            success: function (data) {
				// Show message.
				$('.woocommerce-notices-wrapper').html(
					'<div class="woocommerce-message" role="alert"><a href="' + WISHLIST_PAGE_URL + '" class="button wc-forward">' + VIEW_WISHLIST_BUTTON_TEXT + '</a>' + WISHLIST_NOTICE_MESSAGE + '<a href="#0" class="hide-notice"></a></div>'
				);
				$('.staggs-message-wrapper').addClass('active');

				if (noticeTimeout) {
					clearTimeout(noticeTimeout);
				}
				noticeTimeout = setTimeout(function() {
					$('.staggs-message-wrapper').removeClass('active');
					$('.staggs-message-wrapper').find('.woocommerce-notices-wrapper').html('');
				}, 12000);

				$button.find('.wishlist-icon').html(iconHtml);
			}
		});
	}

	function _saveAndShowSummary(summary_obj) {
		var summary_obj_json = JSON.stringify(summary_obj);

		$.ajax({
			type: 'post',
			url: AJAX_URL,
			data: {
				action: 'staggs_save_configuration_to_file',
				contents: summary_obj_json,
			},
            success: function (data) {
				// Show message.
				var response = JSON.parse(data);
				var product_base_url = window.location.origin + window.location.pathname;
				var options = '?staggs_summary=' + response.filename;

				window.location = product_base_url + options;
			}
		});
	}

	var _getSelectMenuOptions = function() {
		return {
			position: { 
				my: 'left top', 
				at: 'left bottom',
				collision: 'flip',
				using: function (obj,info) {
					if (info.vertical != "top") {
						$(this).addClass("flipped");
						$(info.target.element[0]).addClass("flipped");
					} else {
						$(this).removeClass("flipped");
						$(info.target.element[0]).removeClass("flipped");
					}
					$(this).css({
						left: obj.left + 'px',
						top: obj.top + 'px'
					});
				},
			},
			change: function( event, ui ) {
				$(event.target).trigger('change');
			}
		};
	}

	var _selectMenuItemRenderer = function(ul, item) {
		var li = $( "<li>" );
		if ( item.disabled ) {
			li.addClass( "ui-state-disabled" );
		}
		if ( $(item.element[0]).hasClass('hidden') ) {
			return ul;
		}
		var option = $("<div>" + item.label + "</div>");
		if ( $(item.element[0]).attr('style') ) {
			option.attr('style', $(item.element[0]).attr('style').replaceAll(';;',';') );
			if ( $(item.element[0]).data('input-key') ) {
				_updatePreviewTextFont( $(item.element[0]) );
			}
		}
		return li.append(option).appendTo( ul );
	};

	var _selectMenuButtonRenderer = function( item ) {
		var buttonItem = $( "<span>", {
		  "class": "ui-selectmenu-text"
		});
		this._setText( buttonItem, item.label );
		if ( $(item.element[0]).attr('style') ) {
			buttonItem.attr( "style", $(item.element[0]).attr('style').replaceAll(';;',';') );
			if ( $(item.element[0]).data('input-key') ) {
				_updatePreviewTextFont( $(item.element[0]) );
			}
		}
		return buttonItem;
	}

	function _resizeTextToFit($preview, baseFontSize, action) {
		var spanFontSize = parseInt( $preview.find("span").css("font-size") );
		var previewCount = 0;

		$preview.find('.preview-text-input').each(function(index,textpreview) {
			if ( $(textpreview).text() !== '' ) {
				previewCount++;
			}
		});

		if ( 'none' !== $preview.css('maxHeight') && previewCount > 0 ) {
			$preview.css('height', $preview.css('maxHeight'));
			/**
			 * Bundled preview
			 */
			baseFontSize = Math.floor( parseInt( $preview.css('height') ) / previewCount );
			var fontHeightRatio = parseFloat($preview.css('lineHeight')) / parseFloat($preview.css('fontSize'));
			baseFontSize = Math.floor( baseFontSize / fontHeightRatio );
			$preview.find("span").css("font-size", baseFontSize);

			$preview.find('.preview-text-input').each(function(index,textpreview) {
				if ( $(textpreview).text() === '' ) {
					$(textpreview).addClass('hidden');
					return;
				} else {
					$(textpreview).removeClass('hidden');
				}
	
				while ( $(textpreview).find("span").width() > $preview.width() ) {
					spanFontSize = parseInt( $preview.find("span").css("font-size") );
					$preview.find("span").css("font-size", spanFontSize - 1);
				}
			});
		} else if ( $preview.attr('style') && $preview.attr('style').includes('width') ) {
			/**
			 * Single text preview
			 */
			$preview.find('.preview-text-input').each(function(index,textpreview) {
				if ( $(textpreview).text() === '' ) {
					return;
				}

				if ( $(textpreview).find("span").width() > $preview.width() ) {

					while ( $(textpreview).find("span").width() > $preview.width() ) {
						spanFontSize = parseInt( $preview.find("span").css("font-size") );
						$preview.find("span").css("font-size", spanFontSize - 1);
					}

				} else if ( $(textpreview).find("span").width() < $preview.width() && spanFontSize < baseFontSize ) {

					while ( $(textpreview).find("span").width() < $preview.width() && spanFontSize < baseFontSize ) {
						spanFontSize = parseInt( $preview.find("span").css("font-size") );
						$preview.find("span").css("font-size", spanFontSize + 1);
					}

				}
			});
		}
	}

	function _formatPriceOutput(price, taxLabel = '', disableSign = false) {
		var totalPrice = parseFloat(price),
			htmlPrice;

		totalPrice = totalPrice.toFixed(NUMBER_OF_DECIMALS);
		htmlPrice = totalPrice.toString().replace('.', DECIMAL_SEPARATOR);

		if (THOUSAND_SEPARATOR.length > 0) {
			htmlPrice = _addThousandSep(htmlPrice);
		}

		var isNegative = false;
		if (totalPrice < 0) {
			htmlPrice = htmlPrice.substring(1);
			isNegative = true;
		}

		if (TRIM_PRICE_DECIMALS) {
			htmlPrice = htmlPrice.replace( DECIMAL_SEPARATOR + '00', DECIMAL_SEPARATOR + '-' );
		}

		if (CURRENCY_POS == 'right') {
			htmlPrice = htmlPrice + CURRENCY_SYMBOL;
		} else if (CURRENCY_POS == 'right_space') {
			htmlPrice = htmlPrice + ' ' + CURRENCY_SYMBOL;
		} else if (CURRENCY_POS == 'left_space') {
			htmlPrice = CURRENCY_SYMBOL + ' ' + htmlPrice;
		} else {
			htmlPrice = CURRENCY_SYMBOL + htmlPrice;
		}

		if ( ! disableSign && ! isNegative && 0 != price ) {
			htmlPrice = '<span class="sign">' + PRODUCT_PRICE_SIGN + '</span>' + htmlPrice;
		}
		if ( isNegative ) {
			htmlPrice = '-' + htmlPrice;
		}

		return '<span class="amount">' + htmlPrice + '</span>' + taxLabel;
	}

	function _addThousandSep(n) {
		var rx = /(\d+)(\d{3})/;
		return String(n).replace(/^\d+/, function (w) {
			while (rx.test(w)) {
				w = w.replace(rx, '$1' + THOUSAND_SEPARATOR + '$2');
			}
			return w;
		});
	}

	/**
	 * Misc
	 */

    function _isImageExt(fileExt) {
        // Check the file's MIME type
        const validImageExts = ['jpeg', 'jpg', 'png', 'webp', 'gif', 'tiff', 'bmp', 'eps', 'svg', 'ico', 'heif', 'heic'];
        return validImageExts.includes(fileExt);
    }

    function _isImageFile(file) {
        // Check the file's MIME type
        const mimeType = file.type;
        const validImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/eps', 'image/ico', 'image/tiff', 'image/bmp', 'image/svg+xml', 'image/heif', 'image/heic'];
        return validImageTypes.includes(mimeType);
    }

	function _captureAndDownloadImage() {
		if ( $('#preview_slide_0').length ) {
			modernScreenshot.domToPng(document.getElementById('preview_slide_0'))
				.then(function(dataUrl) {
					var title = 'capture';
					var anchor = document.createElement('a');
					anchor.style.display = 'none';
					document.body.appendChild(anchor);
					anchor.href = dataUrl;
					anchor.download = title + '.png';
					anchor.click();
					window.setTimeout(() => {
						document.body.removeChild(anchor);
					}, 100);
				})
				.catch(function (error) {
					console.error('oops, something went wrong!', error);
				});
		} else if ( $('.staggs-view-gallery model-viewer').length ) {
			var dataUrl = document.getElementById('product-model-view').toDataURL();
			var title = 'capture';
			var anchor = document.createElement('a');
			anchor.style.display = 'none';
			document.body.appendChild(anchor);
			anchor.href = dataUrl;
			anchor.download = title + '.png';
			anchor.click();
			window.setTimeout(() => {
				document.body.removeChild(anchor);
			}, 100);
		}
	}

	function _updateOptionMaterialCanvas(name, values) {
		if ( $('.option-group-canvas[data-key="' + name + '"]').length ) {
			var canvasId = $('.option-group-canvas[data-key="' + name + '"]').attr('id');

			if ( sgg_canvasses.hasOwnProperty(canvasId) ) {
				var objectId = canvasId.replace('_canvas', '');
				var text = _getCanvasObjectById(objectId, canvasId);

				if ( text.length ) {
					for ( var prop in values ) {
						text[0].set( prop, values[prop] );
					}
					
					sgg_canvasses[canvasId].renderAll();
					var dataURL = sgg_canvasses[canvasId].toDataURL();
	
					var material = $('.option-group-canvas[data-key="' + name + '"]').closest('.option-group').data('model');
					if ( ! material ) {
						material = $('.option-group-canvas[data-key="' + name + '"]').closest('.option-group').find('input').data('material-key');
					}

					var textureChannel = $('.option-group-canvas[data-key="' + name + '"]').closest('.option-group').data('model-material');
					if ( ! textureChannel ) {
						textureChannel = 'base';
					}
					
					$.event.trigger({
						type: "modelMaterialChanged",
						model: material,
						channel: textureChannel,
						texture: dataURL,
					});
				}
			}
		}
	}

	function _getCanvasObjectById( id, cIndex = 1 ) {
		if ( typeof sgg_canvasses === 'undefined' ) {
			return {};
		}
		if ( ! sgg_canvasses[cIndex] ) {
			return {};
		}
		return sgg_canvasses[cIndex].getObjects().filter(obj => obj.id === id);
	}

	function slugify(str) {
		str = str.replace(/^\s+|\s+$/g, '');
		str = str.toLowerCase();
		str = str.replace(/[^a-z0-9 -]/g, '')
				 .replace(/\s+/g, '-')
				 .replace(/-+/g, '-');
		return str;
	  }

	document.addEventListener('fullscreenchange', exitHandler);
	document.addEventListener('webkitfullscreenchange', exitHandler);
	document.addEventListener('mozfullscreenchange', exitHandler);
	document.addEventListener('MSFullscreenChange', exitHandler);

	function exitHandler() {
		if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
			$('.staggs-preview-actions button.fullscreen').removeClass('active');
		}
	}

	function toggleFullScreen(el) {
		if (!el) {
			el = document.body; // Make the body go full screen.
		}
		var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) || (document.mozFullScreen || document.webkitIsFullScreen);
		if (isInFullScreen) {
			$('.staggs-preview-actions button.fullscreen').removeClass('active');
			cancelFullScreen();
		} else {
			$('.staggs-preview-actions button.fullscreen').addClass('active');
			requestFullScreen(el);
		}
		return false;
	}

	function cancelFullScreen() {
		var el = document;
		var requestMethod = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullscreen || el.webkitExitFullscreen;
		if (requestMethod) { // cancel full screen.
			requestMethod.call(el);
		} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
			var wscript = new ActiveXObject("WScript.Shell");
			if (wscript !== null) {
				wscript.SendKeys("{F11}");
			}
		}
	}

	function requestFullScreen(el) {
		// Supports most browsers and their versions.
		var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullscreen;
		if (requestMethod) { // Native full screen.
			requestMethod.call(el);
		} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
			var wscript = new ActiveXObject("WScript.Shell");
			if (wscript !== null) {
				wscript.SendKeys("{F11}");
			}
		}
		return false;
	}

	window.staggsGetTotalConfigurationImage = function staggsGetTotalConfigurationImage() {
		return renderFinalProductImage();
	}

	window.staggsGetTotalConfigurationPrice = function staggsGetTotalConfigurationPrice() {
		return getConfiguratorTotals();
	}

	window.staggsGetTotalConfigurationOptions = function staggsGetTotalConfigurationOptions() {
		return getConfiguratorOptionValues();
	}

	jQuery.staggsSyncTextInputs = function syncTextInputs( $input, modifier = '' ) {
		_syncTextInputs( $input, modifier );
	}

})(jQuery);
