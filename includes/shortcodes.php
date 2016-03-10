<?php 

add_shortcode( 'image', 'wmb_shortcode_image' );
function wmb_shortcode_image( $atts, $content = '' ) {
	# Make sure image URL is specified
	if ( empty( $atts['url'] ) ) {
		return;
	}

	# Possible alignments
	$alignments = array(
		'left',
		'right',
		'center',
	);

	# Determine CSS class
	$class = 'alignnone';
	if ( isset( $atts['align'] ) && in_array( $atts['align'], $alignments, true ) ) {
		$class = 'align' . $atts['align'];
	}

	# Determine margin
	$margin = '';
	$margin_alignments = array(
		'left',
		'right',
	);
	if ( ! empty( $atts['offset'] ) && in_array( $atts['align'], $margin_alignments, true ) ) {
		$offset = intval( $atts['offset'] );
		$margin = 'margin-' . $atts['align'] . ': ' . $offset . 'px;';
	}

	# Start rendering
	ob_start();
	
	?>
	<div class="image <?php echo esc_attr( $class ); ?>" style="<?php echo esc_attr( $margin ); ?>">
		<img src="<?php echo esc_url( $atts['url'] ); ?>" alt="" />
	</div><!-- /.image -->
	<?php

	# End rendering
	$html = ob_get_clean();

	return $html;
}

# Custom caption shortcode
add_shortcode( 'caption', 'wmb_shortcode_caption' );
function wmb_shortcode_caption( $attr, $content = null ) { 

	// New-style shortcode with the caption inside the shortcode with the link and image tags.
    if ( ! isset( $attr['caption'] ) ) {
        if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
                $content = $matches[1];
                $attr['caption'] = trim( $matches[2] );
        }
    }

    $id = ! empty( $attr['id'] ) ? $attr['id'] : '';
    $align = ! empty( $attr['align'] ) ? $attr['align'] : 'alignnone';
    $width = ! empty( $attr['width'] ) ? $attr['width'] : '';
    $caption = ! empty( $attr['caption'] ) ? $attr['caption'] : '';

    if ( 1 > (int) $width || empty($caption) ) {
    	return wp_kses_post( $content );
    }

    if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

    return '<div ' . $id . 'class="post-image wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
    . do_shortcode( $content ) . '<p class="wp-caption-text">' . wp_kses_post( $caption ) . '</p></div>';

}

# "Powered by WordPress.com VIP" shortcode.
add_shortcode('powered_by_vip', 'wmb_powered_by_vip');

function wmb_powered_by_vip() {
	return vip_powered_wpcom();
}