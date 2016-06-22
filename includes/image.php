<?php 

# Display a header featured image along with its background
function wmb_featured_image_with_background( $image_id, $size = 'full' ) {
	$image = wp_get_attachment_image_src( $image_id, 'featured_slider_' . $size );
	?>
	<span class="image" style="background-image: url(<?php echo esc_attr( $image[0] ); ?>);" ></span>
	<?php
}

# Display header featured image credits
function wmb_featured_image_credits( $image_id ) {
	$image_content = get_post_field( 'post_excerpt', $image_id );
	if ( ! $image_content ) {
		return;
	}
	?>
	<div class="author-credentials">
		<i><small><?php echo apply_filters( 'the_content', wp_kses_post($image_content) ); ?></small></i>
	</div>						
	<?php
}

# Retrieve the URL of the default featured image of the current post
function wmb_get_post_default_featured_image() {
	if ( ! is_singular() ) {
		return;
	}

	global $post;

	if ( in_category( 'technology' ) ) {
		$img = '_wiki_technology_banner.png';
	} elseif ( in_category( 'foundation' ) ) {
		$img = '_wiki_foundation_banner.png';
	} elseif ( in_category( 'community' ) ) {
		$img = '_wiki_community_banner.png';
	} elseif ( in_category( 'wikipedia' ) ) {
		$img = '_wiki_category_banner.png';
	} else {
		$img = '_abstract_3.png';
	}

	return get_stylesheet_directory_uri() . '/images/' . $img;
}