;(function($, window, document, undefined) {
	var $win = $(window),
		$doc = $(document),
		$page, $html, $body, $wrapper;

	$doc.ready(function() {
		$page = $('html, body');
		$html = $('html');
		$body = $('body');
		$wrapper = $('.wrapper');

		$('.header-search input:submit').on('click', function(event) {
			var $submit = $(this),
				$searchForm = $submit.closest('.header-search'),
				$input = $submit.closest('form').find('input[type="text"], input[type="search"]');

			if ($input.val() === '' || $searchForm.hasClass('collapse')) {
				$searchForm
						.toggleClass('collapse');

				if (! $searchForm.hasClass('collapse') ) {
					$searchForm.find('.search-field').focus();
				};
				
				event.preventDefault();
			};
		});

		$('.slider .slider-clip').flexslider({
			slideshow: true,
			animation: 'fade',
			slideshowSpeed: 7000,
			animationSpeed: 600,
			controlNav: false,
			directionNav: false,
			after: function(slider) {
				var $textContainers = $('.slider-post-text .text-container');
				$textContainers.removeClass('visible');
				$textContainers.eq(slider.currentSlide).addClass('visible');
			}
		});

		$('.nav-toggle').on('click' , function(event) {

			$body.toggleClass('show-nav');
			
			event.preventDefault();
		});

		$doc.on('touchend' , '.show-nav .wrapper', function(event) {
			if ($wrapper.is(event.target)) {
				$body.removeClass('show-nav');
			};
		});

		$('.widget_archive li').each(function() {
			var html = $(this).html();
			html = html.replace('</a>', '<i>');
			html += '</i></a>';
			html = html.replace(/<i>&nbsp;\((\d+)\)<\/i>/, '<i>$1</i>');
			$(this).html(html);
		});

		$('.widget_archive .older-posts a').on('click', function() {
			var $this = $(this);
			var $widget = $this.closest('.widget_archive');
			$this.parent().removeClass('older-posts').hide();
			return false;
		});

		$('#more').on('click', function() {
			$.ajax({
				url: ajaxurl,
				dataType: 'html',
				data: "action=get_more_comments&post_id=" + $('#post_id').attr('value') + "&offset=" + $('#offset').attr('value'),
				success: function(data){
					var newLimit = comments_per_page + parseInt($('#offset').attr('value'));
					var page = 1 + parseInt($('#page').attr('value'));
					var pages = parseInt($('#pages').attr('value'));
					if(page >= pages){
						$('#more').hide();
					}
					$('#offset').attr('value', newLimit);
					$('#page').attr('value', page);
					$('#commentList').append(data);
				},
				error: function() {}
			});
		});

		var touch_start = 0,
			touch_end = 0;
		$doc.on('touchstart touchmove touchend', '.show-nav', function(event) {
			var cursor = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0],
				cursorX = cursor.pageX,
				cursorY = cursor.pageY;

			if (event.type === 'touchstart') {
				touch_start = cursorX;
			} else if(event.type === 'touchmove') {
				touch_end = cursorX;
			} else {
				if (touch_end > touch_start && touch_end - touch_start > 80) {
					$body.removeClass('show-nav');

					touch_start = 0;
					touch_end = 0;
				};
			};
		});
	});
})(jQuery, window, document);
