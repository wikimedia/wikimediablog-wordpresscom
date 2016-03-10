<?php

add_action( 'wp_head', 'wmb_additional_header_scripts' );
function wmb_additional_header_scripts() {
	?>
	<script>
		!function () {
			function supportsSVG() { return !!document.createElementNS && !! document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect }
			if (supportsSVG()) document.documentElement.className += ' svg'
		}();

		var comments_per_page = <?php echo wp_json_encode(DEFAULT_COMMENTS_PER_PAGE); ?>;
	</script>
	<?php
}