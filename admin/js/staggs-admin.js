jQuery(document).ready(function ($) {

	/**
	 * Import/Export Attribute
	 */

	$('.sgg_attribute_page_tools select[name=separator]').on('change', function() {
		var val = $(this).val();
		if ( 'other' == val ) {
			$(this).next('input').attr('disabled', false);
			$(this).next('input').val('');
		} else {
			$(this).next('input').attr('disabled', true);
			$(this).next('input').val($(this).val());
		}
	});

});
