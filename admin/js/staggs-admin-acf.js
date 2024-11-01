jQuery(document).ready(function ($) {

	/**
	 * Staggs attribute allowed types.
	 */
	_setAllowedAttributeTypes( $('div[data-name=sgg_step_template] select'), $('div[data-name=sgg_attribute_type] select') );
	$('div[data-name=sgg_step_template] select').on('change',function() {
		_setAllowedAttributeTypes( $(this), $('div[data-name=sgg_attribute_type] select') );
	});

	var $template_select  = $('div[data-name=sgg_step_template] select');
	var $attr_type_select = $('div[data-name=sgg_attribute_type] select');

	$template_select.on('change', function() {
		$('div[data-name=sgg_attribute_items] .acf-row').each(function(index,row) {
			if ( ! $(this).hasClass('acf-clone') ) {
				if ( 'input' === $attr_type_select.val() ) {
					var $input_type_select = $(this).find('div[data-name=sgg_option_field_type] select');
					_setAllowedInputTypes( $template_select, $input_type_select );
				}
			}
		});
	});

	$('div[data-name=sgg_attribute_items] .acf-row').each(function(index,row) {
		if ( ! $(this).hasClass('acf-clone') ) {
			if ( 'input' === $attr_type_select.val() ) {
				var $input_type_select = $(this).find('div[data-name=sgg_option_field_type] select');
				_setAllowedInputTypes( $template_select, $input_type_select );
			}

			if ( 'no' === $(this).find('div[data-name=sgg_option_base_price] select').val() ) {
				var $input_type_select = $(this).find('div[data-name=sgg_option_field_type] select');
				var $price_type_select = $(this).find('div[data-name=sgg_option_calc_price_type] select');
				_setAllowedInputPrices( $input_type_select, $price_type_select );
			}
		}
	});

	$(document).on('change', 'div[data-name=sgg_option_field_type] select', function() {
		var $row = $(this).parents('.acf-row');
		if ( 'no' === $row.find('div[data-name=sgg_option_base_price] select').val() ) {
			var $price_type_select = $row.find('div[data-name=sgg_option_calc_price_type] select');
			_setAllowedInputPrices( $(this), $price_type_select );
		}
	});

	$(document).on('change', 'div[data-name=sgg_option_base_price] select', function() {
		var $row = $(this).parents('.acf-row');
		if ( 'yes' === $row.find('div[data-name=sgg_option_base_price] select').val() ) {
			var $input_type_select = $row.find('div[data-name=sgg_option_field_type] select');
			var $price_type_select = $row.find('div[data-name=sgg_option_calc_price_type] select');
			_setAllowedInputPrices( $input_type_select, $price_type_select );
		}
	});

	/**
	 * Staggs configurable product
	 */

	if ( $('input[name=is_configurable]').length ) {
		if ( $('input[name=is_configurable]').is(':checked') ) {
			$('div#acf-group_64c55393b75f2').removeClass('hidden');
		} else {
			$('div#acf-group_64c55393b75f2').addClass('hidden');
		}
	}

	$(document).on('change', 'input[name=is_configurable]', function() {
		if ( $(this).is(':checked') ) {
			$('div#acf-group_64c55393b75f2').removeClass('hidden');
		} else {
			$('div#acf-group_64c55393b75f2').addClass('hidden');
		}
	});

	/**
	 * Staggs Attribute
	 */

	// Attribute first option select.

	$('.acf-field.base-attribute-option-select:not(.acf-hidden) select').each(function (index, select) {
		filterAttributeSelectValues( $(select).parents('.base-attribute-option-select').siblings('.base-attribute-select').find('select'), $(select) );
	});

	$(document).on('change', '.acf-field[data-name=sgg_step_attribute] select', function () {
		if ( $(this).parents('.base-attribute-select').siblings('.base-attribute-option-select:not(.acf-hidden)').length ) {
			populateAttributeSelectValues( $(this), $(this).parents('.base-attribute-select').siblings('.base-attribute-option-select').find('select') );
		}
		
		if ( $(this).parents('.base-attribute-select').siblings('.base-attribute-option-rules:not(.acf-hidden)').length ) {
			var $select = $(this);

			$(this).parents('.base-attribute-select')
				.siblings('.base-attribute-option-rules:not(.acf-hidden)')
				.find('.conditional-attribute-option-select select')
				.each(function(index,optionSelect) {

				populateAttributeSelectValues(
					$select,
					$(optionSelect)
				);
			});
		}
	});

	$(document).on('change', '.acf-field[data-name=sgg_step_attribute_default_value] input', function () {
		if ( $(this).parents('.acf-field').siblings('.base-attribute-option-select').length ) {
			populateAttributeSelectValues(
				$(this).parents('.acf-field').siblings('.base-attribute-select').find('select'), 
				$(this).parents('.acf-field').siblings('.base-attribute-option-select').find('select')
			);
		}
	});

	// Conditional logic select.

	$('.acf-field[data-name=sgg_step_conditional_rules] .conditional-step-select').each(function (index, select) {
		filterAttributeSelectValues( $(select).find('select'), $(select).closest('.acf-row').find('.conditional-value-select select') );
	});

	$(document).on('change', '.acf-field[data-name=sgg_step_conditional_rules] select', function () {
		if ( $(this).parents('.conditional-step-select').length ) {
			populateAttributeSelectValues( $(this), $(this).closest('.acf-row').find('.conditional-value-select select') );
		}
	});

	// Option conditional logic select.

	$('.acf-field[data-name=sgg_step_option_conditional_rules] .conditional-step-select').each(function (index, select) {
		filterAttributeSelectValues( $(select).find('select'), $(select).closest('.acf-row').find('.conditional-value-select select') );
	});
	
	$(document).on('change', '.acf-field[data-name=sgg_step_option_conditional_rules] select', function () {
		if ( $(this).parents('.conditional-step-select').length ) {
			populateAttributeSelectValues( $(this), $(this).closest('.acf-row').find('.conditional-value-select select') );
		}
	});

	// Load
	$('.conditional-attribute-option-select select').each(function (index, select) {
		var $attributeSelect = $(this).parents('.base-attribute-option-rules').siblings('.base-attribute-select').find('select');

		if ( ! $(this).parents('.acf-row').hasClass('acf-clone') ) {
			filterAttributeSelectValues(
				$attributeSelect,
				$(select)
			);

			$(select).addClass('populated');
		}
	});

	// Change.
	$(document).on('click', '.acf-field-repeater .acf-actions .button', function () {
		if ( $(this).parents('.base-attribute-option-rules').length ) {
			var $attributeSelect = $(this).parents('.base-attribute-option-rules').siblings('.base-attribute-select').find('select');

			$(this).parents('.acf-field[data-name="sgg_step_option_conditional_display"]')
				.find('.conditional-attribute-option-select select:not(.populated)').each(function(index,optionSelect) {

				populateAttributeSelectValues(
					$attributeSelect,
					$(optionSelect)
				);

				$(optionSelect).addClass('populated');
			});
		}
	});

	/**
	 * Settings page
	 */

	// Attribute conditional logic option select.

	$('.sgg_attribute_page_acf-options-settings .global-attribute-select').each(function (index, globalSelect) {
		filterAttributeSelectValues( $(globalSelect).find('select'), $(globalSelect).siblings('.global-attribute-option-select').find('select') );
	});

	$('.sgg_attribute_page_acf-options-settings .conditional-step-select').each(function (index, select) {
		filterAttributeSelectValues( $(select).find('select'), $(select).siblings('.conditional-value-select').find('select') );
	});

	$(document).on('change', '.sgg_attribute_page_acf-options-settings select', function () {
		if ($(this).parents('.conditional-step-select').length) {
			populateAttributeSelectValues( 
				$(this), 
				$(this).parents('.conditional-step-select').siblings('.conditional-value-select').find('select')
			);
		}

		if ($(this).parents('.global-attribute-select').length) {
			populateAttributeSelectValues( 
				$(this),
				$(this).parents('.global-attribute-select').siblings('.global-attribute-option-select').find('select')
			);
		}
	});

	/**
	 * Helper function
	 */

	function _setAllowedAttributeTypes( $template_select, $type_select ) {
		var template = $template_select.val();
		var attribute_templates = {
			'dropdown': ['image','color','font','link', 'url'],
			'options': ['image','color','font','link','url'],
			'cards': ['image','color','font','link','url'],
			'icons': ['image','link'],
			'swatches': ['image','color','link'],
			'button-group': ['image','color','font','link','url'],
			'tickboxes': ['image','color','font','link','url'],
			'true-false': ['image','color','font','link'],
			'text-input': ['input'],
			'number-input': ['input'],
			'measurements': ['input'],
			'image-upload': ['input'],
			'product': ['link'],
		}
		var allowedTypes = attribute_templates[template];

		$type_select.find('option').each(function(index,option) {
			$(option).attr('disabled', (!allowedTypes.includes($(option).val())));
		});

		if ( $type_select.find('option:selected').is(':disabled') ) {
			$type_select.val(allowedTypes[0]);
			$type_select.trigger('change');
		}
	}

	function _setAllowedInputTypes( $template_select, $type_select ) {
		var template = $template_select.val();
		var input_types = {
			'text-input': ['text','textarea','date'],
			'number-input': ['number','range'],
			'measurements': ['number','range'],
			'image-upload': ['file'],
		}
		var allowedTypes = input_types[template];

		$type_select.find('option').each(function(index,option) {
			$(option).attr('disabled', (!allowedTypes.includes($(option).val())));
		});

		if ( $type_select.find('option:selected').is(':disabled') ) {
			$type_select.val(allowedTypes[0]);
			$type_select.trigger('change');
		}
	}

	function _setAllowedInputPrices( $input_select, $price_select ) {
		var input_type = $input_select.val();
		var input_prices = {
			'text': ['single','unit'],
			'textarea': ['single','unit'],
			'number': ['single','unit','table'],
			'range': ['unit','table'],
		}
		var allowedTypes = input_prices[input_type];

		$price_select.find('option').each(function(index,option) {
			$(option).attr('disabled', (!allowedTypes.includes($(option).val())));
		});

		if ( $price_select.find('option:selected').is(':disabled') ) {
			$price_select.val(allowedTypes[0]);
			$price_select.trigger('change');
		}
	}

	function filterAttributeSelectValues($groupSelect, $select) {
		var groupId = $groupSelect.val();

		$.post(ajaxurl, {
			action: 'get_configurator_attribute_values',
			attribute_id: groupId
		}).done(function (response) {
			var selectItems = JSON.parse(response);
			var entries = Object.entries(selectItems);
			var entryKeys = new Array();

			entries.map(([key, val] = entry) => {
				entryKeys.push(key);
			});

			$select.find('option').each(function (index, option) {
				if (!entryKeys.includes($(option).val())) {
					$(option).remove();
				}
			});
		}).fail(function (error) {
			console.error(error);
		});
	}

	function populateAttributeSelectValues($groupSelect, $select) {
		var groupId = $groupSelect.val();

		$.post(ajaxurl, {
			action: 'get_configurator_attribute_values',
			attribute_id: groupId,
		}).done(function (response) {
			var selectItems = JSON.parse(response);
			var entries = Object.entries(selectItems);

			$select.html('');
			entries.map(([key, val] = entry) => {
				if ( val ) {
					$select.append('<option value="' + key + '">' + val + '</option>');
				}
			});
		}).fail(function (error) {
			console.error(error);
		});
	}

});
