<?php 

function wmb_featured_layouts() {
	return array(
		'image-text' => __( 'Image + Text', 'wmb' ),
		'image-only' => __( 'Image Only', 'wmb' )
	);
}

function wmb_all_layouts() {
	return array(
		'text-only'  => __( 'Text Only', 'wmb' )
	) + wmb_featured_layouts();
}

function wmb_is_post_featured($post_id = null) {
	if (!$post_id) {
		global $post;
		$post_id = $post->ID;
	}

	$featured = array_keys(wmb_all_layouts());
	return has_term($featured, 'featured_layout', $post_id);
}

# Get the layout of a post
function wmb_get_post_layout($post_id = null) {
	if (!$post_id) {
		global $post;
		$post_id = $post->ID;
	}

	$layout = '';

	$layouts = get_the_terms($post_id, 'featured_layout');
	if ($layouts) {
		$layout = $layouts[0]->slug;
	}

	return $layout;
}