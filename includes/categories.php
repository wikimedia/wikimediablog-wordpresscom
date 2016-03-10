<?php

# The main categories
function wmb_main_categories() {
	return array(
		'community', 
		'wikipedia', 
		'foundation', 
		'technology',
	);
}

# Display the current category of a post
function wmb_the_post_category( $post_id = 0 ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = get_the_ID();
	}

	$category = false;

	$main_categories = wmb_main_categories();
	foreach ($main_categories as $category_slug) {
		if ( has_term( $category_slug, 'category', $post_id ) ) {
			$category = wpcom_vip_get_term_by( 'slug', $category_slug, 'category' );
			break;
		}
	}

	if ( ! $category ) {
		$post_categories = get_the_terms( $post_id, 'category' );
		if ( $post_categories ) {
			$category = array_shift( $post_categories );
		}
	}

	if ( ! $category ) {
		return;
	}

	$link = wpcom_vip_get_term_link( $category->term_id, 'category' );
	$title = apply_filters( 'the_title', $category->name );
	echo '<a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>';
}