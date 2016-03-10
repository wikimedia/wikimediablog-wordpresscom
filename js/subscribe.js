(function($) {
	
	$(function() {    
		var email_placeholder = 'Your email address';

		$('#mce-EMAIL').focus( function() {
			if ( $(this).val() == email_placeholder ) {
				$(this).val( '' );
			}

			$('.mc-field-group.input-group').show();
		}).blur( function() {
			if ( $(this).val() == '' ) {
				$(this).val( email_placeholder );
			}
		});
	});

})(jQuery);