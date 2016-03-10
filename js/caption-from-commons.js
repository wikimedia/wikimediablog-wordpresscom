(function ($) {
	$(function() {
		
		$('#loadImage').on('click', function(){
			insert_caption();
			return false;
		});

		function insert_caption() {
			$.ajax({
				url: ajaxurl,
				dataType: 'html',
				type: 'POST',
				data: {
					"action": "get_commons_data",
					"url": $('#src').attr('value'),
					"post_id":  post_id },
				success: function(data){
					$('#succes').html(data);
				},
				error: function() {
					$('#succes').html('<div style="color:red;max-width: 800px;">Unable to find the image using the Commons API. Please be sure to use a file URL where the width is specified.</i></div>');
				}
			});
		}
	});
})(jQuery);