jQuery(document).ready(function ($) {

	/**
	 * Staggs configurable product
	 */

	if ( $('input[name=is_configurable]').length ) {
		if ( $('input[name=is_configurable]').is(':checked') ) {
			$('#carbon_fields_container_staggs_configurator_builder').removeClass('hidden');
		} else {
			$('#carbon_fields_container_staggs_configurator_builder').addClass('hidden');
		}
	}

	$('#carbon_fields_container_configurator_data .cf-complex__group .cf-complex__group .cf-complex__group-head').removeClass('ui-sortable-handle');

	$(document).on('change', 'input[name=is_configurable]', function() {
		if ( $(this).is(':checked') ) {
			$('#carbon_fields_container_staggs_configurator_builder').removeClass('hidden');
		} else {
			$('#carbon_fields_container_staggs_configurator_builder').addClass('hidden');
		}
	});

	/**
	 * Staggs Attribute
	 */

	// Attribute first option select.

	$('#carbon_fields_container_staggs_configurator_builder .base-attribute-option-select:not([hidden]) select').each(function (index, select) {
		filterAttributeSelectValues( $(select).parents('.base-attribute-option-select').siblings('.base-attribute-select').find('select'), $(select) );
	});

	$(document).on('change', '#carbon_fields_container_staggs_configurator_builder .base-attribute-select select', function () {
		if ( $(this).parents('.base-attribute-select').siblings('.base-attribute-option-select:not([hidden])').length ) {
			populateAttributeSelectValues($(this), $(this).parents('.base-attribute-select').siblings('.base-attribute-option-select').find('select') );
		}

		if ( $(this).parents('.base-attribute-select').siblings('.base-attribute-option-rules:not([hidden])').length ) {
			var $select = $(this);

			$(this).parents('.base-attribute-select')
				.siblings('.base-attribute-option-rules:not([hidden])')
				.find('.conditional-attribute-option-select select')
				.each(function(index,optionSelect) {

				populateAttributeSelectValues(
					$select,
					$(optionSelect)
				);
			});
		}
	});

	$(document).on('change', '#carbon_fields_container_staggs_configurator_builder .default-attribute-value-checkbox input', function () {
		if ( $(this).parents('.default-attribute-value-checkbox').siblings('.base-attribute-option-select').length ) {
			populateAttributeSelectValues(
				$(this).parents('.default-attribute-value-checkbox').siblings('.base-attribute-select').find('select'), 
				$(this).parents('.default-attribute-value-checkbox').siblings('.base-attribute-option-select').find('select')
			);
		}
	});

	// Attribute conditional logic option select.

	$('#carbon_fields_container_staggs_configurator_builder .conditional-attribute-option-select select').each(function (index, select) {
		var $attributeSelect = $(this).parents('.base-attribute-option-rules').siblings('.base-attribute-select').find('select');

		filterAttributeSelectValues(
			$attributeSelect,
			$(select)
		);

		$(select).addClass('populated');
	});

	$(document).on('click', '.cf-complex__inserter-button', function () {
		$.event.trigger({
			type: 'newComplexGroupAdded'
		});
	});

	$(document).on('newComplexGroupAdded', function() {
		setTimeout(function() {
			// Attribute page.
			$('.base-attribute-option-rules').each(function(index,optionRules) {
				var $attributeSelect = $(optionRules).siblings('.base-attribute-select').find('select');

				$(optionRules).find('.conditional-attribute-option-select select:not(.populated)').each(function(index,optionSelect) {
					populateAttributeSelectValues(
						$attributeSelect,
						$(optionSelect)
					);

					$(optionSelect).addClass('populated');
				});
			});

			// Setting page.
			$('.global-attribute-option-select').each(function(index,optionSelect) {
				var $attributeSelect = $(optionSelect).siblings('.global-attribute-select').find('select');

				filterAttributeSelectValues(
					$attributeSelect,
					$(optionSelect).find('select')
				);
		
				$(optionSelect).addClass('populated');
			});
		}, 10);
	});

	// $(document).on('click', '.cf-container-post-meta .cf-complex__inserter-button', function () {
	// 	if ( $(this).parents('.base-attribute-option-rules').length ) {
	// 		var $attributeSelect = $(this).parents('.base-attribute-option-rules').siblings('.base-attribute-select').find('select');

	// 		$(this).parents('.cf-field__body').find('.conditional-attribute-option-select select:not(.populated)').each(function(index,optionSelect) {
	// 			populateAttributeSelectValues(
	// 				$attributeSelect,
	// 				$(optionSelect)
	// 			);
				
	// 			$(optionSelect).addClass('populated');
	// 		});
	// 	}
	// });

	// Conditional logic select.

	$('#carbon_fields_container_staggs_configurator_builder .conditional-step-select').each(function (index, select) {
		filterAttributeSelectValues($(select).find('select'), $(select).siblings('.conditional-value-select').find('select') );
	});

	$(document).on('change', '#carbon_fields_container_staggs_configurator_builder select', function () {
		if ($(this).parents('.conditional-step-select').length) {
			populateAttributeSelectValues( 
				$(this), 
				$(this).parents('.conditional-step-select').siblings('.conditional-value-select').find('select')
			);
		}
	});

	/**
	 * Settings page
	 */

	// Attribute conditional logic option select.

	$('#carbon_fields_container_settings .global-attribute-select').each(function (index, globalSelect) {
		filterAttributeSelectValues( $(globalSelect).find('select'), $(globalSelect).siblings('.global-attribute-option-select').find('select') );
	});

	$('#carbon_fields_container_settings .conditional-step-select').each(function (index, select) {
		filterAttributeSelectValues( $(select).find('select'), $(select).siblings('.conditional-value-select').find('select') );
	});

	$(document).on('change', '#carbon_fields_container_settings select', function () {
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
	 * Helper functions
	 */

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
